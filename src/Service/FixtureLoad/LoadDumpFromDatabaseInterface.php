<?php

namespace Voltel\ExtraFoundryBundle\Service\FixtureLoad;

interface LoadDumpFromDatabaseInterface
{
    const TYPE_MYSQL = 'mysql';

    public static function getDatabaseType(): string;

    public function load(string $dumpFileName, string $ConnectionName);
}