<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Voltel\ExtraFoundryBundle\Tests\Setup\Kernel\VoltelExtraFoundryTestingKernel;

// project bootstrap
require_once 'tests/bootstrap.php';

// mechanism to retrieve EntityManager in your app
$kernel = new VoltelExtraFoundryTestingKernel();
$kernel->boot();

/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

return ConsoleRunner::createHelperSet($entityManager);