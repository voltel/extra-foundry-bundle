<?php


namespace Voltel\ExtraFoundryBundle\Service\FixtureEntity;


use Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

class ProxyJar
{
    /** @var Factory|null */
    private $factory;

    /** @var Proxy[]|array */
    private $proxies = [];

    /** @var string */
    private $entityFqcn;


    /**
     * @param Factory $factory
     */
    public function __construct(
        ?Factory $factory = null
    )
    {
        if (!empty($factory)) {
            $this->factory = $factory;

            if ($factory instanceof AbstractFactory) {
                $this->entityFqcn = $factory::getClassName();
            } elseif ($factory instanceof ModelFactory) {
                $this->entityFqcn = $factory::repository()->getClassName();
            }//endif

        }//endif
    }//end of function


    public function addProxy(Proxy $proxy)
    {
        if (empty($this->entityFqcn)) {
            $this->entityFqcn = get_class($proxy->object());
        }//endif

        $this->validateProxy($proxy);
        $this->proxies[] = $proxy;
    }//end of function


    public function addProxyBatch(array $a_proxies)
    {
        if (empty($this->entityFqcn)) {
            /** @var Proxy $first_proxy */
            $first_proxy = $a_proxies[array_key_first($a_proxies)];
            $this->entityFqcn = get_class($first_proxy->object());
        }//endif

        $this->validateProxies($a_proxies);
        $this->proxies = array_merge($this->proxies, $a_proxies);
    }//end of function


    private function validateProxies(array $a_proxies)
    {
        /** @var Proxy $this_proxy */
        foreach ($a_proxies as $this_proxy) {
            $this->validateProxy($this_proxy);
        }//end foreach
    }//end of function


    private function validateProxy(Proxy $proxy)
    {
        if (get_class($proxy->object()) !== $this->entityFqcn) {
            throw new \LogicException(sprintf('The model Factory of class "%s" does not create entities of class "%s"',
                get_class($this->factory), $this->entityFqcn));
        }//endif
    }//end of function


    /**
     * @return Factory|null
     */
    public function getFactory(): ?Factory
    {
        return $this->factory;
    }


    /**
     * @return array
     */
    public function getProxies(): array
    {
        return $this->proxies;
    }


    /**
     * @return string
     */
    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }//end function


}//end of class
