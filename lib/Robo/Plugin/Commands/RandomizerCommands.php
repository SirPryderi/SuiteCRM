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
use SuiteCRM\Randomizer\ModulesRandomizer;
use SuiteCRM\Robo\Traits\CliRunnerTrait;
use SuiteCRM\Robo\Traits\RoboTrait;

/**
 * Class RandomizerCommands helps seeding the SuiteCRM database with randomized data.
 *
 * @see ModulesRandomizer
 * @author Vittorio Iocolano
 */
class RandomizerCommands extends \Robo\Tasks
{
    use RoboTrait;
    use CliRunnerTrait;

    private $randomizer;

    public function __construct()
    {
        global $current_user;

        $this->bootstrap();

        /** @var \User $current_user */
        $current_user = BeanFactory::getBean('Users', '1');

        echo "Running as {$current_user->user_name}", PHP_EOL;

        $this->randomizer = new ModulesRandomizer($current_user);
    }

    /**
     * Seeds the SuiteCRM instance database with randomized data.
     *
     * @param int $sizeBig Used for modules like Contacts, Accounts, Leads, etc.
     * @param int $sizeSmall Used for modules like Meetings, TargetLists, etc.
     * @param int $sizeTiny Used for modules like Users.
     * @param bool $purgeFirst Removes all test data before seeding if set to true. User-entered data won't be affected.
     */
    public function randomizeAll($sizeBig = 200, $sizeSmall = 50, $sizeTiny = 10, $purgeFirst = false)
    {
        if ($purgeFirst) {
            $this->randomizePurge();
        }

        $this->randomizer->randomizeUsers($sizeTiny);

        $this->randomizer->randomizeAccounts($sizeBig);
        $this->randomizer->randomizeContacts($sizeBig);
        $this->randomizer->randomizeLeads($sizeBig);
        $this->randomizer->randomizeTargets($sizeBig);

        $this->randomizer->randomizeMeetings($sizeSmall);
        $this->randomizer->randomizeCalls($sizeBig * 2);

        $this->randomizer->randomizeOpportunities($sizeBig);
        $this->randomizer->randomizeCases($sizeBig);
        $this->randomizer->randomizeBugs($sizeBig);

        $this->randomizer->randomizeTargetLists($sizeSmall);
        $this->randomizer->randomizeCampaigns($sizeBig);

        $this->randomizer->randomizeTasks($sizeBig);
        $this->randomizer->randomizeNotes($sizeBig);

        echo "Done!", PHP_EOL;
    }

    /**
     * Removes all the records created by this tool.
     */
    public function randomizePurge()
    {
        $tables = [
            'users',
            'accounts',
            'contacts',
            'leads',
            'prospects',
            'prospect_lists',
            'prospect_lists_prospects',
            'prospect_list_campaigns',
            'campaigns',
            'email_marketing',
            'cases',
            'bugs',
            'notes',
            'calls',
            'opportunities',
            'tasks',
            'meetings'
        ];

        $this->randomizer->purgeTables($tables);

        echo "All records have been purged from the database", PHP_EOL;
    }

    /**
     * Randomizes a specific module.
     *
     * @param $module string The module name
     * @param $size int How many new records
     */
    public function randomizeModule($module, $size)
    {
        $module = ucfirst(strtolower($module));

        $methodName = "randomize$module";

        if ($methodName == "all") {
            $this->randomizeAll($size, $size / 2, $size / 4);
            return;
        }

        try {
            $this->randomizer->$methodName($size);
        } catch (\Exception $e) {
            echo "No randomizer found for $module";
        }
    }
}