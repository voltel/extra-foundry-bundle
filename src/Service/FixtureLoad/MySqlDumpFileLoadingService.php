<?php

namespace Voltel\ExtraFoundryBundle\Service\FixtureLoad;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class MySqlDumpFileLoadingService
    implements
    LoggerAwareInterface,
    LoadDumpFromDatabaseInterface
{
    use LoggerAwareTrait;

    /** @var string */
    private $mysqlDumpFilePath;

    /** @var ManagerRegistry */
    private $doctrineManagerRegistry;

    /**
     * Note: MySQL dump should contain "DROP TABLE" - "CREATE TABLE" queries
     * to get rid of existing data and indexes preventing insert
     */
    public function __construct(
        ManagerRegistry $doctrineManagerRegistry,
        string $_mysql_dump_file_path
    )
    {
        $this->doctrineManagerRegistry = $doctrineManagerRegistry;
        //
        $this->mysqlDumpFilePath = $_mysql_dump_file_path;
    }


    public static function getDatabaseType(): string
    {
        return LoadDumpFromDatabaseInterface::TYPE_MYSQL;
    }


    public function load(
        string $c_dump_file_name,
        string $c_connection_name = 'default'
    )
    {
        $n_microtime_start = microtime(true);

        $c_dump_file_path = $this->mysqlDumpFilePath . DIRECTORY_SEPARATOR . $c_dump_file_name;
        if (!file_exists($c_dump_file_path)) {
            $c_log_message = sprintf('MySQL dump file was not found at "%s" ', $c_dump_file_path);
            if ($this->logger) $this->logger->log('error', $c_log_message);
            throw new \Exception($c_log_message);
        }//endif

        $f_resource = fopen($c_dump_file_path, 'r'); // 'rt'
        if (!$f_resource) {
            $c_log_message = sprintf('voltel: Error while opening the file at "%s"', $c_dump_file_path);
            if ($this->logger) $this->logger->log('error', $c_log_message);
            throw new \RuntimeException($c_log_message);
        }//endif

        /** @var Connection $connection */
        $connection = $this->doctrineManagerRegistry->getConnection($c_connection_name);
        if (!$connection) {
            $c_log_message = sprintf('voltel: Error while getting a PDO to "%s" connection ', $c_connection_name);
            if ($this->logger) $this->logger->log('error', $c_log_message);
            throw new \RuntimeException($c_log_message);
        }//endif

        // Load dump file (dump tables, create tables, process insert queries)
        if ($this->logger) $this->logger->log('info', 'Loading test data...');

        $this->loadData($connection, $f_resource);

        $n_microtime_end = microtime(true);

        $f_execution_time = $n_microtime_end - $n_microtime_start;
        $c_log_message = sprintf('Initial state data loaded in "%.2f" sec', $f_execution_time);
        //if ($this->consoleLogger) $this->consoleLogger->log('info', $c_sql_stub);
        if ($this->logger) $this->logger->log('info', $c_log_message);


    }//end of function


    private function loadData(Connection $connection, $f_resource)
    {
        $connection->beginTransaction();

        try {
            $c_sql_statement = '';
            while (!feof($f_resource)) {
                $c_sql_statement_line = trim(fgets($f_resource));

                if (empty($c_sql_statement_line)) continue;
                if (0 === strpos($c_sql_statement_line, '--')) continue;

                // These are all important MySQL instructions which should not be skipped
                //if (0 === strpos($c_sql_statement_line, '/*!')) continue;
                //if (0 === strpos($c_sql_statement_line, 'LOCK TABLES')) continue;
                //if (0 === strpos($c_sql_statement_line, 'UNLOCK TABLES')) continue;


                if (false === strrpos($c_sql_statement_line, ';')) {
                    $c_sql_statement .= $c_sql_statement_line . ' ';
                    continue;
                } else {
                    $c_sql_statement .= $c_sql_statement_line;
                }//endif


                // logging to the terminal shortened INSERT statements
                if (0 === strpos($c_sql_statement_line, 'INSERT')) {
                    $c_sql_stub = strlen($c_sql_statement) <= 100 ? $c_sql_statement :
                        substr($c_sql_statement, 0, 70) . ' [...] ' .  substr($c_sql_statement, -30);
                    if ($this->logger) $this->logger->log('debug', $c_sql_stub);
                }//endif

                $connection->executeQuery($c_sql_statement);
                $c_sql_statement = '';
            }//endwhile

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();

            $c_log_message = 'Error while executing an SQL statement. Error message: ' . $e->getMessage();
            if ($this->logger) $this->logger->log('error', $c_log_message);
            throw new \RuntimeException($c_log_message, $e->getCode(), $e);

        } finally {
            fclose($f_resource);
        }//end try&catch

    }//end of function

}//end of class
