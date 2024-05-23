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
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { // needs this condition or there is error on login page
    $settings = new admin_settingpage('local_nolej', get_string('pluginname', 'local_nolej'));
    $ADMIN->add('localplugins', $settings);

    $settingsgeneral->add(
        new admin_setting_heading(
            'local_nolej_api_key_info',
            '',
            get_string('apikeyhowto', 'local_nolej')
        )
    );

    $settings->add(
        new admin_setting_configpasswordunmask(
            'local_nolej/api_key',
            get_string('apikey', 'local_nolej'),
            get_string('apikeyinfo', 'local_nolej'),
            ''
        )
    );
}
