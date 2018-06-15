<?php

namespace SuiteCRM\database;

use Pixie\Exception;

require_once 'vendor/autoload.php';

class SuitePixie extends \Pixie\QueryBuilder\QueryBuilderHandler
{
    private static $_connection = null;

    /**
     * Makes a new SuitePixie query builder handler instance.
     *
     * If no configuration is passed, it attempts to load them automatically from the default config.
     *
     * @param array|null $config
     * @return SuitePixie
     */
    public static function make($config = null)
    {
        self::makeConnection($config);

        try {
            return new self();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Failed to load SuitePixie. " . $e->getMessage());
            die;
        }
    }

    /**
     * Makes a new connection to the database given the proper configuration array.
     * @param $config array|null an array containing the configuration. See https://github.com/usmanhalalit/pixie#connection
     */
    private static function makeConnection($config = null)
    {
        if (self::$_connection !== null)
            return;

        if ($config === null)
            $config = self::loadConfig();

        self::$_connection = new \Pixie\Connection($config['driver'], $config, 'QB');
    }

    /**
     * Attempts to load the default configurations from the $sugar_config global.
     *
     * Dies on failure.
     *
     * @return array the configuration array that can be used to make a new connection.
     */
    private static function loadConfig()
    {
        global $sugar_config;

        if (!isset($sugar_config) || empty($sugar_config)) {
            $GLOBALS['log']->fatal("Unable to load default config");
            die;
        }

        $config = array(
            'driver' => $sugar_config['dbconfig']['db_type'],
            'host' => $sugar_config['dbconfig']['db_host_name'],
            'database' => $sugar_config['dbconfig']['db_name'],
            'username' => $sugar_config['dbconfig']['db_user_name'],
            'password' => $sugar_config['dbconfig']['db_password']
        );

        return $config;
    }

    /**
     * Returns the bean table. Can be used to perform further queries.
     *
     * @param $bean \SugarBean
     * @return SuitePixie
     */
    public function bean($bean)
    {
        return $this->table($bean->table_name);
    }
}