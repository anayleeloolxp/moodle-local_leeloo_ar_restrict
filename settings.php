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
 * Plugin administration pages are defined here.
 *
 * @package     local_leeloo_ar_restrict
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_leeloo_ar_restrict', get_string('setting_title', 'local_leeloo_ar_restrict'));

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_leeloo_ar_restrict/license',
        get_string('license', 'local_leeloo_ar_restrict'),
        get_string('license', 'local_leeloo_ar_restrict'),
        '0',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_leeloo_ar_restrict/vendorkey',
        get_string('vendorkey', 'local_leeloo_ar_restrict'),
        get_string('vendorkey', 'local_leeloo_ar_restrict'),
        '0',
        PARAM_TEXT
    ));
}
