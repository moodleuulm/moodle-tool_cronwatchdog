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
 * Admin tool "Cron watchdog" - Task definition
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cronwatchdog\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/cronwatchdog/locallib.php');

/**
 * The tool_cronwatchdog check task class.
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check extends \core\task\scheduled_task {

    /**
     * Return localised task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcheck', 'tool_cronwatchdog');
    }

    /**
     * Execute scheduled task
     *
     * @return boolean
     */
    public function execute() {

        // Call helper function to do the checks and enable the outputting of the result.
        tool_cronwatchdog_check(true);

        // Return true to make sure that this task is always finished correctly.
        return true;
    }
}
