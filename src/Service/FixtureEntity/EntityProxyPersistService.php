<?php /** @noinspection PhpDocMissingThrowsInspection */

namespace Voltel\ExtraFoundryBundle\Service\FixtureEntity;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Proxy;

/**
 * Note: This service, is injected into "Voltel\ExtraFoundryBundle\Foundry\Story\AbstractStory" class
 *  with "@required" annotation
 */
class EntityProxyPersistService
{
    private static $critical_proxy_entities_count = 10000;

    /** @var bool */
    private static $quiet = false;

    /** @var bool */
    private static $verbose = false;

    /** @var bool */
    private static $veryVerbose = false;

    /** @var ProxyJarCollection */
    private $proxyJars;

    /** @var ServiceLocator */
    private $factoryLocator;

    /** @var array */
    private $lastTimingData = [];

    /** @var ManagerRegistry */
    protected $doctrineManagerRegistry;


    /**
     * EntityProxyPersistService constructor.
     *
     * Service locator may be injected with "bind" in services.yaml which is defined as follows:
     * $_foundry_factory_locator: !tagged_locator { tag: foundry.factory, default_index_method: 'getClassName' }
     * For this to work, the Factory method should extend a custom AbstractFactory
     * with public method "getClassName()",
     * but this is not obligatory since if the Factory doesn't have this method,
     * the FQCN of the Factory will be used as an indexed-by key in the ServiceLocator.
     */
    public function __construct(
        ManagerRegistry $doctrineManagerRegistry,
        ServiceLocator $_foundry_factory_locator
    )
    {
        $this->doctrineManagerRegistry = $doctrineManagerRegistry;
        // See notes above in PhpDoc
        $this->factoryLocator = $_foundry_factory_locator;

        $this->initialize();
    }


    private function initialize()
    {
        // Initialize collection of ProxyJar objects
        $this->proxyJars = new ProxyJarCollection();

        // I guess, usual logger would be more appropriate, but...
        foreach ($_SERVER['argv'] as $c_this_arg) {
            if ($c_this_arg === '--quiet') self::$quiet = true;

            if ($c_this_arg == '-v') self::$verbose = true;

            if ($c_this_arg == '-vv' || $c_this_arg == '-vvv') {
                self::$verbose = true;
                self::$veryVerbose = true;
            }//endif
        }//endforeach
    }//end of function


    /**
     * Returns a new Entity (not Proxy), created by Factory in argument one,
     * with attributes in argument two (an array or a callable).
     * The factory may optionally accept some states, which may be provided in an array of strings in argument four.
     */
    public function createOne(
        Factory $factory,
        $a_attributes = [],
        array $a_states = []
    ): object
    {
        $factory = $this->getFactoryWithStates($factory, $a_states);

        $proxy = $factory->create($a_attributes);

        $this->addProxy($proxy, $factory);

        return $proxy->object();
    }//end of function


    /**
     * Returns a batch (an array) of new Entities (not Proxies), created by Factory in argument one,
     * in the quantity from argument two
     * with attributes in argument three (an array or a callable).
     *
     * In argument four (an array or a callable), the factory may optionally accept an array of state names.
     * If argument four is a callback, the function must return an array of states,
     * and it will be invoked for each entity individually.
     */
    public function createMany(
        Factory $factory,
        int $n_count,
        $mix_attributues = [],
        $mix_states = []
    ): array
    {
        if ($n_count <= 0) {
            throw new \InvalidArgumentException(sprintf('Expected argument two ("n_count") to be greated than zero (1 or more).'));
        }//endif

        $a_entities = [];

        // If last argument for states is a callable: create entities one by one with the callable run for each entity.
        if (is_callable($mix_states)) {
            for ($i = 0; $i < $n_count; $i++) {
                $a_states = call_user_func($mix_states);
                // Sanity check: the callback must return an array of strings with state names
                if (!is_array($a_states)) {
                    throw new \LogicException('Expected callable in argument 4 to return an array of strings with state names.');
                }//endif

                $a_entities[] = $this->createOne($factory, $mix_attributues, $a_states);
            }//endfor

        // If last argument for states is an array: create a batch of entities all at once
        } elseif (is_array($a_states = $mix_states)) {

            $factory = $this->getFactoryWithStates($factory, $a_states);

            $proxyBatch = $factory->many($n_count)->create($mix_attributues);

            $this->addProxyBatch($proxyBatch, $factory);

            // Prepare a batch of entities for which the proxies were just created
            foreach ($proxyBatch as $this_proxy) {
                $a_entities[] = $this_proxy->object();
            }//endforeach

        } else {
            throw new \LogicException('Expected in argument 4 either a callable or an array of strings with state names.');
        }//endif

        return $a_entities;
    }//end of function


