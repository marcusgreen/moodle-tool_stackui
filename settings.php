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
 * TODO describe file settings
 *
 * @package    tool_stackui
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $settingspage = new admin_settingpage('stackuisettings' , get_string('settings:stackuisettings', 'tool_stackui'));

    $ADMIN->add('tools', $settingspage);

    $settingspage->add(new admin_setting_configcheckbox('tool_stackui/enabled',
         get_string('settings:enabled', 'tool_stackui'),
         get_string('settings:enabled_text', 'tool_stackui') , 0));

         $settingspage->add(new admin_setting_configcheckbox('tool_stackui/monospaceqtext',
         get_string('settings:monospaceqtext', 'tool_stackui'),
         get_string('settings:monospaceqtext_text', 'tool_stackui') , 0));
         $settingspage->add(new admin_setting_configtext('tool_stackui/uicohort',

         get_string('settings:cohort', 'tool_stackui'),
         get_string('settings:cohort_text', 'tool_stackui') , 'stacknovice'));
    $formelements = 'fitem_id_defaultmark, fitem_id_idnumber,fitem_id_questiondescription,
fitem_id_variantsselectionseed,fitem_id_status,fitem_id_penalty,id_fixdollars,
fitem_id_questionsimplify,fitem_id_assumepositive, fitem_id_assumereal,fitem_id_prtcorrect,
fitem_id_prtpartiallycorrect,fitem_id_prtincorrect,fitem_id_decimals,fitem_id_scientificnotation
fitem_id_penalty,fitem_id_addhint, fitem_id_tags, fitem_id_defaultmark, fitem_id_idnumber,
fitem_id_questiondescription, fitem_id_variantsselectionseed, fitem_id_status, fitem_id_penalty,
fitem_id_questionsimplify, fitem_id_assumepositive, fitem_id_assumereal,fitem_id_scientificnotation,
fitem_id_multiplicationsign, fitem_id_sqrtsign, fitem_id_complexno, fitem_id_inversetrig, fitem_id_logicsymbol,
fitem_id_matrixparens,id_optionsheader,id_tagsheader';

     $settingspage->add(new admin_setting_configtextarea('tool_stackui/elementstohide',
         get_string('settings:elementstohide', 'tool_stackui'),
         get_string('settings:elementstohide_text', 'tool_stackui') ,
          $formelements));
    $settingspage->add(new admin_setting_configtext('tool_stackui/qvarheight',
          get_string('settings:qvarheight', 'tool_stackui'),
          get_string('settings:qvarheight_text', 'tool_stackui'),
          '',PARAM_NUMBER,3)); // Default height of 200px


}