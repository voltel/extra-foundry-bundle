<?php

namespace Voltel\ExtraFoundryBundle\Foundry\Story;

use Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Zenstruck\Foundry\Story;

/**
 * Note: this class has one (1) method with "@required" annotation
 */
abstract class AbstractStory extends Story
    implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var EntityProxyPersistService */
    protected $persistService;

    /**
     * @required
     */
    public function injectEntityProxyPersistService(
        EntityProxyPersistService $entityProxyPersistService
    )
    {
        $this->persistService = $entityProxyPersistService;
    }


}//end of class
