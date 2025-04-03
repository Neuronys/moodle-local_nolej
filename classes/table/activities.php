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
 * Nolej activities management table.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\table;

defined('MOODLE_INTERNAL') || die;

use action_menu;
use action_menu_link;
use core_contentbank\contentbank;
use core_user;
use core_table\local\filter\filterset;
use core\output\checkbox_toggleall;
use html_writer;
use moodle_url;
use pix_icon;
use table_sql;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the generated activities table.
 */
class activities extends table_sql {

    /**
     * @var string $documentid Document ID
     */
    protected $documentid;

    /**
     * Sets up the table.
     *
     * @param string $documentid The id of the course.
     */
    public function __construct($documentid) {
        parent::__construct('local_nolej_activities_management');

        $this->documentid = $documentid;
    }

    /**
     * Render the participants table.
     *
     * @param int $pagesize Size of page for paginated displayed table.
     * @param bool $useinitialsbar Whether to use the initials bar which will only
     * be used if there is a fullname column defined.
     * @param string $downloadhelpbutton
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        global $OUTPUT;

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        // Select column for bulk actions.
        $mastercheckbox = new checkbox_toggleall(
            'local_nolej_activities_management',
            true,
            [
                'id' => 'activity',
                'name' => 'activity',
                'label' => get_string('selectall'),
                'labelclasses' => 'sr-only',
                'classes' => 'm-1',
                'checked' => false,
            ]
        );
        $headers[] = $OUTPUT->render($mastercheckbox);
        $columns[] = 'select';

        $headers[] = get_string('contentname', 'core_contentbank');
        $columns[] = 'name';

        $headers[] = get_string('type', 'core_contentbank');
        $columns[] = 'type';

        $headers[] = get_string('timecreated', 'core_contentbank');
        $columns[] = 'timecreated';

        $headers[] = get_string('lastmodified', 'core_contentbank');
        $columns[] = 'timemodified';

        $headers[] = get_string('size', 'core_contentbank');
        $columns[] = 'size';

        $headers[] = get_string('uses', 'core_contentbank');
        $columns[] = 'usagecount';

        $headers[] = get_string('actions');
        $columns[] = 'actions';

        $this->define_columns($columns);
        $this->define_headers($headers);

        // Make this table sorted by name by default.
        $this->sortable(true, 'name');
        $this->no_sorting('select');
        $this->no_sorting('usagecount');
        $this->no_sorting('actions');

        $this->set_default_per_page(20);

        $this->set_attribute('id', 'local_nolej_activities_management');

        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
    }

    /**
     * Generate the select column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_select($data) {
        global $OUTPUT;

        $checkbox = new checkbox_toggleall(
            'local_nolej_activities_management',
            false,
            [
                'classes' => 'activitycheckbox m-1',
                'id' => 'activity_' . $data->content_id,
                'name' => 'activityids[]',
                'checked' => false,
                'value' => $data->content_id,
                'label' => get_string('selectitem', 'moodle', $data->name),
                'labelclasses' => 'accesshide',
            ]
        );

        return $OUTPUT->render($checkbox);
    }

    /**
     * Generate the actions column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_actions($data) {
        global $OUTPUT;

        // Actions menu.
        $menu = new action_menu();
        $menu->set_menu_trigger(get_string('actions'));

        $menu->add(
            new action_menu_link(
                new moodle_url('#'),
                new pix_icon('i/delete', 'core'),
                get_string('delete'),
                false,
                [
                    'data-action' => 'delete',
                    'data-activityid' => $data->content_id,
                    'data-activityname' => $data->name,
                ]
            )
        );

        return $OUTPUT->render($menu);
    }

    /**
     * Generate the title link.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_name($data) {
        return html_writer::link($data->link, $data->name);
    }

    /**
     * Generate the time created column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_type($data) {
        return get_string('activities' . $data->type, 'local_nolej');
    }

    /**
     * Generate the time created column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_timecreated($data) {
        if ($data->timecreated) {
            return userdate($data->timecreated);
        }

        return get_string('statusunknown');
    }

    /**
     * Generate the time modified column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_timemodified($data) {
        if ($data->timemodified) {
            return userdate($data->timemodified);
        }

        return get_string('statusunknown');
    }

    /**
     * Generate the size column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_size($data) {
        return display_size($data->size);
    }

    /**
     * Generate the activity's usage count column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_usagecount($data) {
        if ($data->usagecount > 0) {
            return $data->usagecount;
        }
        return 0;
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return string containing sql to use.
     */
    protected function get_sql($count = false): string {
        $contentbankfiles = "SELECT itemid, filesize AS size, filename AS name,
                                    timecreated, timemodified
                               FROM {files}
                              WHERE component = 'contentbank'
                                AND filesize > 0";

        if ($count) {
            $sql = "SELECT COUNT(1)
                      FROM {local_nolej_h5p} AS h
                INNER JOIN ($contentbankfiles) AS f ON f.itemid = h.content_id
                     WHERE h.document_id = :documentid";
            return $sql;
        }

        $sql = "SELECT h.content_id, h.type, f.size, f.name, f.timecreated, f.timemodified
                  FROM {local_nolej_h5p} AS h
            INNER JOIN ($contentbankfiles) AS f ON f.itemid = h.content_id
                 WHERE h.document_id = :documentid";

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql .= " ORDER BY $sort";
        }

        return $sql;
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $countsql = $this->get_sql(true);
        $total = $DB->count_records_sql($countsql, ['documentid' => $this->documentid]);

        $this->pagesize($pagesize, $total);

        $sql = $this->get_sql();
        $data = $DB->get_records_sql(
            $sql,
            ['documentid' => $this->documentid],
            $this->get_page_start(),
            $this->get_page_size()
        );

        $contentbank = new contentbank();

        foreach ($data as $element) {
            $content = $contentbank->get_content_from_id($element->content_id);
            $contenttype = $content->get_content_type_instance();

            $element->link = $contenttype->get_view_url($content);
            $element->icon = $contenttype->get_icon($content);
            $element->usagecount = count($content->get_uses());
        }

        $this->rawdata = $data;

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }

    /**
     * Override the table show_hide_link to not show for select column.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        if ($index > 0) {
            return parent::show_hide_link($column, $index);
        }
        return '';
    }
}
