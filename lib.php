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
 * Function to get Leeloo Install
 *
 * @return string leeloo url
 */
function local_leeloo_ar_restrict_get_leelooinstall() {

    global $SESSION;

    if (isset($SESSION->arresleelooinstall)) {
        return $SESSION->arresleelooinstall;
    }

    $leeloolxplicense = get_config('local_leeloo_ar_restrict')->license;

    $url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
    $postdata = [
        'license_key' => $leeloolxplicense,
    ];

    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );

    if (!$output = $curl->post($url, $postdata, $options)) {
        $arresleelooinstallurl = 'no';
        $SESSION->arresleelooinstall = $arresleelooinstallurl;
    }

    $infoteamnio = json_decode($output);
    if ($infoteamnio->status != 'false') {
        $arresleelooinstallurl = $infoteamnio->data->install_url;
        $SESSION->arresleelooinstall = $arresleelooinstallurl;
    } else {
        $arresleelooinstallurl = 'no';
        $SESSION->arresleelooinstall = $arresleelooinstallurl;
    }

    return $arresleelooinstallurl;
}

/**
 * HTML hook to add the restrictions on unpaid A/R.
 */
function local_leeloo_ar_restrict_before_standard_top_of_body_html() {

    global $USER;
    global $PAGE;
    global $DB;
    global $CFG;
    global $SESSION;

    @$useremail = $USER->email;

    if ($useremail != '' && $useremail != 'root@localhost' && !is_siteadmin()) {

        $leeloolxpurl = local_leeloo_ar_restrict_get_leelooinstall();

        if ($leeloolxpurl == 'no') {
            return true;
        }

        $PAGE->requires->js(new moodle_url('/local/leeloo_ar_restrict/js/custom.js'));

        $cm = $PAGE->cm;
        if (isset($cm->id) && isset($cm->id) != '') {
            $leelooarsync = $DB->get_record_sql('SELECT * FROM {tool_leeloo_ar_sync} WHERE enabled = ? AND courseid = ?', [1, $cm->id]);
            if ($leelooarsync) {
                $userid = $USER->id;
                $leelooarpurchased = $DB->get_record_sql('SELECT * FROM {tool_leeloo_ar_sync_restrict} WHERE userid = ? AND arid = ?', [$userid, $cm->id]);
                if (!$leelooarpurchased) {
                    $activityname = $cm->get_formatted_name();
                    $productid = $leelooarsync->productid;

                    $productalias = $leelooarsync->product_alias;
                    $urlalias = $productid . '-' . $productalias;

                    $jsessionid = $SESSION->jsession_id;

                    $activityrecord = $PAGE->activityrecord;

                    $PAGE->requires->js(new moodle_url('/local/leeloo_ar_restrict/js/custom.js'));

                    $buytext = get_string('buy', 'local_leeloo_ar_restrict');
                    $alink = "https://leeloolxp.com/products-listing/product/$urlalias?session_id=$jsessionid";

                    $leeloodiv = "<div class='leeloo_ar_div' id='leeloo_ar_div_$productid'>";
                    $leeloodiv .= "<h1 class='leeloo_ar_price'>".get_string('paidar', 'local_leeloo_ar_restrict')."</h1>";
                    $leeloodiv .= "<a class='leeloo_ar_cert' id='leeloo_ar_cert_$productid' data-toggle='modal' data-target='#leelooModal_$productid' href='$alink'>$buytext";
                    $leeloodiv .= "</a></div>";

                    $regincss = "<style>body #region-main,body #region-main.has-blocks{display:none;}</style>";
                    $framcecss = "<style>.leeloo_ar_frame {width: 100%;height: 50vh;border: 0;}</style>";
                    $headbutton = "<h4 class='modal-title'>$activityname</h4><button type='button' class='close' data-dismiss='modal'>&times;</button>";

                    $aria = "aria-labelledby='gridSystemModalLabel'";

                    $leeloomodal = "";
                    $leeloomodal .= "<div class='modal fade leeloo_paid_ar_modal' tabindex='-1' $aria id='leelooModal_$productid' role='dialog' style='max-width: 100%;'>";
                    $leeloomodal .= "<div class='modal-dialog'><div class='modal-content'>";
                    $leeloomodal .= "<div class='modal-header'>$headbutton</div><div class='modal-body'></div></div></div></div>$framcecss $regincss";

                    $activityrecord->content = '';
                    $activityrecord->intro = '';

                    $PAGE->set_activity_record($activityrecord);

                    $regionjs = 'document.getElementById("region-main").style.display = "none";';
                    $js2 = $regionjs . 'document.getElementById("region-main").insertAdjacentHTML("afterend", "' . $leeloodiv . $leeloomodal . '")';

                    $PAGE->requires->js_init_code("$js2");
                }
            }
        }
    }
}