    /**
     * Applies states from argument two to the model factory from the argument one.
     * Note: factories will be configured to not immediately persist.
     */
    private function getFactoryWithStates(
        Factory $factory,
        array $a_states = []
    ): Factory
    {
        if (count($a_states) > 0) {
            foreach ($a_states as $mix_key => $mix_value) {
                if (is_numeric($mix_key)) {
                    $c_this_state = $mix_value;
                    $factory = $factory->{$c_this_state}();

                } else {
                    $c_this_state = $mix_key;
                    $factory = call_user_func_array([$factory, $c_this_state], (array) $mix_value);
                }//endif
                //$factory = call_user_func([$factory, $c_this_state]);
            }
        }//endif

        // NB! Factory will be automatically configured to not immediately persist
        //  i.e. "withoutPersisting()" is invoked
        $factory = $factory->withoutPersisting();

        return $factory;
    }//end of function


    /**
     * @internal
     */
    protected function addProxy(Proxy $entityProxy, Factory $factory = null)
    {
        $this->proxyJars->addProxy($entityProxy, $factory);
    }//end of function


    /**
     * @internal
     */
    protected function addProxyBatch(array $a_entity_proxy_batch, Factory $factory = null)
    {
        $this->proxyJars->addProxyBatch($a_entity_proxy_batch, $factory);
    }//end of function


    /**
     * @return array<string, array<Proxy>>
     */
    public function persistAndFlushAll(bool $l_clear_entity_managers = false): array
    {
        $f_time_start = microtime(true);

        $this->persistAll();

        $this->flushAll(true, $l_clear_entity_managers);

        $this->proxyJars->clear();

        $f_time_end = microtime(true);

        $this->addTimingResults(['total' => $f_time_end - $f_time_start]);

        $this->reportResults();

        return $this->proxyJars->getProxyBatchGroupedByFqcn();
    }//end of function


    /**
     * @param bool $l_mark_proxies_as_persisted
     * @throws \ReflectionException
     */
    protected function persistAll(bool $l_mark_proxies_as_persisted = false)
    {
        $f_timestamp_start = microtime(true);

        if ($l_mark_proxies_as_persisted) {
            $reflectionProperty = new \ReflectionProperty(Proxy::class, 'persisted');
            $reflectionProperty->setAccessible(true);
        }//endif

        foreach ($this->proxyJars->getEntityFqcnArrayForAllEntityProxies() as $c_fqcn) {
            /** @var ProxyJar[] $a_proxy_jar_batch */
            $a_proxy_jar_batch = $this->proxyJars->getProxyJarBatchForEntityFqcn($c_fqcn);
            foreach ($a_proxy_jar_batch as $oThisProxyJar) {
                //$this_factory = $oThisProxyJar->getFactory();
                $a_proxies = $oThisProxyJar->getProxies();

                $em = $this->doctrineManagerRegistry->getManagerForClass($c_fqcn);
                if (null === $em) {
                    throw new \LogicException(sprintf('Expected to locate EntityManager in Doctrine Manager Registry for class "%s", got null', $c_fqcn));
                }//endif

                //$this->ems[] = $em;

                /** @var Proxy $oThisEntityProxy */
                foreach ($a_proxies as $oThisEntityProxy) {
                    $entity = $oThisEntityProxy->object();
                    $em->persist($entity);

                    // Mark proxy as persisted
                    if ($l_mark_proxies_as_persisted) {
                        $reflectionProperty->setValue($oThisEntityProxy, true);
                    }//endif
                }//endforeach

            }//end of foreach

        }//endforeach

        $f_timestamp_end = microtime(true);

        $this->addTimingResults(['persist' => $f_timestamp_end - $f_timestamp_start]);
    }//end of function


