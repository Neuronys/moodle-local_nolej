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
 * To be replaced or not by something you like.
 *
 * @package     local_nolej
 * @copyright   2024 E-learning Touch' <contact@elearningtouch.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej;

use context;
use moodle_url;
use navigation_node;
use core_course_category;

/**
 * Utils class.
 */
class utils {

    /**
     *  Retrieves the context instance and related course (if available), from a context ID.
     *
     * @param int|context $contextorid The context instance or its ID.
     * @param bool $courseidonly Return the course ID only instead of its data from the database.
     */
    public static function get_info_from_context($contextorid, bool $courseidonly = false) {
        global $DB;

        // Get the context instance from its id.
        $context = is_int($contextorid) ? context::instance_by_id($contextorid) : $contextorid;
        $course = null;

        // If the context is a course context or a sub-context of a course.
        if (($coursecontext = $context->get_course_context(false)) !== false) {
            // We define the context as being the course context. We don't want to use sub-contexts here.
            $context = $coursecontext;
            // Retrieve the course data.
            $course = $courseidonly
                ? $context->instanceid
                : $DB->get_record('course', ['id' => $coursecontext->instanceid], '*', MUST_EXIST);
        }

        // Return info.
        return [$context, $course];
    }

    /**
     * Setup the current page with current context and course info.
     * Must be called before using the page context.
     *
     * @param context $context The current context instance.
     * @param object|null $course The current course if any.
     * @param string $contextidkey The key to use/check in the current page URL for the context ID.
     */
    public static function page_setup(context $context, ?object $course = null, string $contextidkey = 'contextid') {
        global $PAGE;

        // We only define the context of the page if the course is null, because "in theory", require_login must b
        // called first with the course, which set the page course and context for us.
        // Cannot check with $PAGE->context due to debbuging.
        if ($course === null) {
            $PAGE->set_context($context);
        }

        // If the context ID parameter is missing from the page URL, we add it.
        if ($PAGE->url->get_param($contextidkey) === null) {
            $PAGE->url->param($contextidkey, $PAGE->context->id);
        }

        // Setup the page with additional properties.
        if ($PAGE->context->contextlevel === CONTEXT_COURSECAT) {
            core_course_category::page_setup();
        }
        $PAGE->set_heading($PAGE->context->get_context_name());

        // Set the library node active.
        if ($librarynode = $PAGE->settingsnav->find('nolejlibrary', navigation_node::TYPE_SETTING)) {
            $PAGE->navigation->override_active_url($librarynode->action());
            $librarynode->make_active();
        }
    }
}
