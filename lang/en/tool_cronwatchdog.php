<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin tool "Cron watchdog" - Language pack
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Cron watchdog';
$string['error_maxfaildelay'] = '{$a->taskname}: This task has a faildelay of {$a->faildelay} minutes.';
$string['error_maxrundelay'] = '{$a->taskname}: This task should have run {$a->rundelay} minutes ago, but it did not.';
$string['error_maxglobaldelay'] = 'General problem: The Moodle cron job should have run {$a->globalrundelay} minutes ago, but it did not.';
$string['error_maxupgradedelay'] = 'General problem: The Moodle cron job was delayed because of a pending Moodle upgrade for more than {$a->globalrundelay} minutes.';
$string['cronwatchdog:receivenotification'] = 'Receive Cron watchdog notifications';
$string['messageprovider:notify'] = 'Cron watchdog notifications';
$string['message_smallmessage'] = 'Cron watchdog: {$a->tasks} tasks have problems';
$string['message_statusproblems'] = 'Following problems have been detected:';
$string['message_statusok'] = 'All tasks are running without problems.';
$string['message_subject'] = 'Cron watchdog';
$string['privacy:metadata'] = 'The admin tool cron watchdog does not store any personal data.';
$string['setting_checkdetailsheading'] = 'Check details';
$string['setting_checkgeneralheading'] = 'General check settings';
$string['setting_enablefaildelay'] = 'Enable faildelay check';
$string['setting_enablefaildelay_desc'] = 'If this setting is enabled, cron watchdog will check if any scheduled task has a problematic faildelay.';
$string['setting_enableglobaldelay'] = 'Enable global cron delay check';
$string['setting_enableglobaldelay_desc'] = 'If this setting is enabled, cron watchdog will check if the Moodle cron job has a problematic delay.';
$string['setting_enablepoormanscron'] = 'Enable poor man\'s cron';
$string['setting_enablepoormanscron_desc'] = 'If this setting is enabled, cron watchdog will not only run as scheduled task itself, but will also be triggered as soon as any user visits Moodle in the web.';
$string['setting_enablerundelay'] = 'Enable rundelay check';
$string['setting_enablerundelay_desc'] = 'If this setting is enabled, cron watchdog will check if any scheduled task has a problematic delay.';
$string['setting_enableupgradedelay'] = 'Enable upgrade delay check';
$string['setting_enableupgradedelay_desc'] = 'If this setting is enabled, cron watchdog will check if the Moodle cron job has been blocked by a Moodle DB upgrade for a problematic time.';
$string['setting_maxfaildelay'] = 'Maximum faildelay in minutes';
$string['setting_maxfaildelay_desc'] = 'The faildelay value in minutes which is acceptable for this system. If the faildelay of a task if greater than the configured amount of minutes, a problem is reported for this task.';
$string['setting_maxglobaldelay'] = 'Maximum global cron in minutes';
$string['setting_maxglobaldelay_desc'] = 'The global cron delay value in minutes which is acceptable for this system. If the Moodle cron job has not run successfully for more than the configured amount of minutes, a problem is reported for this task.';
$string['setting_maxrundelay'] = 'Maximum rundelay in minutes';
$string['setting_maxrundelay_desc'] = 'The rundelay value in minutes which is acceptable for this system. If a scheduled task has not run successfully for more than the configured amount of minutes, a problem is reported for this task.';
$string['setting_maxupgradedelay'] = 'Maximum upgrade delay in minutes';
$string['setting_maxupgradedelay_desc'] = 'The upgrade delay value in minutes which is acceptable for this system. If the Moodle cron job has been blocked by a Moodle DB upgrade for more than the configured amount of minutes, a problem is reported for this task.';
$string['setting_poormanscroninterval'] = 'Poor man\'s cron interval';
$string['setting_poormanscroninterval_desc'] = 'The poor man\'s cron interval in minutes. While poor man\'s cron will trigger watchdog as soon as any user visits Moodle in the web, running the checks will only be done every configured amount of minutes.';
$string['taskcheck'] = 'Cron watchdog';