    /**
     * Flushes all entity managers and returns a nested array of flushed entities per entity class.
     * By default, checks if all the entities in proxies are persisted and if not, throws an exception.
     * The check for non-persisted entities may be skipped with the boolean parameter set to "true".
     *
     * @param bool $l_skip_check_if_proxies_persisted
     * @throws \ReflectionException
     */
    protected function flushAll(
        bool $l_skip_check_if_proxies_persisted = false,
        bool $l_clear_entity_managers = false
    )
    {
        $f_timestamp_start = microtime(true);
        $ems = [];

        foreach ($this->proxyJars->getEntityFqcnArrayForAllEntityProxies() as $c_fqcn) {
            $a_entity_proxies = $this->proxyJars->getProxyBatchForEntityFqcn($c_fqcn);

            $em = $this->doctrineManagerRegistry->getManagerForClass($c_fqcn);
            $ems[] = $em;

            if (!$l_skip_check_if_proxies_persisted) {
                foreach ($a_entity_proxies as $oThisEntityProxy) {
                    if (!$oThisEntityProxy->isPersisted()) {
                        throw new \LogicException(sprintf('Did you forget to persist entities of class %s', $c_fqcn));
                    }//endif
                }//endforeach
            }//endif

            $em->flush();
        }//endforeach

        $f_timestamp_flushed = microtime(true);

        // See, if any of the entity factories defines an "afterPersist" callback in its initialize method.
        // If so, invoke "afterPersist" callbacks and flush entities again to store change (update of previously persisted entities).
        $l_any_after_persist_callbacks = false;
        //
        foreach ($this->proxyJars->getProxyJars() as $oThisProxyJar) {
            $c_fqcn = $oThisProxyJar->getEntityFqcn();
            $a_entity_proxies = $oThisProxyJar->getProxies();
            $factory = $oThisProxyJar->getFactory();

            // Try to Locate Factory class t hat creates the entity with this FQCN
            if (!is_null($factory)) {
                $reflectionProperty = new \ReflectionProperty(Factory::class, 'afterPersist');
                $reflectionProperty->setAccessible(true); // "afterPersist" property is private
                $a_after_persist_callbacks = $reflectionProperty->getValue($factory);

            } else {
                $factory_service = $this->getFactoryForEntityOfClass($c_fqcn);
                if (empty($factory_service)) {
                    $c_warning_message = sprintf('Warning: Failed to locate zenstruck/foundry model factory (tagged with "foundry.factory") for class "%s". Ignore only if you used an anonymous factory.', $c_fqcn);
                    //throw new \LogicException($c_warning_message);
                    $this->echoResult($c_warning_message);
                    continue;
                }//endif

                // Try to see if the factory for the entity class has any "afterPersist" methods.
                $reflectionMethod = new \ReflectionMethod(get_class($factory_service), 'initialize');
                $reflectionMethod->setAccessible(true); // "initialize" method is protected
                $factory = $reflectionMethod->invoke($factory_service); // it clones the factory adding "afterPersist" callbacks, if any

                $reflectionProperty = new \ReflectionProperty(Factory::class, 'afterPersist'); // private method on Factory class
                $reflectionProperty->setAccessible(true); // "afterPersist" property is private
                $a_after_persist_callbacks = $reflectionProperty->getValue($factory);
            }//endif


            // If there are any "afterPersist" callbacks defined on the factory, execute them
            if (!empty($a_after_persist_callbacks)) {
                $l_any_after_persist_callbacks = true;
                /** @var Proxy $oThisEntityProxy */
                foreach ($a_entity_proxies as $oThisEntityProxy) {

                    // See Factory::create method for reference
                    $oThisEntityProxy->withoutAutoRefresh(function(Proxy $oThisEntityProxy) use ($a_after_persist_callbacks) {
                        foreach ($a_after_persist_callbacks as $callback) {
                            // NB! There is no way we can identify attributes
                            //  that were used during the "Factory::create()" invocation, so ignore them
                            $oThisEntityProxy->executeCallback($callback/*, $attributes*/);
                        }//endforeach
                    });
                }//endforeach
            }//endif
        }//endforeach

        foreach ($ems as $em) {
            // Flush again, should any of the persisted above entities were modified in "afterPersist" callbacks.
            if ($l_any_after_persist_callbacks) $em->flush();

            // clear entity manager, if it was required
            if ($l_clear_entity_managers) $em->clear();
        }//endforeach

        $f_timestamp_end = microtime(true);

        $this->addTimingResults([
            'flush' => $f_timestamp_flushed - $f_timestamp_start,
            'callbacks' => $f_timestamp_end - $f_timestamp_flushed,
            'flush+callbacks' => $f_timestamp_end - $f_timestamp_start,
        ]);
    }//end of function


