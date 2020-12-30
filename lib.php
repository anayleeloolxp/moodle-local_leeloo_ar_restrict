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
 * Libary file .
 *
 * @package     local_leeloo_ar_restrict
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(dirname(__DIR__)) . '/config.php');

/**
 * HTML hook to add the restrictions on unpaid A/R.
 */
function local_leeloo_ar_restrict_before_standard_top_of_body_html() {

    global $USER;
    global $PAGE;
    global $DB;
    global $CFG;
    global $SESSION;

    $useremail = $USER->email;

    if ($useremail != '' && $useremail != 'root@localhost' && !is_siteadmin()) {
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/leeloo_ar_restrict/js/custom.js'));

        $cm = $PAGE->cm;
        if ($cm->id) {
            $leelooarsync = $DB->get_record_sql('SELECT * FROM {tool_leeloo_ar_sync} WHERE enabled = 1 AND courseid = ' . $cm->id);
            if ($leelooarsync) {
                $userid = $USER->id;
                $leelooarpurchased = $DB->get_record_sql('SELECT * FROM {tool_leeloo_ar_sync_restrict} WHERE userid = "' . $userid . '" AND arid = "' . $cm->id . '"');
                if (!$leelooarpurchased) {
                    $activityname = $cm->get_formatted_name();
                    $productid = $leelooarsync->productid;

                    $productalias = $leelooarsync->product_alias;
                    $urlalias = $productid . '-' . $productalias;

                    $jsessionid = $SESSION->jsession_id;

                    $activityrecord = $PAGE->activityrecord;

                    $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/leeloo_ar_restrict/js/custom.js'));

                    $buytext = get_string('buy', 'local_leeloo_ar_restrict');

                    $leeloodiv = "<div class='leeloo_ar_div' id='leeloo_ar_div_$productid'><h1 class='leeloo_ar_price'>Paid Activity.</h1><a class='leeloo_ar_cert' id='leeloo_ar_cert_$productid' data-toggle='modal' data-target='#leelooModal_$productid' href='https://leeloolxp.com/products-listing/product/$urlalias?session_id=$jsessionid'>$buytext</a></div>";

                    $leeloomodal = "<div class='modal fade leeloo_paid_ar_modal' tabindex='-1' aria-labelledby='gridSystemModalLabel' id='leelooModal_$productid' role='dialog' style='max-width: 90%;'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><h4 class='modal-title'>$activityname</h4><button type='button' class='close' data-dismiss='modal'>&times;</button></div><div class='modal-body'></div></div></div></div><style>.leeloo_ar_frame {width: 100%;height: 50vh;border: 0;}</style><style>body #region-main,body #region-main.has-blocks{display:none;}</style>";

                    $activityrecord->content = '';
                    $activityrecord->intro = '';

                    $PAGE->set_activity_record($activityrecord);

                    $js2 = 'document.getElementById("region-main").style.display = "none";
                    document.getElementById("region-main").insertAdjacentHTML("afterend", "' . $leeloodiv . $leeloomodal . '")';

                    $PAGE->requires->js_init_code("$js2");
                }
            }
        }
    }
}
