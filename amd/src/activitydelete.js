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
 * Show a delete Nolej h5p activity modal
 *
 * @module      local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    ['jquery', 'core/modal_factory', 'core/str', 'core/modal_events', 'core/templates'],
    function($, ModalFactory, String, ModalEvents, Templates) {
        // Initialize single activity delete confirmation modal.
        var trigger = $('[data-action="delete"]');
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: String.get_string('deletecontent', 'core_contentbank'),
            preShowCallback: function(triggerElement, modal) {
                triggerElement = $(triggerElement);
                let urlparams = new URLSearchParams(window.location.search);
                let documentid = urlparams.get('documentid');
                modal.params = {
                    'activityid': triggerElement[0].getAttribute('data-activityid'),
                    'documentid': documentid,
                };
                let activityname = triggerElement[0].getAttribute('data-activityname');
                const body = String.get_string(
                    'deletecontentconfirm',
                    'core_contentbank',
                    {
                        name: activityname,
                    }
                );
                modal.setBody(body);
                modal.setSaveButtonText(String.get_string('delete'));
            }
        }, trigger)
            .done(function(modal) {
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    var deleteurl = new URL(M.cfg.wwwroot + '/local/nolej/activitiesdelete.php');
                    deleteurl.searchParams.append('activityid', modal.params.activityid);
                    deleteurl.searchParams.append('documentid', modal.params.documentid);
                    deleteurl.searchParams.append('contextid', M.cfg.contextid);
                    deleteurl.searchParams.append('sesskey', M.cfg.sesskey);
                    window.location.href = deleteurl.toString();
                });
            });

        // Initialize bulk activity delete confirmation modal.
        $('form[name="activitiesmanagement"] #formactionid').on('change', function() {
            const form = $('form[name="activitiesmanagement"]')[0];

            const checked = $('form[name="activitiesmanagement"] input.activitycheckbox:checked');
            if (checked.length <= 0) {
                // No activities selected.
                return;
            }

            switch ($(this).val()) {
                case '#delete':
                    ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        title: String.get_string('deletecontents', 'local_nolej'),
                    })
                        .then(function(modal) {
                            const activities = checked.closest('tr').find('a:first').map(function() {
                                return {
                                    name: $(this).text(),
                                };
                            }).get();
                            const body = Templates.render(
                                'local_nolej/activitiesdelete',
                                {
                                    activities,
                                }
                            );
                            modal.setBody(body);
                            modal.setSaveButtonText(String.get_string('delete'));

                            modal.getRoot().on(ModalEvents.save, function(e) {
                                e.preventDefault();
                                const deleteurl = new URL(M.cfg.wwwroot + '/local/nolej/activitiesdelete.php');
                                form.action = deleteurl;
                                form.submit();
                            });

                            modal.show();
                            return;
                        });
            }
        });
    }
);
