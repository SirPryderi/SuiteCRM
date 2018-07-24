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
 * Time: 08:21
 */

namespace SuiteCRM\Randomizer;

use BeanFactory;
use DBManagerFactory;
use User;

/**
 * This class handles randomization for each SuiteCRM module.
 *
 * @author Vittorio Iocolano
 */
class ModulesRandomizer extends BaseRandomizer
{
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
            /** @noinspection PhpUndefinedFieldInspection */
            $bean->billing_address_state = $this->faker->state;
            $bean->billing_address_country = $this->faker->country;
            $bean->billing_address_postalcode = $this->faker->postcode;

            $bean->description = $this->faker->text;

            $bean->industry = $this->randomIndustry();
            $bean->account_type = $this->randomAccountType();
            $bean->annual_revenue = $this->randomAmount() . ' (USD $)';

            $bean->created_by = $this->randomUserId();
            $bean->assigned_user_id = $this->randomUserId();

            // 20% chance of having a parent company
            if ($this->faker->boolean(20)) {
                $bean->member_id = $this->randomId('Accounts');
            }

            $this->saveBean($bean);
        }
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

            $case->name = $this->randomDemoData('case_seed_names');
            $case->priority = $this->randomAppListStrings('case_priority_dom');
            $case->status = $this->randomAppListStrings('case_status_dom');
            $case->type = $this->randomAppListStrings('case_type_dom');

            $case->assigned_user_id = $account->assigned_user_id;

            @$this->saveBean($case);
        }
    }

    public function randomizeBugs($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Bug $bug */
            $bug = BeanFactory::newBean('Bugs');

            $account = $this->random('Accounts');

            if (empty($account)) {
                echo "Unable to create randomize Bug because no valid account has been found", PHP_EOL;
                return;
            }

            $bug->account_id = $account->id;
            $bug->priority = $this->randomAppListStrings('bug_priority_dom');
            $bug->status = $this->randomAppListStrings('bug_status_dom');
            $bug->type = $this->randomAppListStrings('bug_type_dom');
            $bug->source = $this->randomAppListStrings('source_dom');
            $bug->resolution = $this->randomAppListStrings('issue_resolution_dom');
            $bug->product_category = $this->randomAppListStrings('product_category_dom');
            $bug->name = $this->randomDemoData('bug_seed_names');

            $bug->assigned_user_id = $account->assigned_user_id;

            $this->saveBean($bug);
        }
    }

    public function randomizeNotes($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Note $note */
            $note = BeanFactory::newBean('Notes');

            $type = $this->faker->randomElement(['Accounts', 'Cases', 'Opportunities']);
            $parent = $this->random($type);

            if (empty($parent)) {
                echo "Unable to create randomize Bug because no valid $type has been found", PHP_EOL;
                return;
            }

            $note->contact_id = $this->randomId('Contacts');
            $note->parent_type = $type;
            $note->parent_id = $parent->id;

            $seeData = $this->randomDemoData('note_seed_names_and_Descriptions');

            $note->name = $seeData[0];
            $note->description = $seeData[1];

            $note->assigned_user_id = $parent->assigned_user_id;

            $this->saveBean($note);
        }
    }

    public function randomizeCalls($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Call $call */
            $call = BeanFactory::newBean('Calls');

            $type = $this->faker->randomElement(['Accounts', 'Contacts']);
            $parent = $this->random($type);

            $this->faker;

            if (empty($parent)) {
                echo "Unable to create randomize Call because no valid $type has been found", PHP_EOL;
                continue;
            }

            $call->parent_type = $type;
            $call->parent_id = $parent->id;

            $call->name = $this->randomDemoData('call_seed_data_names');
            $call->assigned_user_id = $this->randomUserId();

            $call->direction = $this->randomAppListStrings('call_direction_dom');
            $call->status = $this->randomAppListStrings('call_status_dom');

            $call->date_start = $this->randomDateTime();
            $call->duration_hours = '0';
            $call->duration_minutes = $this->faker->numberBetween(1, 59);

            $this->saveBean($call);
        }
    }

    public function randomizeOpportunities($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Opportunity $opportunity */
            $opportunity = BeanFactory::newBean('Opportunities');

            $account = $this->random('Accounts');

            $opportunity->account_id = $account->id;
            $opportunity->assigned_user_id = $account->assigned_user_id;
            $opportunity->name = $account->name . " - " . $this->faker->sentence(6, true);
            $opportunity->lead_source = $this->randomAppListStrings('lead_source_dom');
            $opportunity->sales_stage = $this->randomAppListStrings('sales_stage_dom');

            // If the deal is already one, make the date closed occur in the past.
            if ($opportunity->sales_stage == "Closed Won" || $opportunity->sales_stage == "Closed Lost") {
                $opportunity->date_closed = $this->randomDate(null, 'now');
            } else {
                $opportunity->date_closed = $this->randomDate('now', '+15 years');
            }

            $opportunity->opportunity_type = $this->randomAppListStrings('opportunity_type_dom');

            $opportunity->amount = $this->randomAmount();
            $opportunity->probability = $this->randomPercentage();

            $opportunity->description = $this->faker->sentences(3, true);

            $this->saveBean($opportunity);
        }
    }

    public function randomizeTasks($size)
    {
        for ($i = 0; $i < $size; $i++) {
            /** @var \Task $task */
            $task = BeanFactory::newBean('Tasks');

            $parentType = $this->faker->randomElement(['Accounts', 'Contacts']);
            $parent = $this->random($parentType);

            $task->name = $this->randomDemoData('task_seed_data_names');

            $task->parent_id = $parent->id;
            $task->parent_type = $parentType;

            $task->assigned_user_id = $this->randomUserId();

            $task->priority = $this->randomAppListStrings('task_priority_dom');
            $task->status = $this->randomAppListStrings('task_status_dom');

            if ($task->status === 'Completed') {
                $task->date_due = $this->randomDateTime(null, 'now');
                $task->date_due_flag = 1;
            } else {
                $task->date_due = $this->randomDateTime('-2 days', '+1 months');
                $task->date_due_flag = 0;
            }

            $task->date_start = $this->randomDateTime(null, $task->date_due);

            $this->saveBean($task);
        }
    }

    public function randomizeMeetings($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Meeting $meeting */
            $meeting = BeanFactory::newBean('Meetings');

            $parentType = $this->faker->randomElement(['Accounts', 'Contacts']);
            $parent = $this->random($parentType);

            $meeting->name = $this->randomDemoData('meeting_seed_data_names');
            $meeting->description = $this->randomDemoData('meeting_seed_data_descriptions_v2');
            $meeting->status = $this->randomAppListStrings('meeting_status_dom');

            if ($meeting->status == 'Planned') {
                // Future date
                $meeting->date_start = $this->randomDateTime('now', '+1 months');
            } else {
                // Past date
                $meeting->date_start = $this->randomDateTime(null, 'now');
            }

            $meeting->duration_hours = $this->faker->numberBetween(0, 2);
            $meeting->duration_minutes = $this->faker->numberBetween(0, 59);

            $meeting->assigned_user_id = $this->randomUserId();

            $meeting->parent_id = $parent;
            $meeting->parent_type = $parentType;
            // dont update vcal
            $meeting->update_vcal = false;

            // TODO Fix mysterious warnings
            @$this->saveBean($meeting);

            // TODO code for invites
        }
    }

    public function randomizeCampaigns($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \ProspectList $prospectList */
            $prospectList = $this->random('ProspectLists');

            // ~ ~ ~
            // Campaign
            // ~ ~ ~

            /** @var \Campaign $campaign */
            $campaign = BeanFactory::newBean('Campaigns');

            $user = $this->random('Users');

            $campaign->name = "Newsletter #$i - " . $this->faker->realText(20);
            $campaign->campaign_type = 'Email';

            $campaign->assigned_user_id = $user->id;
            $campaign->status = $this->faker->randomElement(['Planning', 'Inactive', 'Active', 'Complete']);
            $campaign->description = $this->faker->text;
            $campaign->budget = $this->randomAmount();
            $campaign->actual_cost = $this->randomAmount();
            $campaign->expected_revenue = $this->randomAmount();
            $campaign->expected_cost = $this->randomAmount();
            $campaign->impressions = $this->faker->numberBetween(0);
            $campaign->objective = $this->faker->text(500);
            $campaign->content = $this->faker->paragraphs(5, true);

            $this->saveBean($campaign);

            // ~ ~ ~
            // Email Marketing
            // ~ ~ ~

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

            $marketing->date_start = $this->randomDateTime();
            $marketing->template_id = $this->randomId('EmailTemplates');
            $marketing->status = strtolower($campaign->status);
            $marketing->campaign_id = $campaign->id;
            $marketing->all_prospect_lists = true;

            $this->saveBean($marketing);

            $campaign->load_relationship('prospectlists');
            $campaign->prospectlists->get();
            $campaign->prospectlists->add($prospectList);

            // ~ ~ ~
            // Trackers
            // ~ ~ ~

            /** @var \CampaignTracker $tracker */
            $tracker = BeanFactory::newBean('CampaignTrackers');

            $tracker->tracker_name = $campaign->name . " Tracker";
            $tracker->tracker_url = "https://example.com";
            $tracker->campaign_id = $campaign->id;
            $tracker->is_optout = false;

            // TODO Add opt-out?

            $this->saveBean($tracker);

            // ~ ~ ~
            // Logs
            // ~ ~ ~

            /** @var \CampaignLog $log */
            $log = BeanFactory::newBean('CampaignLog');

            // TODO cycle for a list of targets
            $target = $this->randomContactable();

            $log->campaign_id = $campaign->id;
            $log->target_tracker_key = $tracker->tracker_key;
            $log->target_id = $target->id;
            $log->target_type = $target->module_name;

            $errorHappened = $this->faker->boolean(10);

            if ($errorHappened) {
                $log->activity_type = $this->faker->randomElement(['send error', 'invalid email', 'blocked']);
            } else {
                $log->activity_type = $this->faker->randomElement([
                    'targeted' => 'Message Sent/Attempted',
                    'link' => 'Click-thru Link',
                    'viewed' => 'Viewed Message',
                    'removed' => 'Opted Out',
                    // Do they need enabling?
                    // 'lead' => 'Leads Created',
                    // 'contact' => 'Contacts Created',
                ]);
            }

            $log->list_id = $prospectList->id;
            $log->marketing_id = $marketing->id;

            $this->saveBean($log);
        }
    }
}