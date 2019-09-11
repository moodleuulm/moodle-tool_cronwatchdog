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
 * Admin tool "Cron watchdog" - Settings
 *
 * @package     tool_cronwatchdog
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // New settings page.
    $page = new admin_settingpage('tool_cronwatchdog', get_string('pluginname', 'tool_cronwatchdog', null, true));

    if ($ADMIN->fulltree) {
        // Create Check general settings heading.
        $page->add(new admin_setting_heading('tool_cronwatchdog/checkgeneralheading',
                new lang_string('setting_checkgeneralheading', 'tool_cronwatchdog'),
                ''));

        // Create Enable Poor Man's cron heading.
        $page->add(new admin_setting_configcheckbox('tool_cronwatchdog/enablepoormanscron',
                new lang_string('setting_enablepoormanscron', 'tool_cronwatchdog'),
                new lang_string('setting_enablepoormanscron_desc', 'tool_cronwatchdog'),
                0));

        // Create Poor Man's cron interval widget.
        $page->add(new admin_setting_configtext('tool_cronwatchdog/poormanscroninterval',
                new lang_string('setting_poormanscroninterval', 'tool_cronwatchdog'),
                new lang_string('setting_poormanscroninterval_desc', 'tool_cronwatchdog'),
                1,
                PARAM_INT));
        $page->hide_if('tool_cronwatchdog/poormanscroninterval',
                'tool_cronwatchdog/poormanscron', 'notchecked');

        // Create Check details settings heading.
        $page->add(new admin_setting_heading('tool_cronwatchdog/checkdetailsheading',
                new lang_string('setting_checkdetailsheading', 'tool_cronwatchdog'),
                ''));

        // Create Enable global cron delay check widget.
        $page->add(new admin_setting_configcheckbox('tool_cronwatchdog/enableglobaldelay',
                new lang_string('setting_enableglobaldelay', 'tool_cronwatchdog'),
                new lang_string('setting_enableglobaldelay_desc', 'tool_cronwatchdog'),
                1));

        // Create Maximum global cron delay check widget.
        $page->add(new admin_setting_configselect('tool_cronwatchdog/maxglobaldelay',
                new lang_string('setting_maxglobaldelay', 'tool_cronwatchdog'),
                new lang_string('setting_maxglobaldelay_desc', 'tool_cronwatchdog'),
                0,
                [0 => 0, 60 => 1, 120 => 2, 180 => 3, 240 => 4, 300 => 5])
        );
        $page->hide_if('tool_cronwatchdog/maxglobaldelay',
                'tool_cronwatchdog/enableglobaldelay', 'notchecked');

        // Create Enable upgrade delay check widget.
        $page->add(new admin_setting_configcheckbox('tool_cronwatchdog/enableupgradedelay',
                new lang_string('setting_enableupgradedelay', 'tool_cronwatchdog'),
                new lang_string('setting_enableupgradedelay_desc', 'tool_cronwatchdog'),
                1));

        // Create Maximum upgrade delay check widget.
        $page->add(new admin_setting_configselect('tool_cronwatchdog/maxupgradedelay',
                new lang_string('setting_maxupgradedelay', 'tool_cronwatchdog'),
                new lang_string('setting_maxupgradedelay_desc', 'tool_cronwatchdog'),
                0,
                [0 => 0, 60 => 1, 120 => 2, 180 => 3, 240 => 4, 300 => 5])
        );
        $page->hide_if('tool_cronwatchdog/maxupgradedelay',
                'tool_cronwatchdog/enableupgradedelay', 'notchecked');

        // Create Enable rundelay check widget.
        $page->add(new admin_setting_configcheckbox('tool_cronwatchdog/enablerundelay',
                new lang_string('setting_enablerundelay', 'tool_cronwatchdog'),
                new lang_string('setting_enablerundelay_desc', 'tool_cronwatchdog'),
                1));

        // Create Maximum rundelay widget.
        $page->add(new admin_setting_configselect('tool_cronwatchdog/maxrundelay',
                new lang_string('setting_maxrundelay', 'tool_cronwatchdog'),
                new lang_string('setting_maxrundelay_desc', 'tool_cronwatchdog'),
                0,
                [0 => 0, 60 => 1, 120 => 2, 180 => 3, 240 => 4, 300 => 5])
        );
        $page->hide_if('tool_cronwatchdog/maxrundelay',
                'tool_cronwatchdog/enablemaxrundelay', 'notchecked');

        // Create Enable faildelay check widget.
        $page->add(new admin_setting_configcheckbox('tool_cronwatchdog/enablefaildelay',
                new lang_string('setting_enablefaildelay', 'tool_cronwatchdog'),
                new lang_string('setting_enablefaildelay_desc', 'tool_cronwatchdog'),
                1));

        // Create Maximum faildelay widget.
        $options[0] = 0;
        $delay = 1;
        while ($delay <= 1440) {
            $options[$delay * 60] = $delay;
            $delay *= 2;
        }
        $options[1440 * 60] = 1440;
        $page->add(new admin_setting_configselect('tool_cronwatchdog/maxfaildelay',
                new lang_string('setting_maxfaildelay', 'tool_cronwatchdog'),
                new lang_string('setting_maxfaildelay_desc', 'tool_cronwatchdog'),
                0,
                $options)
        );
        $page->hide_if('tool_cronwatchdog/maxfaildelay',
                'tool_cronwatchdog/enablemaxfaildelay', 'notchecked');
        unset($delay, $options);
    }

    // Add settings page to navigation tree.
    $ADMIN->add('server', $page);
}
