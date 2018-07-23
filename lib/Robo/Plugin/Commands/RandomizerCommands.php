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
        define('SUGARCRM_IS_INSTALLING', true);

        global $current_user;

        $this->bootstrap();
        $this->faker = Factory::create();
        $this->user = BeanFactory::getBean('Users', '1');
        $current_user = $this->user;
        $this->box['Users'][] = $this->user;
        echo "Running as {$this->user->user_name}", PHP_EOL;
    }

    public function randomizeAll($sizeBig = 200, $sizeSmall = 50, $sizeTiny = 10, $purgeFirst = false)
    {
        if ($purgeFirst) {
            $this->randomizePurge();
        }

        $this->randomizeUsers($sizeTiny);
        $this->randomizeAccounts($sizeBig);
        $this->randomizeCases($sizeBig);
        $this->randomizeBugs($sizeBig);
        $this->randomizeContacts($sizeBig);
        $this->randomizeTargetLists($sizeSmall);
        $this->randomizeCampaigns($sizeBig);
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
                'Intern',
                'President',
                'VP Operations',
                'VP Sales',
                'Director Operations',
                'Director Sales',
                'Mgr Operations',
                'IT Developer',
                'Senior Product Manager'
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

                if (!$id) {
                    throw new \RuntimeException();
                }

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
        global $app_list_strings;
        return $this->faker->randomKey($app_list_strings['industry_dom']);
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

    public function randomizeCases($size)
    {
        global $app_list_strings;

        for ($i = 0; $i < $size; $i++) {
            /** @var \aCase $case */
            $case = BeanFactory::newBean('Cases');

            $account = $this->random('Accounts');

            if (empty($account)) {
                echo "Unable to create randomize Case because no valid account has been found", PHP_EOL;
                return;
            }

            $case->account_id = $account->id;
            $case->account_name = $account->name;

            $case->name = $this->randomCaseName();
            $case->priority = $this->faker->randomKey($app_list_strings['case_priority_dom']);
            $case->status = $this->faker->randomKey($app_list_strings['case_status_dom']);
            $case->type = $this->faker->randomKey($app_list_strings['case_type_dom']);

            $case->assigned_user_id = $account->assigned_user_id;

            @$this->saveBean($case);
        }
    }

    /**
     * @return string
     */
    private function randomCaseName()
    {
        return $this->faker->randomElement([
            'Having trouble adding new items',
            'System not responding',
            'Need assistance with large customization',
            'Need to purchase additional licenses',
            'Warning message when using the wrong browser'
        ]);
    }

    public function randomizeBugs($size)
    {
        global $app_list_strings;

        for ($i = 0; $i < $size; $i++) {
            /** @var \Bug $bug */
            $bug = BeanFactory::newBean('Bugs');

            $account = $this->random('Accounts');

            if (empty($account)) {
                echo "Unable to create randomize Bug because no valid account has been found", PHP_EOL;
                return;
            }

            $bug->account_id = $account->id;
            $bug->priority = $this->faker->randomKey($app_list_strings['bug_priority_dom']);
            $bug->status = $this->faker->randomKey($app_list_strings['bug_status_dom']);
            $bug->type = $this->faker->randomKey($app_list_strings['bug_type_dom']);
            $bug->source = $this->faker->randomKey($app_list_strings['source_dom']);
            $bug->resolution= $this->faker->randomKey($app_list_strings['issue_resolution_dom']);
            $bug->product_category = $this->faker->randomKey($app_list_strings['product_category_dom']);
            $bug->name = $this->randomBugName();

            $bug->assigned_user_id = $account->assigned_user_id;

            $this->saveBean($bug);
        }
    }

    private function randomBugName()
    {
        return $this->faker->randomElement([
            'Error occurs while running count query',
            'Warning is displayed in file after exporting',
            'Fatal error during installation',
            'Broken image appears in home page',
            'Syntax error appears when running old reports'
        ]);
    }

    public function randomizeCampaigns($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Campaign $campaign */
            $campaign = BeanFactory::newBean('Campaigns');

            $user = $this->random('Users');

            $campaign->name = "Newsletter #$i";
            $campaign->assigned_user_id = $user->id;
            $campaign->status = $this->faker->randomElement(['Planning', 'Inactive', 'Active', 'Complete']);
            $campaign->description = $this->faker->text;
            $campaign->budget = $this->faker->numberBetween(500);
            $campaign->actual_cost = $this->faker->numberBetween(500);
            $campaign->expected_revenue = $this->faker->numberBetween(500);
            $campaign->expected_cost = $this->faker->numberBetween(500);
            $campaign->impressions = $this->faker->numberBetween(0);
            $campaign->objective = $this->faker->text(500);
            $campaign->content = $this->faker->paragraphs(5, true);
            $campaign->campaign_type = 'Newsletter';

            $this->saveBean($campaign);

            /** @var \EmailMarketing $marketing */
            $marketing = BeanFactory::newBean('EmailMarketing');
            $marketing->name = $campaign->name . " Email Marketing";

            $marketing->from_name = $user->name;
            $marketing->from_addr = 'no-reply@example.com';
            $marketing->reply_to_name = $user->name;
            $marketing->reply_to_addr = 'no-reply@example.com';

            // TODO generate email accounts?
            $marketing->inbound_email_id = $this->randomId('InboundEmail');
            $marketing->outbound_email_id = $this->randomId('OutboundEmailAccounts');

            $marketing->date_start = $this->faker->dateTimeThisCentury->format("Y-m-d H:i:s");
            $marketing->template_id = $this->randomId('EmailTemplates');
            $marketing->status = strtolower($campaign->status);
            $marketing->campaign_id = $campaign->id;
            $marketing->all_prospect_lists = true;

            // TODO Tracker!

            $this->saveBean($marketing);
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
            'prospect_lists',
            'prospect_lists_prospects',
            'prospect_list_campaigns',
            'campaigns',
            'email_marketing',
            'cases',
            'bugs',
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