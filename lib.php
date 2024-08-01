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
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:ignore
defined('MOODLE_INTERNAL') || die();

/**
 * Add a link to the library in the navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param ?context $context The context instance
 */
function local_nolej_add_to_navigation($navigation, $context = null) {
    global $PAGE;

    if ($context === null) {
        $context = $PAGE->context;
    }

    // The context must exist at this point, otherwise it's weird.
    if (!has_capability('local/nolej:usenolej', $context)) {
        return;
    }

    // Adds an entry to the navigation.
    $navigation->add(
        get_string('library', 'local_nolej'),
        new moodle_url('/local/nolej/manage.php', ['contextid' => $context->id]),
        global_navigation::TYPE_SETTING,
        null,
        'nolejlibrary',
        new pix_icon('nolej', '', 'local_nolej')
    );
}

/**
 * Add a link to the library in the global navigation.
 *
 * @param global_navigation $navigation
 */
function local_nolej_extend_navigation(global_navigation $navigation) {
    local_nolej_add_to_navigation($navigation);
}

/**
 * Add a link to the library in the course category navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $context The context of the course category
 */
function local_nolej_extend_navigation_category_settings($navigation, $context) {
    local_nolej_add_to_navigation($navigation, $context);
}

/**
 * Add a link to the library in the course navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 */
function local_nolej_extend_navigation_course($navigation, $course, $context) {
    local_nolej_add_to_navigation($navigation, $context);
}

/**
 * Add a link to the library in the frontpage navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 */
function local_nolej_extend_navigation_frontpage($navigation, $course, $context) {
    local_nolej_add_to_navigation($navigation, $context);
}
