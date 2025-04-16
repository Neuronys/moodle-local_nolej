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
 * Show/hide elements depending on selected type.
 *
 * @module      local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    // Editor initially hidden
    $('#fitem_id_sourcetext').css('display', 'none');

    // URL help message initially hidden.
    $('#fitem_id_sourceurldesc').css('display', 'none');

    // Source type changed.
    $('input[name="sourcetype"]').on('change', function() {
        // Display editor iff text type is selected.
        var istext = $(this).is(':checked') && $(this).val() == 'text';
        $('#fitem_id_sourcetext').css('display', istext ? '' : 'none');

        // Display URL help message iff web type is selected.
        var isweb = $(this).is(':checked') && $(this).val() == 'web';
        $('#fitem_id_sourceurldesc').css('display', isweb ? '' : 'none');
    });
});
