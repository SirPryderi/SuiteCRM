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
 * Date: 24/07/18
 * Time: 08:17
 */

namespace SuiteCRM\Randomizer;


use BeanFactory;
use DBManagerFactory;
use Faker\Factory;
use Faker\Generator;
use Person;
use SugarBean;

require_once __DIR__ . '/../../install/demoData.en_us.php';

/**
 * This class contains base utilities to generate random data for SuiteCRM.
 *
 * An example usage is ModulesRandomizer.
 *
 * @see ModulesRandomizer
 * @author Vittorio Iocolano
 */
abstract class BaseRandomizer
{
    /** @var string */
    const ID_PREFIX = 'randomizer.';
    /** @var array */
    protected $box;
    /** @var Generator */
    protected $faker;
    /** @var \User */
    private $user;

    /**
     * BaseRandomizer constructor.
     * @param \User $user
     */
    public function __construct(\User $user)
    {
        define('SUGARCRM_IS_INSTALLING', true);
        $this->faker = Factory::create();
        $this->user = $user;
        $this->box['Users'][] = $this->user;
    }

    public function purgeTables(array $tables)
    {
        $db = DBManagerFactory::getInstance();

        $prefix = self::ID_PREFIX;

        foreach ($tables as $table) {
            $db->query("DELETE FROM $table WHERE id LIKE '$prefix%'");
        }
    }

    /**
     * @return string
     */
    protected function randomDepartment()
    {
        return $this->faker->randomElement(['Marketing', 'Sales', 'Management', 'Production']);
    }

    /**
     * @return string
     */
    protected function randomEmployeeStatus()
    {
        return $this->faker->randomElement(['Active', 'Terminated', 'Leave of Absence']);
    }

    /**
     * @return string
     */
    protected function randomUserId()
    {
        return $this->random('Users', $this->user)->id;
    }

    /**
     * @param $module
     * @param null $fallback
     * @return SugarBean|null
     */
    protected function random($module, $fallback = null)
    {
        try {
            if (isset($this->box[$module]) && is_array($this->box[$module]) && count($this->box[$module]) > 20) {
                $result = $this->faker->randomElement($this->box[$module]);
            } else {
                $seed = BeanFactory::getBean($module);

                $db = DBManagerFactory::getInstance();

                $id = $db->fetchOne("SELECT id FROM $seed->table_name WHERE deleted=0 ORDER BY RAND() LIMIT 1")['id'];

                if (!$id) {
                    throw new \RuntimeException();
                }

                $result = $seed->retrieve($id);
            }

            if (!is_subclass_of($result, SugarBean::class)) {
                throw new \RuntimeException();
            }

            return $result;
        } catch (\Exception $e) {
            return $fallback;
        }
    }

    /**
     * Performs various utilities to properly save a randomized bean.
     *
     * If $current and $total are provided a progress display (eg: [50/100]) is shown.
     *
     * @param $bean SugarBean
     * @param int $current
     * @param int $total
     */
    protected function saveBean(SugarBean $bean, $current = null, $total = null)
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

        if (empty($current) && empty($total)) {
            $progress = '';
        } else {
            $progress = sprintf(" [%02d/%02d]", $current, $total);
        }

        if ($module != 'CampaignLog') {
            echo "Saving$progress [$module] [$name]", PHP_EOL;
        }

