<?php

namespace Voltel\ExtraFoundryBundle\Service\FixtureLoad;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Voltel\ExtraFoundryBundle\Service\FixtureLoad\LoadDumpFromDatabaseInterface;

class SqlDumpLoaderService
{
    /** @var LoadDumpFromDatabaseInterface */
    private $databaseDumpFileLoaderService;

    private $dumpFileName;
    private string $connectionName;

    public function __construct(
        ServiceLocator $databaseLoaderServiceLocator,
        string $_database_type, // "mysql"
        string $_dump_file_name,
        string $c_connection_name = 'default'
    )
    {
        // uncomment for debugging
        //foreach ($databaseLoaderServiceLocator->getProvidedServices() as $c_key => $value) {
            //echo PHP_EOL . ($c_key . ': ' . (string)$value);
        //}

        if ($databaseLoaderServiceLocator->has($_database_type)) {
            /** @var LoadDumpFromDatabaseInterface dumpFileLoadingService */
            $this->databaseDumpFileLoaderService = $databaseLoaderServiceLocator->get($_database_type);
        } else {
            throw new \LogicException(sprintf('Failed to locate database loader service (implements interface "%s") for database type "%s".',
                LoadDumpFromDatabaseInterface::class, $_database_type));
        }//endif

        $this->dumpFileName = $_dump_file_name;
        $this->connectionName = $c_connection_name;
    }

    /**
     * @throws \Exception
     */
    public function loadSqlDump(): void
    {
        $this->databaseDumpFileLoaderService->load($this->dumpFileName, $this->connectionName);
    }
}
