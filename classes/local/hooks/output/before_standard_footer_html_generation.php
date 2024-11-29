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

namespace tool_stackui\local\hooks\output;

/**
 * Hook callbacks for tool_stackui
 *
 * @package    tool_stackui
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_footer_html_generation {

    /**
     * Output items at the end of pages
     * @return void
     * @package tool_stackui
     */
    public static function callback(\core\hook\output\before_standard_footer_html_generation $hook): void {
        global $DB, $OUTPUT;

        if (! get_config('tool_stackui', 'enabled')) {
            return;
        }
        if (!self::in_uicohort()) {
            return;
        }

        global $PAGE;
        $pagetype = $PAGE->pagetype;
        if ($pagetype !== "question-type-stack") {
            return;
        }

        $content = "
        <div id='showhide' class='col-md-9 d-flex flex-wrap align-items-start felement'>
            <div class='row'>
                <button class='btn btn-secondary' id='id_showall'>Show all</button>&nbsp;&nbsp;
                <button class='btn btn-secondary' id='id_simplify'>Simplify</button>
            </div>
        </div>
        ";

        $content .= "<script>

        const btnShowAll = document.getElementById('id_showall');
        const btnSimplify = document.getElementById('id_simplify');

        const header = document.getElementById('fitem_id_name');

        btnShowAll.addEventListener('click', function(event) {
        window.location.href = window.location.href;
            event.preventDefault();

            const url = new URL(window.location.href);
            url.searchParams.append('showall', 'true');
            window.location.href = url.href;
        });

        btnSimplify.addEventListener('click', function(event) {
        window.location.href = window.location.href;
            event.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.append('showall', 'false');
            window.location.href = url.href;
        });
        function insertAfter(referenceNode, newNode) {
            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        }

        insertAfter(header, showhide);
        </script>";
        $showall = optional_param('showall', '', PARAM_TEXT);

        if ($showall !== 'true') {
            $content .= self::hide_elements();
        }
        $hook->add_html($content);

    }
    /**
     * Hide elements with javascript
     *
     * @return string
     */
    public static function hide_elements(): string {
        global $DB, $OUTPUT;
        $config = get_config('tool_stackui', 'elementstohide');
        $array = explode(',', $config);
        $trimmedarray = array_map('trim', $array);

        $tohide = array_filter($trimmedarray, function($value) {
            return $value !== ''; // Remove empty strings only.
        });
        $content = '<style>';
        foreach ($tohide as $element) {
            $content .= PHP_EOL;
            $content .= '#'.$element. '{'.PHP_EOL;
            $content .= 'display:none;'.PHP_EOL;
            $content .= '}'.PHP_EOL;
        }

        $content .= '</style>';
        // The Fix dollars elements are in a different format to other elements.
        if (in_array('id_fixdollars', $tohide)) {
            $content .= "<script>
            const element = document.getElementById('id_fixdollars');
            const ancestor = element.closest('.mb-3');
            ancestor.style.display = 'none';
            </script>";
        }
        $msg = 'Some elements are hidden for simplification based on you being in cohort '.get_config('tool_stackui', 'uicohort');
        \core\notification::add($msg, \core\notification::WARNING);
        return $content;
    }



    /**
     * Check if the user is in the UI cohort
     *
     * @param array $tweaks
     * @return array
     * @package tool_stackui
     */
    public static function in_uicohort(): array {
        global $DB, $USER;
        $incohort = [];
        $uicohort = get_config('tool_stackui', 'uicohort');
        $cache = \cache::make('tool_stackui', 'stackuicache');
        if (($incohort = $cache->get('incohort')) === false) {
            $sql = "SELECT * FROM {cohort} co
                    JOIN {cohort_members} cm
                    ON co.id = cm.cohortid
                    WHERE cm.userid = :userid
                    AND co.name = :uicohort
                    ";
            $incohort = $DB->get_records_sql($sql, ['userid' => $USER->id, 'uicohort' => $uicohort]);
            $cache->set('incohort', $incohort);
        }
        return $incohort;

    }
}
