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
 * Add config page to admin menu.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { // Needs this condition or there is error on login page.
    $settings = new admin_settingpage('local_nolej', get_string('pluginname', 'local_nolej'));
    $ADMIN->add('localplugins', $settings);

    // API Key info.
    $settings->add(
        new admin_setting_heading(
            'local_nolej_api_key_info',
            '',
            get_string('apikeyhowto', 'local_nolej')
        )
    );

    // API Key.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'local_nolej/api_key',
            get_string('apikey', 'local_nolej'),
            get_string('apikeyinfo', 'local_nolej'),
            ''
        )
    );

    // Library polling interval.
    $settings->add(
        new admin_setting_configtext(
            'local_nolej/pollinginterval',
            get_string('pollinginterval', 'local_nolej'),
            get_string('pollingintervalinfo', 'local_nolej'),
            '30',
            '/^[1-9][0-9]*$/' // Positive integer, 1 minimum (i.e. 1 second).
        )
    );

    // Context where to save h5p activities.
    $settings->add(
        new admin_setting_configselect(
            'local_nolej/storagecontext',
            get_string('storagecontext', 'local_nolej'),
            get_string('storagecontextinfo', 'local_nolej'),
            'coursecontext',
            [
                'coursecontext' => get_string('storagecontextcourse', 'local_nolej'),
                'currentcontext' => get_string('storagecontextcurrent', 'local_nolej'),
                'nolejcontext' => get_string('storagecontextnolej', 'local_nolej'),
            ]
        )
    );
}
