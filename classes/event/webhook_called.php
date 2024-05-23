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
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\event;

defined('MOODLE_INTERNAL') || die();

class webhook_called extends \core\event\base
{
    protected function init()
    {
        $this->data['context'] = \context_system::instance();
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name()
    {
        return get_string('eventwebhookcalled', 'local_nolej');
    }

    public function get_description()
    {
        return $this->other['message'];
    }

    public function get_url()
    {
        if ($this->other['documentid'] == null) {
            return null;
        }
        return new \moodle_url(
            '/local/nolej/edit.php',
            ['documentid' => $this->other['documentid']]
        );
    }
}
