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
 * Admin tool "Cron watchdog" - Local library
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper function to do the actual checks of the watchdog.
 *
 * @param bool $echo If yes, echo output directly, if no, don't echo anything.
 */
function tool_cronwatchdog_check($echo = false) {
    global $DB;

    $currenttime = time();
    // Get config.
    $config = get_config('tool_cronwatchdog');

    // Get list of scheduled tasks from Task API.
    $tasks = \core\task\manager::get_all_scheduled_tasks();

    // Get list of scheduled tasks from Database (as we need the task ID).
    $dbscheduledtasks = $DB->get_records('task_scheduled', null, 'id', 'classname, id', IGNORE_MISSING);
    $dbfailedtasks = $DB->get_records('tool_cronwatchdog', null, 'id', 'taskid, timestamp', IGNORE_MISSING);

    // Initialize failed tasks array.
    $failedtasks = array();

    // SECTION: Check if DB update is pending which blocks scheduled tasks from running.

    // Check if the administrator wanted us to do the upgrade delay check.
    if ($config->enableupgradedelay == true) {
        // Check if a Moodle upgrade is pending and because of that cron is blocked.
        if (moodle_needs_upgrading()) {
            $lastglobalcron = get_config('tool_task', 'lastcronstart');
            $upgraderundelay = floor((time() - $lastglobalcron));
            if ($upgraderundelay > $config->maxupgradedelay) {
                // Remember this fact for later use.
                $failedtasks['upgrade'] = get_string('error_maxupgradedelay', 'tool_cronwatchdog',
                        array('upgraderundelay' => $upgraderundelay)); // TODO: This will crash after merge.
            }
        }
    }

    // SECTION: Check if cron is running at all.

    // Check if the administrator wanted us to do the global cron delay check.
    if ($config->enableglobaldelay == true) {
        // Check if a Moodle global cron has not run for any reason recently and the reason is _not_ a pending upgrade.
        if (!key_exists('upgrade', $failedtasks)) { // TODO: This will crash after merge.
            $lastglobalcron = get_config('tool_task', 'lastcronstart');
            $globalrundelay = floor((time() - $lastglobalcron));
            if ($globalrundelay > $config->maxglobaldelay) {
                // Remember this fact for later use.
                $failedtasks['global'] = get_string('error_maxglobaldelay', 'tool_cronwatchdog',
                        array('globalrundelay' => $globalrundelay)); // TODO: This will crash after merge.
            }
        }
    }

    // SECTION: Check if scheduled tasks are running in general but have problems.

    // Check if the administrator wanted us to do one of the rundelay or faildelay checks.
    if ($config->enablerundelay == true || $config->enablefaildelay == true) {
        // Get list of scheduled tasks from Task API.
        $tasks = \core\task\manager::get_all_scheduled_tasks();

        // Get list of scheduled tasks from Database (as we need the task ID).
        $records = $DB->get_records('task_scheduled', null, 'id', 'id, classname', IGNORE_MISSING);

        // Process all scheduled tasks.
        foreach ($tasks as $task) {

            // Initialize the problems array.
            $problems = array();

            // Get the component of the task.
            $component = $task->get_component();

            // Do only if the plugin is enabled OR if the plugin tells the task manager
            // to run the task even if the plugin is disabled AND if the task is enabled.
            if ($plugininfo = \core_plugin_manager::instance()->get_plugin_info($component)) {
                if (($plugininfo->is_enabled() === true || $task->get_run_if_component_disabled())
                        && $task->get_disabled() == false) {

                    // Check if the administrator wanted us to do the faildelay check.
                    if ($config->enablefaildelay == true) {
                        // Check if the task has a faildelay set and if it exceeds our threshold.
                        $faildelay = $task->get_fail_delay();
                        if ($faildelay > $config->maxfaildelay) {
                            // Remember this problem for later use.
                            $problems[] = get_string('error_maxfaildelay', 'tool_cronwatchdog',
                                    array('taskname' => $task->get_name(), 'faildelay' => $faildelay));
                        }
                    }

                    // Check if the administrator wanted us to do the rundelay check.
                    if ($config->enablerundelay == true) {
                        // Check if the task should have run already and if the delay exceeds our threshold.
                        $rundelay = floor(($currenttime - $task->get_next_run_time()));
                        if ($rundelay > $config->maxrundelay) {
                            // Remember this problem for later use.
                            $problems[] = get_string('error_maxrundelay', 'tool_cronwatchdog',
                                    array('taskname' => $task->get_name(), 'rundelay' => $rundelay));
                        }
                    }

                    // If we have found any problems, remember this task in the list of failed tasks.
                    if (count($problems) > 0) {
                        $taskid = $dbscheduledtasks[ "\\" . get_class($task) ]->id;
                        $failedtasks[ $taskid ] = implode(',', $problems);
                    }
                }
            }
        }
    }

    // New failed tasks that should be added to the db.
    $failedtasksadd = array_diff_key($failedtasks, $dbfailedtasks);
    $numberfailed = count($failedtasksadd);
    if ($numberfailed > 0) {
        tool_cronwatchdog_notify($failedtasksadd, $numberfailed);
        tool_cronwatchdog_db_insert($failedtasks, $currenttime);
    }

    // And tasks that dont faile any more and should be deleted.
    $failedtasksreduce = array_diff_key($dbfailedtasks, $failedtasks);
    if (count($failedtasksreduce) > 0) {
        tool_cronwatchdog_db_delete(array_keys($failedtasksreduce));
    }
}

