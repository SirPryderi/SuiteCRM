<?php
/**
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

/**
 * Created by PhpStorm.
 * User: viocolano
 * Date: 20/07/18
 * Time: 10:33
 */

namespace SuiteCRM\Robo\Plugin\Commands;

use BeanFactory;
use DBManagerFactory;
use Faker\Factory;
use Faker\Generator;
use Person;
use SuiteCRM\Robo\Traits\CliRunnerTrait;
use SuiteCRM\Robo\Traits\RoboTrait;
use User;

class RandomizerCommands extends \Robo\Tasks
{
    use RoboTrait;
    use CliRunnerTrait;

    /** @var array */
    protected $box;
    /** @var Generator */
    private $faker;
    /** @var User */
    private $user;

    /**
     * Randomizer constructor.
     */
    const ID_PREFIX = 'randomizer.';

    public function __construct()
    {
        $this->bootstrap();
        $this->faker = Factory::create();
        $this->user = BeanFactory::getBean('Users', '1');
        $this->box['Users'][] = $this->user;
        echo "Running as {$this->user->user_name}", PHP_EOL;
    }

    public function randomizeAll($sizeBig = 200, $sizeSmall = 50, $sizeTiny = 10)
    {
        $this->randomizeUsers($sizeTiny);
        $this->randomizeAccounts($sizeBig);
        $this->randomizeContacts($sizeBig);
        $this->randomizeTargetLists($sizeSmall);
    }

