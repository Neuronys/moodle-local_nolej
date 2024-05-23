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
 * Defines various library functions.
 *
 * @package     local_nolej
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add a link to the library in the global navigation.
 */
function local_nolej_extend_navigation(global_navigation $navigation)
{
    if (!has_capability('local/nolej:usenolej', context_system::instance())) {
        return;
    }

    $node = $navigation->add(
        get_string('library', 'local_nolej'),
        new moodle_url('/local/nolej/manage.php'),
        global_navigation::TYPE_SETTING,
        null,
        'nolej_library',
        new pix_icon('nolej', '', 'local_nolej')
    );
}
