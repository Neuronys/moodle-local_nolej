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
 * Plugin upgrade
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool result.
 */
function xmldb_local_nolej_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    // Update user_id field precision.
    if ($oldversion < 2024061301) {

        // Update user_id field precision for table nolej_module.
        $table = new xmldb_table('nolej_module');
        $field = new xmldb_field('user_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Update user_id field precision for table nolej_activity.
        $table = new xmldb_table('nolej_activity');
        $field = new xmldb_field('user_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Nolej savepoint reached.
        upgrade_plugin_savepoint(true, 2024061301, 'local', 'nolej');
    }

    // Rename tables and re-define foreign keys.
    if ($oldversion < 2024061302) {

        // Rename table nolej_module to local_nolej_module.
        $table = new xmldb_table('nolej_module');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'local_nolej_module');
        }

        // Rename table nolej_activity to local_nolej_activity.
        $table = new xmldb_table('nolej_activity');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'local_nolej_activity');
        }

        // Rename table nolej_h5p to local_nolej_h5p.
        $table = new xmldb_table('nolej_h5p');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'local_nolej_h5p');
        }

        // Define key document_id (foreign) to be dropped from local_nolej_activity.
        $table = new xmldb_table('local_nolej_activity');
        $key = new xmldb_key('document_id', XMLDB_KEY_FOREIGN, ['document_id'], 'local_nolej_module', ['document_id']);

        // Re-define foreign keys.
        if ($dbman->table_exists($table)) {
            $dbman->drop_key($table, $key);
            $dbman->add_key($table, $key);
        }

        // Define key document_id (foreign) to be dropped from local_nolej_h5p.
        $table = new xmldb_table('local_nolej_h5p');
        $key = new xmldb_key('document_id', XMLDB_KEY_FOREIGN, ['document_id'], 'local_nolej_module', ['document_id']);

        // Re-define foreign keys.
        if ($dbman->table_exists($table)) {
            $dbman->drop_key($table, $key);
            $dbman->add_key($table, $key);
        }

        // Update the version number to the current version.
        upgrade_plugin_savepoint(true, 2024061302, 'local', 'nolej');
    }

    return true;
}