    public function randomizeUsers($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var User $bean */
            $bean = BeanFactory::newBean('Users');

            $this->fakePerson($bean);

            $bean->user_name = $this->faker->userName;
            $bean->department = $this->randomDepartment();
            $bean->employee_status = $this->randomEmployeeStatus();

            $bean->reports_to_id = $this->randomUserId();

            $this->saveBean($bean);
        }
    }

    public function randomizeTargetLists($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \ProspectList $bean */
            $bean = BeanFactory::newBean('ProspectLists');

            $bean->name = "Target List #$i";
            $bean->list_type = $this->faker->randomElement([
                'default',
                'seed',
                'exempt_domain',
                'exempt_address',
                'exempt',
                'test',
            ]);
            $bean->description = $this->faker->text;

            $this->saveBean($bean);

            $table = $bean->rel_prospects_table;
            $sql = "INSERT INTO $table (id, prospect_list_id, related_id, related_type) VALUES ";

            $potentialTargets = $this->box['Contacts'];
            $count = $this->faker->numberBetween(0, count($potentialTargets));
            $targets = $this->faker->randomElements($potentialTargets, $count);
            echo "Adding $count targets", PHP_EOL;

            foreach ($targets as $target) {
                $rowId = $this->getUUID();
                $sql .= " ('$rowId', '$bean->id', '$target->id', '$target->table_name'),";
            }

            DBManagerFactory
                ::getInstance()
                ->query(trim($sql, ' ,'));
        }
    }

    /**
     * @param $bean Person
     */
    private function fakePerson($bean)
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        $bean->first_name = $this->faker->firstName($gender);
        $bean->last_name = $this->faker->lastName;
        $bean->salutation = $this->faker->title($gender);
        $bean->title = $this->randomTitle();

        $bean->phone_work = $this->faker->phoneNumber;
        $bean->phone_other = $this->faker->phoneNumber;

        $bean->primary_address_street = $this->faker->streetAddress;
        $bean->primary_address_city = $this->faker->city;
        $bean->primary_address_state = $this->faker->state;
        $bean->primary_address_postalcode = $this->faker->postcode;
        $bean->primary_address_country = $this->faker->country;

        if ($this->faker->boolean) { // has alt address
            $bean->alt_address_street = $this->faker->streetAddress;
            $bean->alt_address_city = $this->faker->city;
            $bean->alt_address_state = $this->faker->state;
            $bean->alt_address_postalcode = $this->faker->postcode;
            $bean->alt_address_country = $this->faker->country;
        }

        $bean->email1 = $this->faker->email;
    }

    /**
     * @return string
     */
    private function randomTitle()
    {
        return $this->faker->randomElement(
            [
                'Manager',
                'Head of Department',
                'Director',
                'Assistant',
                'Intern'
            ]
        );
    }

    /**
     * @return string
     */
    private function randomDepartment()
    {
        return $this->faker->randomElement(['Marketing', 'Sales', 'Management', 'Production']);
    }

    /**
     * @return string
     */
    private function randomEmployeeStatus()
    {
        return $this->faker->randomElement(['Active', 'Terminated', 'Leave of Absence']);
    }

    /**
     * @return string
     */
    private function randomUserId()
    {
        return $this->random('Users', $this->user)->id;
    }

    /**
     * @param $module
     * @param null $fallback
     * @return \SugarBean|null
     */
    private function random($module, $fallback = null)
    {
        try {
            if (isset($this->box[$module]) && is_array($this->box[$module]) && count($this->box[$module]) > 20) {
                $result = $this->faker->randomElement($this->box[$module]);
            } else {
                $seed = BeanFactory::getBean($module);

                $db = DBManagerFactory::getInstance();

                $id = $db->fetchOne("SELECT id FROM $seed->table_name ORDER BY RAND() LIMIT 1")['id'];

                $result = $seed->retrieve($id);
            }

            if (!is_subclass_of($result, \SugarBean::class)) {
                throw new \RuntimeException();
            }

            return $result;
        } catch (\Exception $e) {
            return $fallback;
        }
    }

    /**
     * @param $bean \SugarBean
     */
    private function saveBean($bean)
    {
        $module = $bean->module_name;

        $bean->created_by = $this->user->id;

        $bean->id = $this->getUUID();
        $bean->new_with_id = true;

        $bean->save();

        /** @noinspection PhpUndefinedFieldInspection */
        $name = is_subclass_of($bean, Person::class)
            ? "$bean->first_name $bean->last_name"
            : $bean->name;

        echo "Saving [$module] [$name]\n";

        $this->box[$module][] = $bean;
    }

    public function randomizeAccounts($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Account $bean */
            $bean = BeanFactory::newBean('Accounts');

            $bean->name = $this->faker->company . ' ' . $this->faker->companySuffix;
            $bean->phone_office = $this->faker->phoneNumber;
            $bean->phone_alternate = $this->faker->phoneNumber;
            $bean->phone_fax = $this->faker->phoneNumber;

            $bean->website = 'www.' . $this->faker->domainName;
            $bean->email1 = $this->faker->companyEmail;

            $bean->billing_address_street = $this->faker->streetAddress;
            $bean->billing_address_city = $this->faker->city;
            $bean->billing_address_state = $this->faker->state;
            $bean->billing_address_country = $this->faker->country;
            $bean->billing_address_postalcode = $this->faker->postcode;

            $bean->description = $this->faker->text;

            $bean->industry = $this->randomIndustry();
            $bean->account_type = $this->randomAccountType();
            $bean->annual_revenue = $this->faker->numberBetween(5000, 1000000);

            $bean->created_by = $this->randomUserId();
            $bean->assigned_user_id = $this->randomUserId();

            // 20% chance of having a parent company
            if ($this->faker->boolean(20)) {
                $bean->member_id = $this->randomId('Accounts');
            }

            $this->saveBean($bean);
        }
    }

    /**
     * @return string
     */
    private function randomIndustry()
    {
        return $this->faker->randomElement([
            'Apparel',
            'Banking',
            'Biotechnology',
            'Chemicals',
            'Communications',
            'Construction',
            'Consulting',
            'Education',
            'Electronics',
            'Energy',
            'Engineering',
            'Entertainment',
            'Environmental',
            'Finance',
            'Government',
            'Healthcare',
            'Hospitality',
            'Insurance',
            'Machinery',
            'Manufacturing',
            'Media',
            'Not For Profit',
            'Recreation',
            'Retail',
            'Shipping',
            'Technology',
            'Telecommunications',
            'Transportation',
            'Utilities',
            'Other',
        ]);
    }

    /**
     * @return string
     */
    private function randomAccountType()
    {
        return $this->faker->randomElement([
            'Analyst',
            'Competitor',
            'Customer',
            'Integrator',
            'Investor',
            'Partner',
            'Press',
            'Prospect',
            'Reseller',
            'Other',
        ]);
    }

    /**
     * Returns an existing id or null in case of failure (no beans).
     *
     * @param $module
     * @return null|string
     */
    private function randomId($module)
    {
        $bean = $this->random($module);

        if (empty($bean)) {
            return null;
        }

        return $bean->id;
    }

    public function randomizeContacts($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Contact $bean */
            $bean = BeanFactory::newBean('Contacts');

            $this->fakePerson($bean);

            $bean->assigned_user_id = $this->randomUserId();
            $bean->department = $this->randomDepartment();
            $bean->account_id = $this->randomId('Accounts');
            $bean->reports_to_id = $this->randomId('Contacts');
            $bean->lead_source = $this->randomLeadSource();

            $this->saveBean($bean);
        }
    }

    /**
     * @return string
     */
    private function randomLeadSource()
    {
        return $this->faker->randomElement([
            'Cold Call',
            'Existing Customer',
            'Self Generated',
            'Employee',
            'Partner',
            'Public Relations',
            'Direct Mail',
            'Conference',
            'Trade Show',
            'Web Site',
            'Word of mouth',
            'Email',
            'Campaign',
            'Other',
        ]);
    }

    /**
     * Removes all the records created by this tool.
     */
    public function randomizePurge()
    {
        $db = DBManagerFactory::getInstance();

        $prefix = self::ID_PREFIX;

        $tables = [
            'users',
            'accounts',
            'contacts',
            'prospect_list',
            'prospect_lists_prospects',
            'prospect_list_campaigns',
        ];

        foreach ($tables as $table) {
            $db->query("DELETE FROM $table WHERE id LIKE '$prefix%'");
        }

        echo "All records have been purged from the database", PHP_EOL;
    }

    /**
     * Returns a prefixed unique id
     *
     * The rationale behind this is the ability of easily purging demo data, because their id starts with a known prefix.
     * @return string
     */
    private function getUUID()
    {
        return self::ID_PREFIX . uniqid();
    }
}