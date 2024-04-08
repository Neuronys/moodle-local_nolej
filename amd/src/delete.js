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
 * Show a delete Nolej module modal
 *
 * @module      local_nolej
 * @author      2023 Vincenzo Padula <vincenzo@oc-group.eu>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'core/str', 'core/modal_events'], function ($, ModalFactory, String, ModalEvents) {
    var trigger = $('.local_nolej_delete');
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: String.get_string('deletemodule', 'local_nolej'),
        body: String.get_string('deletemoduledescription', 'local_nolej'),
        preShowCallback: function (triggerElement, modal) {
            var triggerElement = $(triggerElement);
            modal.params = {
                'documentid': triggerElement[0].getAttribute('data-documentid')
            };
            modal.setSaveButtonText(String.get_string('deletemodule', 'local_nolej'));
        }
    }, trigger)
        .done(function (modal) {
            modal.getRoot().on(ModalEvents.save, function (e) {
                e.preventDefault();
                var documentid = modal.params.documentid;
                window.location.href = M.cfg.wwwroot + '/local/nolej/delete.php?documentid=' + documentid;
            });
        });
});
