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
 * Check for updates for Nolej modules in progress.
 *
 * @module      local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    var request = {
        methodname: 'local_nolej_get_library_updates',
        args: {}
    };

    var requestupdates = function() {
        Ajax.call([request])[0].done(function(data) {
            for (let i = 0, len = data.updates.length; i < len; i++) {

                // Show notification.
                Notification.addNotification({
                    message: data.updates[i].message,
                    type: data.updates[i].success ? 'success' : 'error'
                });

                // Update table row.
                let rowid = '#local_nolej_module_' + data.updates[i].id;
                $(rowid + ' .local_nolej_title').text(data.updates[i].title);
                $(rowid + ' .local_nolej_status').text(data.updates[i].status);
                $(rowid + ' .local_nolej_status_icon i').attr(
                    'class',
                    'fa fa-exclamation ' + (data.updates[i].success ? 'text-success' : 'text-danger')
                );
                $(rowid + ' .local_nolej_lastupdate').text(data.updates[i].lastupdate);
            }
        }).fail(Notification.exception);
    };

    return {
        init: function(interval) {
            requestupdates();
            setInterval(requestupdates, interval);
        }
    };
});
