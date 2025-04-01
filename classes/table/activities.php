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

use DateTime;
use context;
use core_table\dynamic as dynamic_table;
use core_table\local\filter\filterset;
use core_user\output\status_field;
use moodle_url;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');

/**
 * Class for the displaying the generated activities table.
 */
class activities extends \table_sql {

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
        global $CFG, $OUTPUT, $PAGE;

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        // Select column for bulk actions.
        $mastercheckbox = new \core\output\checkbox_toggleall(
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

        $headers[] = get_string('uses', 'core_contentbank');
        $columns[] = 'usagecount';

        $headers[] = get_string('size', 'core_contentbank');
        $columns[] = 'size';

        $headers[] = get_string('actions');
        $columns[] = 'actions';

        $this->define_columns($columns);
        $this->define_headers($headers);

        // Make this table sorted by name by default.
        $this->sortable(true, 'name');
        $this->no_sorting('select');
        $this->no_sorting('usagecount');
        $this->no_sorting('size');
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

        $checkbox = new \core\output\checkbox_toggleall(
            'local_nolej_activities_management',
            false,
            [
                'classes' => 'activitycheckbox m-1',
                'id' => 'activity_' . $data->id,
                'name' => 'activityids[]',
                'checked' => false,
                'value' => $data->id,
                'label' => get_string('selectitem', 'moodle', fullname($data)),
                'labelclasses' => 'accesshide',
            ]
        );

        return $OUTPUT->render($checkbox);
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_actions($data) {
        global $OUTPUT;

        // Actions menu.
        $menu = new \action_menu();
        $menu->set_menu_trigger(get_string('actions'));

        $menu->add(
            new \action_menu_link(
                new moodle_url('#'),
                new \pix_icon('i/delete', 'core'),
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
        return \html_writer::link($data->link, $data->name);
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

        return get_string('unknown');
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

        return get_string('unknown');
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return string containing sql to use.
     */
    protected function get_sql($count = false): string {
        $select = $count ? 'COUNT(1)' : '*';

        $sql = "SELECT $select
                  FROM {local_nolej_h5p} n
            INNER JOIN {contentbank_content} c ON c.id = n.content_id
                 WHERE n.document_id = :documentid";

        if ($count) {
            return $sql;
        }

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
        $total = $DB->count_records_sql(
            $countsql,
            [
                'documentid' => $this->documentid,
            ]
        );

        $this->pagesize($pagesize, $total);

        $sql = $this->get_sql();
        $data = $DB->get_records_sql(
            $sql,
            [
                'documentid' => $this->documentid,
            ],
            $this->get_page_start(),
            $this->get_page_size()
        );

        $contentbank = new \core_contentbank\contentbank();

        foreach ($data as $element) {
            $content = $contentbank->get_content_from_id($element->content_id);
            $contenttype = $content->get_content_type_instance();

            $file = $content->get_file();
            $filesize = $file ? $file->get_filesize() : 0;
            $author = \core_user::get_user($content->get_content()->usercreated);
            $element->link = $contenttype->get_view_url($content);
            $element->icon = $contenttype->get_icon($content);
            $element->usagecount = count($content->get_uses());
            $element->bytes = $filesize;
            $element->size = display_size($filesize);
            $element->author = fullname($author);
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
