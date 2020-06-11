<?php
/* Copyright (C) 2004		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Benoit Mortier			<benoit.mortier@opensides.be>
 * Copyright (C) 2005-2017	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2016	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2011-2015	Philippe Grand			<philippe.grand@atoo-net.com>
 * Copyright (C) 2011		Remy Younes				<ryounes@gmail.com>
 * Copyright (C) 2012-2015	Marcos García			<marcosgdf@gmail.com>
 * Copyright (C) 2012		Christophe Battarel		<christophe.battarel@ltairis.fr>
 * Copyright (C) 2011-2016	Alexandre Spangaro		<aspangaro.dolibarr@gmail.com>
 * Copyright (C) 2015		Ferran Marcet			<fmarcet@2byte.es>
 * Copyright (C) 2016		Raphaël Doursenaud		<rdoursenaud@gpcsolutions.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	    \file       htdocs/advancedictionaries/core/tpl/dictionaries.tpl.php
 *		\ingroup    advancedictionaries
 *		\brief      Page template for show list of dictionaries and the selected dictionary
 */


/*
 * View
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
$form = new Form($db);

dol_include_once('/advancedictionaries/class/html.formdictionary.class.php');
$formdictionary = new FormDictionary($db);

$titre = $langs->trans("DictionarySetup");
$linkback = '';
$titlepicto = 'title_setup';
if (isset($dictionary) && $dictionary->enabled) {
    $langs->loadLangs($dictionary->langs);

    $titre.=' - '.$langs->trans($dictionary->nameLabel);
    $linkback = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $langs->trans("BackToDictionaryList") . '</a>';
    if (!empty($dictionary->titlePicto)) $titlepicto = $dictionary->titlePicto;

    if (!empty($dictionary->customTitle)) $titre = $langs->trans($dictionary->customTitle);
    if (!empty($dictionary->customBackLink)) $linkback = $dictionary->customBackLink;
    if (!empty($dictionary->hideCustomBackLink)) $linkback = '';
}

print load_fiche_titre($titre, $linkback, $titlepicto);

if (!isset($dictionary))
{
    print $langs->trans("DictionaryDesc");
    print " ".$langs->trans("OnlyActiveElementsAreShown")."<br>\n";
}

/*
 * Show a dictionary
 */
