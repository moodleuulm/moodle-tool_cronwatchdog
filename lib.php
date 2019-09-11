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
 * Admin tool "Cron watchdog" - Library
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Use before_footer callback function to implement Poor Man's Cron Functionality.
 */
function tool_cronwatchdog_before_footer() {
    global $CFG;

    // Get config.
    $config = get_config('tool_cronwatchdog');

    // Do only if the plugin config is already set and if Poor Man's Cron is enabled.
    if (isset($config->enablepoormanscron) && $config->enablepoormanscron == true) {

        // Do only if it's time to run again.
        if ($config->poormanscroninterval > 0 &&
                $config->poormanscronlastrun < time() - $config->poormanscroninterval) {
            // Require locallib.php only if needed.
            require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/cronwatchdog/locallib.php');

            // Run check.
            tool_cronwatchdog_check(false);

            // Update timestamp of last run.
            set_config('poormanscronlastrun', time(), 'tool_cronwatchdog');
        }
    }
}