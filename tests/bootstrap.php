<?php

require dirname(__DIR__).'/vendor/autoload.php';

$c_xml_config = file_get_contents(__DIR__ . '/../phpunit.xml.dist');
$phpunit_xml_config = new SimpleXMLElement($c_xml_config);

foreach ($phpunit_xml_config->php->env as $this_env) {
    if (trim($this_env['name']) === 'DATABASE_URL') {
        //echo PHP_EOL . $this_env['name'] . ': ' . $this_env['value'];
        $_ENV['DATABASE_URL'] = (string) $this_env['value'];
        break;
    }//endif
}//endforeach

if (empty($_ENV['DATABASE_URL'])) {
    throw new \LogicException(sprintf('Expected to find environmental variable "DATABASE_URL" defined in phpunit.xml.dist configuration file under the path of phpunit:php:env.'));
}//endif

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';


// Read at: https://github.com/zenstruck/foundry#global-state
// Note: The ResetDatabase trait is required when using global state.
//
//Zenstruck\Foundry\Test\TestState::addGlobalState(function () {
//    // GlobalStory::load();
//});