/**
 * Helper function to do the notification to the user.
 *
 * @param array $failedtasks
 * @param int $numberfailed
 * @param bool $echo
 */
function tool_cronwatchdog_notify($failedtasks, $numberfailed, $echo = false) {

    global $USER;

    // Compose the output, starting with an introduction and add all problems then.
    $output = get_string('message_statusproblems', 'tool_cronwatchdog') . PHP_EOL;

    foreach ($failedtasks as $task) {
        $output .= $task . PHP_EOL;
    }

    if ($echo == true) {
        echo $output;
    }

    // Compose the notification message.
    $message = new \core\message\message();
    $message->courseid = SITEID;
    $message->component = 'tool_cronwatchdog';
    $message->name = 'notify';
    $message->userto = $USER;
    $message->notification = 1;
    $message->userfrom = \core_user::get_noreply_user();
    $message->subject = get_string('message_subject', 'tool_cronwatchdog');
    $message->fullmessage = $output;
    $message->fullmessageformat = FORMAT_MOODLE;
    $message->fullmessagehtml = $output;
    $message->smallmessage = get_string('message_smallmessage', 'tool_cronwatchdog', array('tasks' => $numberfailed));
    $message->contexturl = new \moodle_url("/admin/tool/task/scheduledtasks.php");
    $message->contexturlname = get_string('scheduledtasks', 'tool_task');

    // Compose the notification message.
    message_send($message);
}

/**
 * Helper function to do the inserting of timestamps into the database table.
 *
 * @param array $tasks
 * @param int $currenttime
 */
function tool_cronwatchdog_db_insert($tasks, $currenttime) {
    global $DB;

    $table = array();

    foreach ($tasks as $key => $task) {
        $record = new stdClass();
        $record->taskid = $key;
        $record->timestamp = $currenttime;

        try {
            $DB->insert_record('tool_cronwatchdog', $record);
        } catch (Exception $error) {
            // Just ignore this exception for now.
            // We unset $task just for fun.
            unset($task);
        }
    }
}

/**
 * Helper function to do the deleting of timestamps from the database table.
 *
 * @param array $tasks
 */
function tool_cronwatchdog_db_delete($tasks) {
    global $DB;

    foreach ($tasks as $task) {
        $DB->delete_records('tool_cronwatchdog', array('taskid' => $task));
    }
}