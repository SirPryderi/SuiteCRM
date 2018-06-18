<?php
/**
 * Created by PhpStorm.
 * User: viocolano
 * Date: 15/06/18
 * Time: 12:06
 */

use SuiteCRM\database\SuitePixie;

class SuitePixieTest extends \SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function setUp()
    {
        parent::setUp();
        if (!defined('sugarEntry')) {
            define('sugarEntry', true);
        }

        global /** @noinspection PhpUnusedLocalVariableInspection */
        $app_strings, $mod_strings;

        include_once __DIR__ . '/../../../../include/SugarObjects/SugarConfig.php';
        include_once __DIR__ . '/../../../../include/database/DBManagerFactory.php';
        include_once __DIR__ . '/../../../../include/SugarLogger/LoggerManager.php';
    }

    public function testFactory()
    {
        $builder = SuitePixie::make();

        self::assertNotNull($builder, "Builder is null!");

        unset($builder);
    }

    public function testRetrieveFromDatabaseManager()
    {
        $builder = DBManager::builder();

        self::assertNotNull($builder, "Builder is null!");
    }

    public function testBeanGet()
    {
        $bean = BeanFactory::getBean('Accounts');

        $results = DBManager::builder()->bean($bean)->get();

        self::assertNotNull($results, "Failed to perform select query.");
    }

    public function testMultiple()
    {
        $one = DBManager::builder()->table('accounts');
        $two = DBManager::builder()->table('alerts');

        self::assertNotEquals($one, $two);
    }

    public function testBasicUsage()
    {
        $accountName = 'TestAccount' . uniqid();

        $data = [
            'id' => uniqid(),
            'name' => $accountName,
        ];

        DBManager::builder()->table('accounts')->insert($data);

        $result = DBManager::builder()->table('accounts')->where('name', $accountName)->get();

        self::assertNotNull($result, "No results for a newly entered user!");

        DBManager::builder()->table('accounts')->where('name', $accountName)->delete();
    }

    public function testCheckEnabled()
    {
        $category = 'test' . uniqid();

        DBManager::builder()->table('config')->insert([
            ['category' => $category, 'name' => 'module_test_enabled', 'value' => 1],
            ['category' => $category, 'name' => 'module_test_disabled', 'value' => 0]
        ]);

        self::assertTrue(DBManager::isConfigEnabled('module_test_enabled'));
        self::assertFalse(DBManager::isConfigEnabled('module_test_disabled'));
        self::assertFalse(DBManager::isConfigEnabled('module_test_not_there'));

        // Makes sure that the methods used above work specifying a category
        self::assertTrue(DBManager::isConfigEnabled('module_test_enabled', $category));
        self::assertFalse(DBManager::isConfigEnabled('module_test_enabled', 'nonExistingCategory' . uniqid()));
        self::assertFalse(DBManager::isConfigEnabled('module_test_disabled', $category));
        self::assertFalse(DBManager::isConfigEnabled('module_test_not_there', $category));

        // Removes the inserted test rows
        DBManager::builder()->table('config')->where('category', $category)->delete();
    }
}
