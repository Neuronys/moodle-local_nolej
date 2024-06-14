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
 * Webhook has been called event.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\event;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * The webhook_called event class.
 */
class webhook_called extends \core\event\base {

    /**
     * Init the event
     */
    protected function init()
    {
        $this->context = \context_system::instance();
        $this->data['crud'] = 'c'; // Create, Read, Update, Delete.
        $this->data['edulevel'] = \core\event\base::LEVEL_OTHER;
    }

    /**
     * Get the localised event name
     * @return string
     */
    public static function get_name()
    {
        return get_string('eventwebhookcalled', 'local_nolej');
    }

    /**
     * Return data received.
     * @return array
     */
    public function get_description()
    {
        return $this->other['message'];
    }

    /**
     * Url to Nolej module
     * @return ?moodle_url
     */
    public function get_url()
    {
        if ($this->other['documentid'] == null) {
            return null;
        }
        return new moodle_url(
            '/local/nolej/edit.php',
            ['documentid' => $this->other['documentid']]
        );
    }
}
