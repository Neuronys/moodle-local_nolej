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
 * Nolej modules management table.
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
use local_nolej\module;
use moodle_url;
use pix_icon;
use table_sql;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the generated activities table.
 */
class modules extends table_sql {

    /**
     * @var int $contextid The context id.
     */
    protected $contextid;

    /**
     * Sets up the table.
     *
     * @param int $contextid
     */
    public function __construct($contextid) {
        parent::__construct('local_nolej_modules_management');

        $this->contextid = $contextid;
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
        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('title', 'local_nolej');
        $columns[] = 'title';

        if (is_siteadmin()) {
            $headers[] = get_string('author', 'core_contentbank');
            $columns[] = 'user_id';
        }

        $headers[] = get_string('created', 'local_nolej');
        $columns[] = 'created';

        $headers[] = get_string('lastupdate', 'local_nolej');
        $columns[] = 'lastupdate';

        $headers[] = get_string('status', 'local_nolej');
        $columns[] = 'status';

        $headers[] = get_string('totalsize', 'local_nolej');
        $columns[] = 'size';

        $headers[] = get_string('activitiescount', 'local_nolej');
        $columns[] = 'activities';

        $headers[] = get_string('unusedactivities', 'local_nolej');
        $columns[] = 'unused';

        $headers[] = get_string('actions');
        $columns[] = 'actions';

        $this->define_columns($columns);
        $this->define_headers($headers);

        // Make this table sorted by name by default.
        $this->sortable(true, 'lastupdate', SORT_DESC);
        $this->no_sorting('unused');
        $this->no_sorting('actions');

        $this->set_default_per_page(20);

        $this->set_attribute('id', 'local_nolej_modules_management');

        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
    }

    /**
     * Generate the actions column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_actions($data) {
        return isset($data->link) ? html_writer::link($data->link, get_string('manageactivities', 'local_nolej')) : '';
    }

    /**
     * Generate the title link.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_title($data) {
        return $data->title;
    }

    /**
     * Generate the author name.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_user_id($data) {
        return fullname(core_user::get_user($data->user_id));
    }

    /**
     * Generate the time created column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_created($data) {
        if ($data->created) {
            return userdate($data->created);
        }

        return get_string('statusunknown');
    }

    /**
     * Generate the last update column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_lastupdate($data) {
        if ($data->lastupdate) {
            return userdate($data->lastupdate);
        }

        return get_string('statusunknown');
    }

    /**
     * Generate the status column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_status($data) {
        return module::getstatusname((int) $data->status);
    }

    /**
     * Generate the total size column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_size($data) {
        return display_size($data->size);
    }

    /**
     * Generate the activities count column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_activities($data) {
        if ($data->activities > 0) {
            return $data->activities;
        }
        return '-';
    }

    /**
     * Generate the unused activities count column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_unused($data) {
        if ($data->unused > 0) {
            return $data->unused;
        }
        return $data->activities > 0
            ? 0 // All activities are used.
            : '-'; // Not applicable.
    }

    /**
     * Builds the SQL query.
     *
     * @param bool $count When true, return the count SQL.
     * @return string containing sql to use.
     */
    protected function get_sql($count = false): string {
        if ($count) {
            $sql = "SELECT COUNT(1)
                      FROM {local_nolej_module}
                     WHERE status >= 6";

            // User check.
            if (!is_siteadmin()) {
                $sql .= " AND user_id = :userid";
            }

            return $sql;
        }

        $contentbankfiles = "SELECT itemid, filesize
                               FROM {files}
                              WHERE component = 'contentbank'
                                AND filesize > 0";

        $groupedactivities = "SELECT h.document_id, COUNT(h.document_id) AS activities,
                                     MAX(h.tstamp) AS lastupdate, SUM(f.filesize) AS size,
                                     GROUP_CONCAT(DISTINCT f.itemid SEPARATOR ';') AS itemids
                                FROM {local_nolej_h5p} h
                          INNER JOIN ($contentbankfiles) AS f ON f.itemid = h.content_id
                            GROUP BY h.document_id";

        $sql = "SELECT m.title, m.document_id, m.user_id, m.status, m.tstamp AS created,
                       a.lastupdate, a.activities, IFNULL(a.size, 0) as size, a.itemids
                  FROM {local_nolej_module} AS m
             LEFT JOIN ($groupedactivities) AS a ON a.document_id = m.document_id
                 WHERE m.status >= 6";

        // User check.
        if (!is_siteadmin()) {
            $sql .= " AND user_id = :userid";
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
        global $DB, $USER;

        $countsql = $this->get_sql(true);
        $total = $DB->count_records_sql($countsql, ['userid' => $USER->id]);

        $this->pagesize($pagesize, $total);

        $sql = $this->get_sql();
        $data = $DB->get_records_sql(
            $sql,
            ['userid' => $USER->id],
            $this->get_page_start(),
            $this->get_page_size()
        );

        $contentbank = new contentbank();

        foreach ($data as $element) {
            $element->unused = 0;

            if ($element->itemids == null) {
                continue;
            }

            $managelink = new moodle_url('/local/nolej/manage.php', [
                'contextid' => $this->contextid,
                'documentid' => $element->document_id,
            ]);
            $element->link = $managelink->out(false);

            $itemids = explode(';', $element->itemids);
            foreach ($itemids as $itemid) {
                $content = $contentbank->get_content_from_id($itemid);
                if (empty($content->get_uses())) {
                    $element->unused += 1;
                }
            }
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