if (isset($dictionary)) {
    if ($dictionary->enabled) {
        $now = dol_now();

        //------------------------------------------------------------------------------------------------------------------
        // Confirm box
        //------------------------------------------------------------------------------------------------------------------

        $formconfirm = '';
        $formquestion = '';

        // Make form question
        if ($action == 'add_line' || $action == 'edit_line') {
            $formquestion = array('text' => '<i>* ' . $langs->trans("AdvanceDictionariesFieldRequired") . '</i>');

            // Add hidden input
            $formquestion[] = array('type' => 'hidden', 'name' => 'token', 'value' => $_SESSION['newtoken']);
            if (!empty($sortfield)) $formquestion[] = array('type' => 'hidden', 'name' => 'sortfield', 'value' => $sortfield);
            if (!empty($sortorder)) $formquestion[] = array('type' => 'hidden', 'name' => 'sortorder', 'value' => $sortorder);
            if (!empty($page)) $formquestion[] = array('type' => 'hidden', 'name' => 'page', 'value' => $page);
            if ($limit > 0 && $limit != $conf->liste_limit) $formquestion[] = array('type' => 'hidden', 'name' => 'limit', 'value' => $limit);
            if ($search_active != 1) $formquestion[] = array('type' => 'hidden', 'name' => 'search_' . $dictionary->active_field, 'value' => $search_active);
            if ($action == 'edit_line') $formquestion[] = array('type' => 'hidden', 'name' => 'rowid', 'value' => $rowid);
            foreach ($dictionary->fields as $fieldName => $field) {
                if (!$field['is_not_searchable']) {
                    $formquestion[] = array('type' => 'hidden', 'name' => 'search_' . $fieldName, 'value' => GETPOST('search_' . $fieldName));
                }
            }

            // Get default values
            $dictionary_line = $dictionary->getNewDictionaryLine();
            if ($action == 'edit_line') $dictionary_line->fetch($rowid);
            if ($error) $fieldsValue = $dictionary->getFieldsValueFromForm($action == 'edit_line' ? 'edit_' : 'add_', '', $action == 'edit_line' ? 1 : 0);

            // Add input fields
            foreach ($dictionary->fields as $fieldName => $field) {
                if (($action == 'add_line' && (!empty($field['is_not_addable']) || !empty($field['is_not_show_in_add']))) ||
                    ($action == 'edit_line' && (!empty($field['is_not_editable'])) || !empty($field['is_not_show_in_edit']))) continue;
                $label = $langs->trans(!empty($field['label_in_add_edit']) ? $field['label_in_add_edit'] : $field['label']);
                if (isset($fieldsValue[$fieldName])) $dictionary_line->fields[$fieldName] = $fieldsValue[$fieldName];

                $input_label = '';
                if (!empty($field['is_require'])) {
                    $input_label .= '<span class="fieldrequired">';
                }
                if (!empty($field['help'])) {
                    if (preg_match('/^http(s*):/i', $field['help'])) $input_label .= '<a href="' . $field['help'] . '" target="_blank">' . $label . ' ' . img_help(1, $label) . '</a>';
                    else $input_label .= $form->textwithpicto($label, $langs->trans($field['help']));   // Tooltip on hover
                } elseif (!empty($field['help_button'])) {
                    $input_label .= $form->textwithpicto($label, $langs->trans($field['help_button']), 1, 'help', '', 0, 2, $fieldName);   // Tooltip on click
                    $input_label .= <<<SCRIPT
                    <script type="text/javascript">
                    	jQuery(document).ready(function () {
                    		jQuery(".classfortooltiponclick").click(function () {
                    		    console.log("We click on tooltip for element with dolid="+$(this).attr('dolid'));
                    		    if ($(this).attr('dolid'))
                    		    {
                                    jQuery(".classfortooltiponclicktext").dialog({ width: 'auto', autoOpen: false });
                                    obj=$("#idfortooltiponclick_"+$(this).attr('dolid'));
                    		        obj.dialog("open");
                    		    }
                    		});
                        });
                    </script>
SCRIPT;
                } else $input_label .= $label;
                if (!empty($field['is_require'])) {
                    $input_label .= ' *</span>';
                }

                if (is_array($field['add_params_in_add_edit'])) {
                    foreach ($field['add_params_in_add_edit'] as $name) {
                        $formquestion[] = array('name' => $name);
                    }
                }

                $formquestion[] = array('type' => 'other', 'name' => ($action == 'edit_line' ? 'edit_' : 'add_') . $fieldName, 'label' => $input_label, 'value' => $dictionary_line->showInputFieldAD($fieldName, null, $action == 'edit_line' ? 'edit_' : 'add_'));
            }
        }

        // Confirmation de l'ajout d'une ligne
        if ($action == 'add_line') {
            $formconfirm = $formdictionary->formconfirm($_SERVER["PHP_SELF"] . '?' . $param3, $langs->trans('AdvanceDictionariesAddLine'), $langs->trans('AdvanceDictionariesConfirmAddLine'), 'confirm_add_line', $formquestion, 0, 1, 800, '70%', 1, 1);
			$formconfirm .= $dictionary->showUpdateListValuesScript($fieldsValue, $action == 'edit_line' ? 'edit_' : 'add_');
		} // Confirmation de l'edition d'une ligne
        elseif ($action == 'edit_line') {
            $formconfirm = $formdictionary->formconfirm($_SERVER["PHP_SELF"] . '?' . $param3 . '&rowid=' . $rowid . '&prevrowid=' . $prevrowid, $langs->trans('AdvanceDictionariesEditLine'), $langs->trans('AdvanceDictionariesConfirmEditLine'), 'confirm_edit_line', $formquestion, 0, 1, 800, '70%', 1, 1);
			$formconfirm .= $dictionary->showUpdateListValuesScript($fieldsValue, $action == 'edit_line' ? 'edit_' : 'add_');
		} // Confirmation de la suppression de la ligne
        elseif ($action == 'delete_line') {
            $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?' . $param3 . '&rowid=' . $rowid . '&prevrowid=' . $prevrowid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete_line', '', 0, 1);
        }

        $parameters = array();
        $reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
        if (empty($reshook)) $formconfirm .= $hookmanager->resPrint;
        elseif ($reshook > 0) $formconfirm = $hookmanager->resPrint;

        // Print form confirm
        print $formconfirm;

        //------------------------------------------------------------------------------------------------------------------
        // Show list of values
        //------------------------------------------------------------------------------------------------------------------
        if ($dictionary->fetch_lines($search_active, $search_filters, $order_by, $offset, $limit+1) > 0) {
            $nbtotalofrecords = '';
            if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
                $nbtotalofrecords = $dictionary->fetch_lines($search_active, $search_filters, array(), 0, 0, true);
            }
            $num = count($dictionary->lines);

            $addButton = '';
            if ($dictionary->lineCanBeAdded && $canCreate) {
                $addButton = '<a href="' . $_SERVER['PHP_SELF'] . '?' . $param3 . '&action=add_line&module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name) . '&'.$now.'="' . ((float)DOL_VERSION >= 8.0 ? 'class=" butActionNew"' : '') . '>';
                $addButton .= $langs->trans("Add");
                if ((float)DOL_VERSION >= 8.0) $addButton .= '<span class="fa fa-plus-circle valignmiddle"></span>';
                $addButton .= '</a>';
            }

            $arrayofselected = is_array($toselect) ? $toselect : array();

            // List of mass actions available
            $arrayofmassactions = array();
            if ($dictionary->lineCanBeDeleted && $canDelete) $arrayofmassactions['predelete'] = $langs->trans("Delete");
            if (in_array($massaction, array('predelete'))) $arrayofmassactions = array();
            $massactionbutton = $form->selectMassAction('', $arrayofmassactions);

            print '<form id="searchFormList" action="' . $_SERVER['PHP_SELF'] . '?module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name) . '" method="POST">';
            print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
            print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
            print '<input type="hidden" name="action" value="list">';
            if (!empty($sortfield)) print '<input type="hidden" name="sortfield" value="' . dol_escape_htmltag($sortfield) . '">';
            if (!empty($sortorder)) print '<input type="hidden" name="sortorder" value="' . dol_escape_htmltag($sortorder) . '">';
            if (!empty($page)) print '<input type="hidden" name="page" value="' . dol_escape_htmltag($page) . '">';
            if (!empty($contextpage)) print '<input type="hidden" name="contextpage" value="' . dol_escape_htmltag($contextpage) . '">';
            if ($limit > 0 && $limit != $conf->liste_limit) print '<input type="hidden" name="limit" value="' . dol_escape_htmltag($limit) . '">';
            if ($search_active != 1) print '<input type="hidden" name="search_' . $dictionary->active_field . '" value="' . dol_escape_htmltag($search_active) . '">';

            print_barre_liste('', $page, $_SERVER["PHP_SELF"], '&' . $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, '', 0, $addButton, '', $limit);

            $objecttmp = new DictionaryLine($db, $dictionary);
            $trackid = 'dic' . $dictionary->id;
            include DOL_DOCUMENT_ROOT . '/core/tpl/massactions_pre.tpl.php';

            $moreforfilter = '';
            // More filters from hook
            $parameters = array();
            $reshook = $hookmanager->executeHooks('printFieldPreListTitle', $parameters, $dictionary, $action);
            if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
            else $moreforfilter = $hookmanager->resPrint;

            if (!empty($moreforfilter)) {
                print '<div class="liste_titre liste_titre_bydiv centpercent">';
                print $moreforfilter;
                print '</div>';
            }

            $varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
            $selectedfields = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage);    // This also change content of $arrayfields
            if ($massactionbutton) $selectedfields .= $form->showCheckAddButtons('checkforselect', 1);

            print '<div class="div-table-responsive">';
            print '<table class="tagtable liste' . ($moreforfilter ? " listwithfilterbefore" : "") . '">' . "\n";

            $showTechnicalId = $dictionary->showTechnicalID;

            // Title line with search boxes
            print '<tr class="liste_titre_filter">';
            if ($showTechnicalId) print '<td class="liste_titre"></td>';
            foreach ($dictionary->fields as $fieldName => $field) {
                if ($arrayfields[$fieldName]['checked'] && empty($field['is_not_show'])) {
                    $moreClasses = !empty($field['td_search']['moreClasses']) ? ' ' . $field['td_search']['moreClasses'] : '';
                    $moreAttributes = !empty($field['td_search']['moreAttributes']) ? ' ' . $field['td_search']['moreAttributes'] : '';
                    $align = !empty($field['td_search']['align']) ? $field['td_search']['align'] : $dictionary->getAlignFlagForField($fieldName);

                    print '<td align="' . $align . '" class="liste_titre' . $moreClasses . '"' . $moreAttributes . '>';
                    if (!$field['is_not_searchable']) {
                        print $dictionary->showInputSearchField($fieldName, $search_filters);
                    }
                    print '</td>';
                }
            }
            // Hook fields
            $parameters = array('arrayfields' => $arrayfields);
            $reshook = $hookmanager->executeHooks('printFieldListOption', $parameters, $dictionary, $action);
            print $hookmanager->resPrint;
            print '<td class="liste_titre maxwidthonsmartphone" align="center">';
            print $form->selectyesno('search_' . $dictionary->active_field, $search_active, 1, false, 1);
            print '</td>';
            print '<td class="liste_titre" align="right">';
            print $form->showFilterButtons();
            print '</td>';
            print '</tr>';
			print $dictionary->showUpdateListValuesScript($search_filters, 'search_');

            // Fields title
            print '<tr class="liste_titre">';
            if ($showTechnicalId) print_liste_field_titre($langs->trans("TechnicalID"), $_SERVER["PHP_SELF"], $dictionary->rowid_field, "", '&' . $param2, 'width="5%"', $sortfield, $sortorder);
            foreach ($dictionary->fields as $fieldName => $field) {
                if ($arrayfields[$fieldName]['checked'] && empty($field['is_not_show'])) {
                    $moreAttributes = !empty($field['td_title']['moreAttributes']) ? ' ' . $field['td_title']['moreAttributes'] : '';
                    $align = !empty($field['td_title']['align']) ? $field['td_title']['align'] : $dictionary->getAlignFlagForField($fieldName);
                    $moreAttributes .= ' align="' . $align . '"';

                    print_liste_field_titre($arrayfields[$fieldName]['label'], $_SERVER["PHP_SELF"], $field['is_not_sortable'] ? '' : $fieldName, '', '&' . $param2, $moreAttributes, $sortfield, $sortorder);
                    print '</td>';
                }
            }
            // Hook fields
            $parameters = array('arrayfields' => $arrayfields, 'param' => $param2, 'sortfield' => $sortfield, 'sortorder' => $sortorder);
            $reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $dictionary, $action);
            print $hookmanager->resPrint;
            print_liste_field_titre($langs->trans("Status"), $_SERVER["PHP_SELF"], $dictionary->active_field, "", '&' . $param2, 'width="10%" align="center"', $sortfield, $sortorder);
            print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"], "", '', '', 'align="center"', $sortfield, $sortorder, 'maxwidthsearch ');
            print '</tr>';

            // Lines with values
            $last_rowid = 0;
            $idx = 0;
            foreach ($dictionary->lines as $line) {
                if ($idx >= min($num, $limit)) break;

                // Output line
                print '<tr class="oddeven" id="rowid-' . $line->id . '">';

                if ($showTechnicalId) {
                    print '<td class="nowrap">';
                    print $line->id;
                    print "</td>";
                }

                foreach ($dictionary->fields as $fieldName => $field) {
                    if ($arrayfields[$fieldName]['checked'] && empty($field['is_not_show'])) {
                        $moreClasses = !empty($field['td_output']['moreClasses']) ? ' class="' . $field['td_output']['moreClasses'] . '"' : '';
                        $moreAttributes = !empty($field['td_output']['moreAttributes']) ? ' ' . $field['td_output']['moreAttributes'] : '';
                        $align = !empty($field['td_output']['align']) ? $field['td_output']['align'] : $dictionary->getAlignFlagForField($fieldName);

                        print '<td align="' . $align . '"' . $moreClasses . $moreAttributes . '>';
                        print $line->showOutputFieldAD($fieldName);
                        print '</td>';
                    }
                }

                // Fields from hook
                $parameters = array('arrayfields' => $arrayfields);
                $reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $line, $action);
                print $hookmanager->resPrint;

                // Active
                print '<td align="center" class="nowrap">';
                $isLineCanBeDisabled = $dictionary->isLineCanBeDisabled($line);
                if ($isLineCanBeDisabled === null) {
                    print $langs->trans("AlwaysActive");
                } elseif ($isLineCanBeDisabled === true && $canDisable) {
                    print '<a href="' . $_SERVER["PHP_SELF"] . '?' . $param3 . '&action=activate_' . ($line->active ? 'off' : 'on') . '&rowid=' . $line->id . '#rowid-' . $line->id . '">' .
                        img_picto($langs->trans($line->active ? 'Activated' : 'Disabled'), $line->active ? 'switch_on' : 'switch_off') . '</a>';
                } elseif (is_string($isLineCanBeDisabled)) {
                    print $langs->trans($isLineCanBeDisabled);
                } else {
                    print img_picto($langs->trans($line->active ? 'Activated' : 'Disabled'), $line->active ? 'switch_on' : 'switch_off');
                }
                print "</td>";

                // Action column
                print '<td class="nowrap" align="center">';
                // Modify link
                if ($dictionary->lineCanBeUpdated && $canUpdate) print '<a class="reposition" href="' . $_SERVER["PHP_SELF"] . '?' . $param3 . '&rowid=' . $line->id . '&action=edit_line&'.$now.'=#rowid-' . $line->id . '">' . img_edit() . '</a>';
                // Delete link
                if ($dictionary->lineCanBeDeleted && $canDelete) print '<a href="' . $_SERVER["PHP_SELF"] . '?' . $param3 . '&rowid=' . $line->id . '&prevrowid=' . $last_rowid . '&action=delete_line' . '&rowid=' . $line->id . '&'.$now.'=#rowid-' . $line->id . '">' . img_delete() . '</a>';
                if ($massactionbutton || $massaction) {   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
                    $selected = 0;
                    if (in_array($line->id, $arrayofselected)) $selected = 1;
                    print '<input id="cb' . $line->id . '" class="flat checkforselect marginleftonly" type="checkbox" name="toselect[]" value="' . $line->id . '"' . ($selected ? ' checked="checked"' : '') . '>';
                }
                print '</td>';

                print "</tr>";

                $last_rowid = $line->id;
                $idx++;
            }

            $parameters = array('arrayfields' => $arrayfields);
            $reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters, $dictionary, $action);
            print $hookmanager->resPrint;

            print '</table>';
            print '</div>';

            print '</form>';
        } else {
            setEventMessage($dictionary->errorsToString(), 'errors');
        }
    } else {
        accessforbidden();
    }
} else {
    /*
     * Show list of dictionary to show
     */

    print '<div class="div-table-responsive-no-min">';
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    print '<td width="10px">';
    print '<td width="20%">' . $langs->trans("Module") . '</td>';
    print '<td width="40%">' . $langs->trans("Dictionary") . '</td>';
    print '<td>' . $langs->trans("Table") . '</td>';
    print '</tr>';

    $dictionaries = Dictionary::fetchAllDictionaries($db, $moduleFilter, $familyFilter);

    $lastfamily = '';
    foreach ($dictionaries as $dictionary) {
        if ($dictionary->enabled && !$dictionary->hidden) {
            $langs->loadLangs($dictionary->langs);

            if ($lastfamily != $dictionary->family) {
                $lastfamily = $dictionary->family;
                print '<tr class="oddeven family_title"><td colspan="4">' . $langs->trans($dictionary->familyLabel) . '</td></tr>';
            }

            print '<tr class="oddeven"><td width="10px"></td>' .
                '<td width="20%">' . (!empty($dictionary->modulePicto) ? img_picto('', $dictionary->modulePicto) . ' ' : '') . $langs->trans($dictionary->moduleLabel) . '</td>' .
                '<td width="40%"><a href="' . $_SERVER["PHP_SELF"] . '?module=' . $dictionary->module . '&name=' . $dictionary->name . '">' .
                $langs->trans($dictionary->nameLabel) . '</a></td>' .
                '<td>' . $dictionary->table_name . '</td></tr>';
        }
    }
    print '</table>';
    print '</div>';
}

print '<br>';