    /**
     * @param string $c_fqcn
     * @return Factory|null
     * @throws \ReflectionException
     */
    private function getFactoryForEntityOfClass(string $c_fqcn): ?Factory
    {
        // Locate Factory that creates the entity with this FQCN
        if ($this->factoryLocator->has($c_fqcn)) {
            // if Factory service extends from AbstractFactory, it will have public method "getClassName"
            // that will be used as key for the service in the container
            // (see services.yaml, parameter "default_index_method" in "!tagged_locator" used for dependency injection.
            return $this->factoryLocator->get($c_fqcn);

        } else {
            // both key and value in the returned array are fqcn of the zendstruck model factory class
            foreach ($this->factoryLocator->getProvidedServices() as $c_key => $c_factory_fqcn) {
                // This would not be necessary if "getClass" method were "public", but it is "protected"
                $oReflectionMethod = new \ReflectionMethod($c_factory_fqcn, 'getClass');
                $oReflectionMethod->setAccessible(true);
                $c_entity_class_name = $oReflectionMethod->invoke(null);

                if ($c_entity_class_name === $c_fqcn) {
                    return $this->factoryLocator->get($c_key);
                }//endif
            }//end
        }//endif

        return null;
    }//end of function


    /**
     * Prints in the console a warning message if current count of entity proxies to be persisted/flushed
     * is more than the class variable value
     *
     * @param string|null $c_additional_message
     */
    public function checkCriticalProxyCount(string $c_additional_message = null)
    {
        $n_current_proxy_count = $this->proxyJars->getProxyCount();

        if ($n_current_proxy_count >= self::$critical_proxy_entities_count) {
            $this->echoResult(sprintf('[!] Warning! Number of entity proxies (%s) to persist/flush is over maximal optimal (%s).',
                $n_current_proxy_count, self::$critical_proxy_entities_count));

            if ($c_additional_message) {
                $this->echoResult($c_additional_message);
            }//endif
        }//endif

    }//end of function


    /**
     * @param string $c_fqcn
     * @return int
     */
    public function getProxyCountForClass(string $c_fqcn) : int
    {
        return $this->proxyJars->getProxyCount($c_fqcn);
    }//end of function


    /**
     * @param string $c_fqcn
     * @return array|null
     */
//    public function getProxyBatchForClass(string $c_fqcn) : ?array
//    {
//        return $this->proxyJars->getProxyBatchForEntityFqcn($c_fqcn);
//    }//end of function


    /**
     * Returns "true" if the array of entities for persistence/flush is not empty
     *
     * @return bool
     */
    public function isFlushRequired() : bool
    {
        return $this->proxyJars->getProxyCount() > 0;
    }


    /**
     * @return array
     */
    public function getLastTimingData() : array
    {
        return $this->lastTimingData;
    }


    /**
     * @param array<string, float> $a_results
     */
    private function addTimingResults(array $a_results)
    {
        $this->lastTimingData = array_merge($this->lastTimingData, $a_results);
    }


    private function reportResults()
    {
        foreach ($this->proxyJars->getEntityFqcnArrayForAllEntityProxies() as $c_this_fqcn) {
            $n_entity_count = $this->proxyJars->getProxyCount($c_this_fqcn);
            $this->echoResult(sprintf('%s %s entities ("%s") have been persisted and flushed. ',
                $n_entity_count, basename($c_this_fqcn), $c_this_fqcn));
        }//endforeach

        $this->echoTiming();
    }//endn of function


    /**
     * Prints/echos results in the terminal. If it's a quiet mode won't print anything.
     * May optionally print total time of the last operation in "-v" verbose mode.
     * If verbosity level is "-vv" or "-vvv", the timing information will be detailed.
     */
    private function echoResult(string $c_result)
    {
        if (self::$quiet) return;

        if (!empty($c_result)) echo PHP_EOL . $c_result;
    }//end of function


    private function echoTiming()
    {
        if (self::$verbose) {
            echo PHP_EOL . sprintf('    Operation time is %.2f  sec. ', $this->lastTimingData['total']);
        }//endif

        if (self::$veryVerbose) {
            var_export($this->lastTimingData);
            echo PHP_EOL . '=============';
        }//endif
    }//end of function


}//end of class
