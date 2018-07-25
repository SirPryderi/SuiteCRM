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
        for ($i = 1; $i <= $size; $i++) {
            /** @var User $bean */
            $bean = BeanFactory::newBean('Users');

            $this->fakePerson($bean);

            $bean->user_name = $this->faker->userName;
            $bean->department = $this->randomDepartment();
            $bean->employee_status = $this->randomEmployeeStatus();

            $bean->reports_to_id = $this->randomUserId();

            $this->saveBean($bean, $i, $size);
        }
    }

    public function randomizeTargetLists($size, $minListSize = 20, $maxListSize = 50)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \ProspectList $bean */
            $bean = BeanFactory::newBean('ProspectLists');

            $bean->name = "Target List #$i";
            $bean->list_type = 'default';
            $bean->description = $this->faker->text;
            $bean->assigned_user_id = $this->randomUserId();

            $this->saveBean($bean, $i, $size);

            // TODO replace SQL with relationship?
            $table = $bean->rel_prospects_table;
            $sql = "INSERT INTO $table (id, prospect_list_id, related_id, related_type) VALUES ";

            $count = $this->faker->numberBetween($minListSize, $maxListSize);

            echo "Adding [$count]    [Targets] to this [TargetList]", PHP_EOL;

            for ($i = 0; $i < $count; $i++) {
                $target = $this->randomContactable();
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
        for ($i = 1; $i <= $size; $i++) {
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
            $bean->annual_revenue = $this->randomAmount() . " ({$this->faker->currencyCode})";

            $bean->created_by = $this->randomUserId();
            $bean->assigned_user_id = $this->randomUserId();

            // 20% chance of having a parent company
            if ($this->faker->boolean(20)) {
                $bean->member_id = $this->randomId('Accounts');
            }

            $this->saveBean($bean, $i, $size);
        }
    }

    public function randomizeContacts($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Contact $bean */
            $bean = BeanFactory::newBean('Contacts');

            $this->fakeContact($bean);

            $bean->account_id = $this->randomId('Accounts');
            $bean->reports_to_id = $this->randomId('Contacts');

            $this->saveBean($bean, $i, $size);
        }
    }

    public function randomizeLeads($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Lead $bean */
            $bean = BeanFactory::newBean('Leads');

            $this->fakeContact($bean);

            $bean->account_name = $this->faker->optional()->company;
            $bean->reports_to_id = $this->randomId('Contacts');
            $bean->status = $this->randomAppListStrings('lead_status_dom');
            $bean->status_description = $this->faker->paragraph(2);
            $bean->opportunity_amount = $this->randomAmount() . " ({$this->faker->currencyCode})";
            $bean->refered_by = $this->randomContactable()->name;

            $this->saveBean($bean, $i, $size);
        }
    }

    public function randomizeTargets($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Prospect $bean */
            $bean = BeanFactory::newBean('Prospects');

            $this->fakeContact($bean);

            $bean->account_name = $this->faker->optional()->company;
            $bean->do_not_call = $this->faker->boolean(20);

            $this->saveBean($bean, $i, $size);
        }
    }

    public function randomizeCases($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            @$this->saveBean($case, $i, $size);
        }
    }

    public function randomizeBugs($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            $this->saveBean($bug, $i, $size);
        }
    }

    public function randomizeNotes($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            $this->saveBean($note, $i, $size);
        }
    }

    public function randomizeCalls($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            $this->saveBean($call, $i, $size);
        }
    }

    public function randomizeOpportunities($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            $this->saveBean($opportunity, $i, $size);
        }
    }

    public function randomizeTasks($size)
    {
        for ($i = 1; $i <= $size; $i++) {
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

            $this->saveBean($task, $i, $size);
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
            @$this->saveBean($meeting, $i, $size);

            // TODO code for invites
        }
    }

    public function randomizeCampaigns($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \ProspectList $prospectList */
            $prospectList = $this->random('ProspectLists');

            if (empty($prospectList)) {
                echo 'Failed to fetch a valid TargetList', PHP_EOL;
                return;
            } else {
                echo "Using  [TargetLists] $prospectList->name", PHP_EOL;
            }

            // ~ ~ ~
            // Campaign
            // ~ ~ ~

            /** @var \Campaign $campaign */
            $campaign = BeanFactory::newBean('Campaigns');

            $user = $this->random('Users');

            $campaign->name = "Newsletter #$i - " . $this->faker->realText(20);
            $campaign->campaign_type = 'Email';

            $campaign->assigned_user_id = $user->id;
            $campaign->status = 'Complete';
            $campaign->description = $this->faker->text;
            $campaign->budget = $this->randomAmount();
            $campaign->actual_cost = $this->randomAmount();
            $campaign->expected_revenue = $this->randomAmount();
            $campaign->expected_cost = $this->randomAmount();
            $campaign->impressions = $this->faker->numberBetween(0);
            $campaign->objective = $this->faker->text(500);
            $campaign->content = $this->faker->paragraphs(5, true);

            $campaign->start_date = $this->randomDate();
            $campaign->end_date = $this->randomDate($campaign->start_date);

            $this->saveBean($campaign, $i, $size);

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

            $rel = 'prospectlists';
            $campaign->load_relationship($rel);
            $campaign->$rel->add($prospectList);

            // ~ ~ ~
            // Trackers
            // ~ ~ ~

            /** @var \CampaignTracker $tracker */
            $tracker = BeanFactory::newBean('CampaignTrackers');

            $tracker->tracker_name = $campaign->name . ' Tracker';
            $tracker->tracker_url = 'https://example.com';
            $tracker->campaign_id = $campaign->id;
            $tracker->is_optout = false;

            // TODO Add opt-out?

            $this->saveBean($tracker, $i, $size);

            // ~ ~ ~
            // Logs
            // ~ ~ ~

            $relationships = ['prospects', 'accounts', 'contacts', 'leads'];
            $targets = [];

            foreach ($relationships as $rel) {
                $prospectList->load_relationship($rel);
                $targets = array_merge($targets, $prospectList->$rel->getBeans());
            }

            $count = count($targets);

            echo "Saving [CampaignLogs] for $count targets", PHP_EOL;

            foreach ($targets as $target) {
                /** @var \Person|\Contact $target */

                $log = $this->makeCampaignLog($tracker, $target, $prospectList, $marketing);

                $errorHappened = $this->faker->boolean(10);

                if ($errorHappened) {
                    $log->activity_type = $this->faker->randomElement(['send error', 'invalid email', 'blocked']);
                    $log->more_information = $target->email1;
                } else {
                    $log->activity_type = 'targeted';
                    // TODO set the related ID to the email address ID, although probably not needed
                    $log->related_type = 'Emails';
                }

                $log->hits = 1;
                @$this->saveBean($log);

                // In case of success the user might view, click or opt-out.
                // Also create new leads and stuff.
                if (!$errorHappened) {

                    $hasViewed = $this->faker->boolean(70);

                    if ($hasViewed) {

                        // ~ ~ ~
                        // Viewed
                        // ~ ~ ~

                        $viewedLog = $this->makeCampaignLogFromLog($log);;
                        $viewedLog->activity_type = 'viewed';
                        $viewedLog->hits = $this->faker->biasedNumberBetween(1, 20);
                        $this->saveBean($viewedLog);

                        // Now that they've viewed for sure, did they click something too?

                        $hasClicked = $this->faker->boolean();
                        $hasOptedOut = $this->faker->boolean(10);
                        $hasCreatedLead = $this->faker->boolean(7);
                        $hasCreatedContact = $this->faker->boolean(7);

                        // ~ ~ ~
                        // Link Clicked
                        // ~ ~ ~

                        if ($hasClicked) {
                            $clickedLog = $this->makeCampaignLogFromLog($log);;
                            $clickedLog->activity_type = 'link';
                            $clickedLog->hits = $this->faker->biasedNumberBetween(1, 20);
                            $clickedLog->activity_date = $this->randomDateTime($viewedLog->activity_date);
                            $this->saveBean($clickedLog);
                        }

                        // ~ ~ ~
                        // Opted Out
                        // ~ ~ ~

                        if ($hasOptedOut) {
                            $optedOutLog = $this->makeCampaignLogFromLog($log);;
                            $optedOutLog->activity_type = 'removed';
                            $optedOutLog->activity_date = $this->randomDateTime($viewedLog->activity_date);
                            $optedOutLog->hits = 1;
                            $this->saveBean($optedOutLog);
                        }

                        // ~ ~ ~
                        // Lead Created
                        // ~ ~ ~

                        if ($hasCreatedLead && $target->module_name === "Prospects") {
                            $time = $this->randomDateTime($viewedLog->activity_date);

                            /** @var \Lead $lead */
                            $lead = BeanFactory::newBean('Leads');
                            $lead->fromArray($target->toArray());
                            $lead->campaign_id = $campaign->id;
                            $lead->lead_source_description = $campaign->name;
                            $lead->lead_source = 'Campaign';
                            $lead->opportunity_amount = $this->randomAmount();
                            $lead->date_entered = $time;
                            $this->saveBean($lead);

                            $createdLeadLog = $this->makeCampaignLogFromLog($log);
                            $createdLeadLog->activity_type = 'lead';
                            $createdLeadLog->activity_date = $time;
                            $createdLeadLog->related_type = $lead->module_name;
                            $createdLeadLog->related_id = $lead->id;
                            $createdLeadLog->hits = 1;

                            $this->saveBean($createdLeadLog);
                        }

                        // ~ ~ ~
                        // Contact Created
                        // ~ ~ ~

                        if ($hasCreatedContact && $target->module_name === "Prospects") {
                            $time = $this->randomDateTime($viewedLog->activity_date);

                            /** @var \Contact $contact */
                            $contact = BeanFactory::newBean('Contacts');
                            $contact->fromArray($target->toArray());
                            $contact->campaign_id = $campaign->id;
                            $contact->lead_source = 'Campaign';
                            $contact->date_entered = $time;
                            $this->saveBean($contact);

                            $createdLeadLog = $this->makeCampaignLogFromLog($log);
                            $createdLeadLog->activity_type = 'contact';
                            $createdLeadLog->activity_date = $time;
                            $createdLeadLog->related_type = $contact->module_name;
                            $createdLeadLog->related_id = $contact->id;
                            $createdLeadLog->hits = 1;

                            $this->saveBean($createdLeadLog);
                        }
                    }
                }
            }
        }


    }

    /**
     * @param \CampaignTracker $tracker
     * @param \SugarBean $target
     * @param \ProspectList $prospectList
     * @param \EmailMarketing $marketing
     * @return \CampaignLog
     */
    private
    function makeCampaignLog(
        \CampaignTracker $tracker,
        \SugarBean $target,
        \ProspectList $prospectList,
        \EmailMarketing $marketing
    )
    {
        /** @var \CampaignLog $log */
        $log = BeanFactory::newBean('CampaignLog');

        $log->campaign_id = $tracker->campaign_id;
        $log->target_tracker_key = $tracker->tracker_key;
        $log->target_id = $target->id;
        $log->target_type = $target->module_name;

        $log->list_id = $prospectList->id;
        $log->marketing_id = $marketing->id;

        $log->activity_date = $this->randomDateTime();

        return $log;
    }

    private function makeCampaignLogFromLog(\CampaignLog $other)
    {
        /** @var \CampaignLog $log */
        $log = BeanFactory::newBean('CampaignLog');

        $log->campaign_id = $other->campaign_id;
        $log->target_tracker_key = $other->target_tracker_key;
        $log->target_id = $other->target_id;
        $log->target_type = $other->target_type;

        $log->list_id = $other->list_id;
        $log->marketing_id = $other->marketing_id;

        $log->activity_date = $this->randomDateTime();

        return $log;
    }

    public function randomizeEmails($size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Email $email */
            $email = BeanFactory::newBean('Emails');

            $email->assigned_user_id = $this->randomUserId();
            $email->date_sent = $this->randomDateTime('-10 years', 'now');

            $email->name = $this->faker->words(3, true); // email subject
            $email->description = $this->faker->realText(1000); // email body

            $outbound = $this->faker->boolean();

            if ($outbound) {
                $email->type = 'out';
                $email->status = 'sent';
                $from = $this->random('Users');
                $to = $this->randomContactable();
            } else {
                $email->type = 'inbound';

                $email->status = $this->faker->randomElement([
                    'archived',
                    'read',
                    'replied',
                    'unread'
                ]);
                
                $from = $this->randomContactable();
                $to = $this->random('Users');
            }

            // This is the related topic of the email
            // $email->parent_id = $account_id;
            // $email->parent_type = 'Accounts';

            $email->to_addrs = $to->emailAddress->getPrimaryAddress($to);
            $email->from_addr = $from->emailAddress->getPrimaryAddress($from);

            $email->from_addr_name = $email->from_addr;
            $email->to_addrs_names = $email->to_addrs;

            $this->saveBean($email, $i, $size);

            // ~ ~ ~
            // Relationships
            // ~ ~ ~

            $from_rel = $from->table_name;
            $email->load_relationship($from_rel);
            $email->$from_rel->add($from);

            $to_rel = $to->table_name;
            $email->load_relationship($to_rel);
            $email->$to_rel->add($to);
        }
    }

    public function randomizeProjects($size, $teamSizeMin = 1, $teamSizeMax = 6)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \Project $project */
            // It will complain in the end, so I am just auto-loading the class here
            BeanFactory::newBean('Projects');

            $project = new \Project();

            $project->name = "The " . ucfirst($this->faker->word) . " Project";
            $project->priority = $this->randomAppListStrings('projects_priority_options');
            $project->status = $this->randomAppListStrings('project_status_dom');
            $project->override_business_hours = $this->faker->boolean;
            $project->assigned_user_id = $this->randomUserId();

            $duration = $this->faker->numberBetween(15, 80); // days

            if ($project->status == 'Draft' || $project->status == 'In Review') {
                $project->estimated_start_date = $this->randomDate('now', '+15 years');
                $project->estimated_end_date = $this->modifyDateString($project->estimated_start_date, "+$duration days");
            } elseif ($project->status == 'Underway' || $project->status == 'On_Hold') {
                $project->estimated_start_date = $this->randomDate('-15 days', 'now');
                $project->estimated_end_date = $this->modifyDateString($project->estimated_start_date, "+$duration days");
            } else {
                $project->estimated_end_date = $this->randomDate('-10 years', 'now');
                $project->estimated_start_date = $this->modifyDateString($project->estimated_end_date, "-$duration days");
            }

            $this->saveBean($project, $i, $size);

            // ~ ~ ~
            // Resources
            // ~ ~ ~

            $team = $this->randomSet('Users', $teamSizeMin, $teamSizeMax);

            $relationship = 'project_users_1';
            $project->load_relationship($relationship);

            foreach ($team as $resource) {
                $project->$relationship->add($resource);
            }

            // ~ ~ ~
            // Tasks
            // ~ ~ ~

            $tasksCount = $this->faker->numberBetween($teamSizeMin * 2, $teamSizeMax * 2);

            $this->randomizeProjectTasks($project, $team, $tasksCount);

            // ~ ~ ~
            // Related Item
            // ~ ~ ~

            $related = $this->randomOfAKind(['Accounts', 'Opportunities', 'Cases', 'Bugs', 'Contacts']);

            $rel = $related->table_name;
            $project->load_relationship($rel);
            $project->$rel->add($related);
        }
    }

    private function randomizeProjectTasks(\Project $project, $team, $size)
    {
        for ($i = 1; $i <= $size; $i++) {
            /** @var \ProjectTask $task */
            BeanFactory::newBean('ProjectTasks');
            $task = new \ProjectTask();

            $task->name = $project->name . " - Task $i";
            $task->project_id = $project->id;

            $task->task_number = $i;

            $task->assigned_user_id = $this->faker->randomElement($team)->id;

            $task->description = $this->faker->text;

            $duration = $this->faker->biasedNumberBetween(1, 14, $function = 'Faker\Provider\Biased::linearLow'); // days

            $task->date_start = $this->randomDate($project->estimated_start_date, $project->estimated_end_date);
            $task->date_finish = $this->modifyDateString($task->date_start, "+$duration days");

            if ($project->status == 'Draft' || $project->status == 'In Review') {
                $task->percent_complete = 0;
            } elseif ($project->status == 'Underway' || $project->status == 'On_Hold') {
                $task->percent_complete = $this->faker->numberBetween(0, 100);
            } else {
                $task->percent_complete = 100;
            }

            // Timeline

            $task->status = $this->randomAppListStrings('task_status_dom');
            $task->priority = $this->randomAppListStrings('project_task_priority_options');

            $task->predecessors = 0;

            $task->duration = $duration;
            $task->duration_unit = 'Days';

            $task->estimated_effort = $this->faker->numberBetween(5, 300);
            $task->actual_effort = $this->faker->numberBetween(5, 300);
            $task->utilization = $this->faker->randomElement(['none', '25', '50', '75', '100']);
            $task->relationship_type = $this->randomAppListStrings('relationship_type_list');

            $task->milestone_flag = $this->faker->boolean;

            $this->saveBean($task, $i, $size);
        }
    }
}