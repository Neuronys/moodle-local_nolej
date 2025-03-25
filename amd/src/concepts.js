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
 * Hide concept details when disabled.
 *
 * @module      local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    // Find all concept enable selectors.
    $('[id^="id_concept_"][id$="_enable"').each(function() {
        const id = $(this).attr('id');

        // Get concept id from element id.
        const match = id.match(/^id_concept_([a-z0-9-]+)_enable$/);
        if (!match) {
            return;
        }

        const conceptid = match[1];

        if ($(this).val() == '0') {
            // Hide concept details.
            $('#fitem_id_concept_' + conceptid + '_definition').css('display', 'none');
            $('#fitem_id_concept_' + conceptid + '_use_for_gaming').css('display', 'none');
            $('#fgroup_id_concept_' + conceptid + '_games').css('display', 'none');
            $('#fitem_id_concept_' + conceptid + '_use_for_practice').css('display', 'none');
        }

        $(this).on('change', function() {
            // Hide concept details if concept not enabled.
            const display = $(this).val() == '0' ? 'none' : '';
            $('#fitem_id_concept_' + conceptid + '_definition').css('display', display);
            $('#fitem_id_concept_' + conceptid + '_use_for_gaming').css('display', display);
            $('#fgroup_id_concept_' + conceptid + '_games').css('display', display);
            $('#fitem_id_concept_' + conceptid + '_use_for_practice').css('display', display);
        });
    });
});
