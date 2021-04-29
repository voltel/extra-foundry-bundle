<?php


namespace Voltel\ExtraFoundryBundle\Service\FixtureEntity;


use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Proxy;

class ProxyJarCollection
{
    /** @var ProxyJar[]|array */
    private $proxyJars = [];


    /**
     * Empties the collection of ProxyJar entities
     */
    public function clear()
    {
        $this->proxyJars = [];
    }


    /**
     * @return ProxyJar[]
     */
    public function getProxyJars(): array
    {
        return $this->proxyJars;
    }//end of function


    /**
     * Returns an array of arrays, where outer array keys are FQCN of the entities in corresponding inner array
     * @return array<string, array>
     */
    public function getProxyBatchGroupedByFqcn(): array
    {
        $a_entity_proxies = [];

        foreach ($this->getEntityFqcnArrayForAllEntityProxies() as $c_this_fqcn) {
            $a_entity_proxies[$c_this_fqcn] = $this->getProxyBatchForEntityFqcn($c_this_fqcn);
        }//end foreach

        return $a_entity_proxies;
    }//end of function


    /**
     * Returns a count of all proxies in all ProxyJar entities in this collection
     * @return int
     */
    public function getProxyCount(?string $c_fqcn = null): int
    {
        $n_count = 0;

        foreach ($this->proxyJars as $this_proxy_jar) {
            if (!empty($c_fqcn) && $this_proxy_jar->getEntityFqcn() !== $c_fqcn) {
                continue;
            }//endif

            $n_count += count($this_proxy_jar->getProxies());
        }//endforeach

        return $n_count;
    }//end of function


    /**
     * Returns an array of strings with FQCN of entities from all ProxyJar entities in the collection
     */
    public function getEntityFqcnArrayForAllEntityProxies(): array
    {
        $a_fqcn_array = [];

        /** @var ProxyJar $this_jar */
        foreach ($this->proxyJars as $this_jar) {
            $a_fqcn_array[] = $this_jar->getEntityFqcn();
        }//end foreach

        return array_unique($a_fqcn_array);
    }//end of function


    /**
     * Returns an array of ProxyJar objects that hold proxies of the entities of the class in the parameter
     * @returns ProxyJar[]|array
     */
    public function getProxyJarBatchForEntityFqcn(string $c_fqcn): array
    {
        $a_proxy_jars = [];

        /** @var ProxyJar $this_jar */
        foreach ($this->proxyJars as $this_jar) {
            $c_this_fqcn = $this_jar->getEntityFqcn();
            if ($c_this_fqcn === $c_fqcn) {
                $a_proxy_jars[] = $this_jar;
            }//endif
        }//end foreach

        return $a_proxy_jars;
    }//end of function


    /**
     * Returns an batch of proxies for entities of the class in the parameter
     */
    public function getProxyBatchForEntityFqcn(string $c_fqcn): array
    {
        $a_proxy_batch = [];

        /** @var ProxyJar $this_jar */
        foreach ($this->proxyJars as $this_jar) {
            $c_this_fqcn = $this_jar->getEntityFqcn();
            if ($c_this_fqcn === $c_fqcn) {
                $a_proxy_batch = array_merge($a_proxy_batch, $this_jar->getProxies());
            }//endif
        }//end foreach

        return $a_proxy_batch;
    }//end of function


    /**
     * Registers one proxy in a suitable ProxyJar (determined or created)
     */
    public function addProxy(Proxy $proxy, Factory $factory = null)
    {
        if (!empty($factory)) {
            $proxyJar = $this->getProxyJarForFactory($factory);
            $proxyJar->addProxy($proxy);

            return;
        }//endif

        $proxyJar = $this->getProxyJarWithoutFactoryForEntityFqcn(get_class($proxy->object()));
        $proxyJar->addProxy($proxy);
    }//end of function


    /**
     * Registers a batch of proxies in a suitable ProxyJar (determined or created)
     */
    public function addProxyBatch(array $a_proxies, Factory $factory = null)
    {
        if (empty($a_proxies)) return;

        if (!empty($factory)) {
            $proxyJar = $this->getProxyJarForFactory($factory);
            $proxyJar->addProxyBatch($a_proxies);

            return;
        }//endif

        $first_proxy = $a_proxies[array_key_first($a_proxies)];
        $proxyJar = $this->getProxyJarWithoutFactoryForEntityFqcn(get_class($first_proxy->object()));
        $proxyJar->addProxyBatch($a_proxies);
    }//end of function


    /**
     * Returns a ProxyJar that for the same Factory as provided in the parameter.
     * If no matching jar found, a new one is created, added to collection, and returned
     */
    private function getProxyJarForFactory(Factory $factory): ProxyJar
    {
        $c_factory_hash = spl_object_hash($factory);
        //
        if (array_key_exists($c_factory_hash, $this->proxyJars)) {
            return $this->proxyJars[$c_factory_hash];
        }//endif

        /** @var ProxyJar $this_jar */
        foreach ($this->proxyJars as $this_jar) {
            if ($this_jar->getFactory() === $factory) {
                return $this_jar;
            }//endif
        }//endforeach

        $proxy_jar = new ProxyJar($factory);
        $this->addJarToCollection($proxy_jar);

        return $proxy_jar;
    }//end of function


    /**
     * Returns a ProxyJar that has no Factory set, but has some proxies in it for the same entity FQCN.
     * If no matching jar found, a new one is created, added to collection, and returned
     */
    private function getProxyJarWithoutFactoryForEntityFqcn(string $c_fqcn): ProxyJar
    {
        /** @var ProxyJar $this_jar */
        foreach ($this->proxyJars as $this_jar) {
            if (!is_null($this_jar->getFactory())) continue;

            if ($this_jar->getEntityFqcn() === $c_fqcn) {
                return $this_jar;
            }//endif
        }//endforeach

        $proxy_jar = new ProxyJar();
        $this->addJarToCollection($proxy_jar);

        return $proxy_jar;
    }//end of function


    /**
     * Registers a ProxyJar in the Collection.
     * Hash of the factory object (registered with the jar) can be used for fast jar retrieval
     */
    private function addJarToCollection(ProxyJar $proxyJar)
    {
        $factory = $proxyJar->getFactory();

        if (!is_null($factory)) {
            $key = spl_object_hash($factory);
            $this->proxyJars[$key] = $proxyJar;
        } else {
            $this->proxyJars[] = $proxyJar;
        }//endif
    }//end of function


}//end of class