        $this->box[$module][] = $bean;
    }

    /**
     * Returns a prefixed unique id
     *
     * The rationale behind this is the ability of easily purging demo data, because their id starts with a known prefix.
     * @return string
     */
    protected function getUUID()
    {
        return self::ID_PREFIX . uniqid();
    }

    /**
     * @param $bean Person
     */
    protected function fakePerson(Person $bean)
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
        /** @noinspection PhpUndefinedFieldInspection */
        $bean->primary_address_state = $this->faker->state;
        $bean->primary_address_postalcode = $this->faker->postcode;
        $bean->primary_address_country = $this->faker->country;

        if ($this->faker->boolean) { // has alt address
            $bean->alt_address_street = $this->faker->streetAddress;
            $bean->alt_address_city = $this->faker->city;
            /** @noinspection PhpUndefinedFieldInspection */
            $bean->alt_address_state = $this->faker->state;
            $bean->alt_address_postalcode = $this->faker->postcode;
            $bean->alt_address_country = $this->faker->country;
        }

        $bean->description = $this->faker->realText(500);

        $bean->email1 = $this->faker->email;
    }

    /**
     * @param \Contact|\Lead|\Prospect $bean
     */
    protected function fakeContact($bean)
    {
        $this->fakePerson($bean);

        $bean->assigned_user_id = $this->randomUserId();
        $bean->department = $this->randomDepartment();
        $bean->lead_source = $this->randomLeadSource();
    }

    /**
     * @return string
     */
    protected function randomTitle()
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
    protected function randomIndustry()
    {
        return $this->randomAppListStrings('industry_dom');
    }

    protected function randomAppListStrings($key)
    {
        global $app_list_strings;

        return $this->faker->randomKey($app_list_strings[$key]);
    }

    /**
     * @return string
     */
    protected function randomAccountType()
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
    protected function randomId($module)
    {
        $bean = $this->random($module);

        if (empty($bean)) {
            return null;
        }

        return $bean->id;
    }

    /**
     * Returns a set of beans from the requested module of size between $min and $max.
     *
     * @param $module
     * @param $min
     * @param $max
     * @return SugarBean[]
     */
    protected function randomSet($module, $min, $max)
    {
        $size = $this->faker->numberBetween($min, $max);
        $tries = 0;
        $set = [];

        while (count($set) < $size && $tries < $max * 1.5) {
            $entry = $this->random($module);

            if (!empty($entry)) {
                $set[] = $entry;
            }

            $tries++;
        }

        return $set;
    }

    /**
     * Returns an instance of random kind specified in the $modules parameter.
     *
     * @param array $modules
     * @return SugarBean|null
     */
    protected function randomOfAKind(array $modules)
    {
        $module = $this->faker->randomElement($modules);

        return $this->random($module);
    }

    /**
     * @return string
     */
    protected function randomLeadSource()
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
     * Retrieves an element of the array defined in demoData.en_us.php
     * @param $key
     * @return mixed
     */
    protected function randomDemoData($key)
    {
        global $sugar_demodata;

        return $this->faker->randomElement($sugar_demodata[$key]);
    }

    /**
     * @param string $min
     * @param string $max
     * @return string
     */
    protected function randomDateTime($min = '-15 years', $max = 'now')
    {
        return $this->faker->dateTimeBetween($min, $max)->format("Y-m-d H:i:s");
    }

    /**
     * @param string $min
     * @param string $max
     * @return string
     */
    protected function randomDate($min = '-15 years', $max = 'now')
    {
        return $this->faker->dateTimeBetween($min, $max)->format("Y-m-d");
    }

    /**
     * Fetches a Date string, applies a modification like "+3 days" and returns a re-formatted (Y-m-d) string.
     *
     * @param string $date
     * @param string $mod
     * @return string
     */
    protected function modifyDateString($date, $mod)
    {
        $dateTime = new \DateTime($date);

        $dateTime->modify($mod);

        return $dateTime->format("Y-m-d");
    }

    /**
     * @return int
     */
    protected function randomPercentage()
    {
        return $this->faker->numberBetween(0, 100);
    }

    /**
     * Returns a realist amount of money (US dollars).
     *
     * @return int
     */
    protected function randomAmount()
    {
        return $this->faker->numberBetween(50, 99999) * 100;
    }

    /**
     * @return Person|\Account|\Contact|\Prospect|\Lead
     */
    protected function randomContactable()
    {
        $type = $this->faker->randomElement(['Accounts', 'Contacts', 'Prospects', 'Leads']);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->random($type);
    }
}