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

$titre = $langs->trans("DictionarySetup");
$linkback = '';
$titlepicto = 'title_setup';
if (isset($dictionary) && $dictionary->enabled) {
    $langs->loadLangs($dictionary->langs);

    $titre.=' - '.$langs->trans($dictionary->nameLabel);
    $linkback = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $langs->trans("BackToDictionaryList") . '</a>';
    if ($dictionary->titlePicto) $titlepicto = $dictionary->titlePicto;
}

print load_fiche_titre($titre, $linkback, $titlepicto);

if (!isset($dictionary) || !$dictionary->enabled)
{
    print $langs->trans("DictionaryDesc");
    print " ".$langs->trans("OnlyActiveElementsAreShown")."<br>\n";
}

print "<br>\n";

/*
 * Show a dictionary
 */
if (isset($dictionary) && $dictionary->enabled) {
    //------------------------------------------------------------------------------------------------------------------
    // Generate the add form
    //------------------------------------------------------------------------------------------------------------------
    if (($dictionary->lineCanBeAdded && $canCreate) || ($action == 'edit' && $dictionary->edit_in_add_form && $dictionary->lineCanBeUpdated && $canUpdate)) {
        print '<form action="' . $_SERVER['PHP_SELF'] . '?module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name) . '" method="POST">';
        print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
        print '<input type="hidden" name="action" value="'.($action == 'edit' && $dictionary->edit_in_add_form ? 'confirm_edit' : 'add').'">';
        if (!empty($sortfield)) print '<input type="hidden" name="sortfield" value="'.dol_escape_htmltag($sortfield).'">';
        if (!empty($sortorder)) print '<input type="hidden" name="sortorder" value="'.dol_escape_htmltag($sortorder).'">';
        if (!empty($page)) print '<input type="hidden" name="page" value="'.dol_escape_htmltag($page).'">';
        if ($limit > 0 && $limit != $conf->liste_limit) print '<input type="hidden" name="limit" value="'.dol_escape_htmltag($limit).'">';
        if ($search_active != 1) print '<input type="hidden" name="search_'.$dictionary->active_field.'" value="'.dol_escape_htmltag($search_active).'">';
        if ($action == 'edit' && $dictionary->edit_in_add_form) print '<input type="hidden" name="rowid" value="' . $rowid . '">';

        print '<div id="addform" class="div-table-responsive-no-min">';
        print '<table class="noborder" width="100%">';

        // Position of field in each line
        $form_lines = array();
        $max_column = array();
        foreach ($dictionary->fields as $fieldName => $field) {
            if (($action != 'edit' && !$field['is_not_addable']) || ($action == 'edit' && !$field['is_not_editable'] && $dictionary->edit_in_add_form)) {
                $position = empty($field['td_input']['positionLine']) ? 0 : $field['td_input']['positionLine'];
                $form_lines[$position][$fieldName] = $field;
                $max_column[$position] = (isset($max_column[$position]) ? $max_column[$position] : 0) + 1;
            }
        }
        ksort($form_lines);
        rsort($max_column);
        $max_column = $max_column[0];

        $dictionary_line = $dictionary->getNewDictionaryLine();
        if ($dictionary->edit_in_add_form && $action == 'edit') $dictionary_line->fetch($rowid);
        $fieldsValue = $dictionary->getFieldsValueFromForm($dictionary->edit_in_add_form && $action == 'edit' ? 'edit_' : 'add_');

        $has_required_fields = false;
        $idx = 1; $numLines = count($form_lines);
        foreach ($form_lines as $fields) {
            $numColumns = count($fields);

            // Line for title
            print '<tr class="liste_titre">';
            $idx_column = 1;
            foreach ($fields as $fieldName => $field) {
                $label = $langs->trans($field['label']);
                $moreClasses = !empty($field['td_title']['moreClasses']) ? ' class="' . $field['td_title']['moreClasses'] . '"' : '';
                $moreAttributes = !empty($field['td_title']['moreAttributes']) ? ' ' . $field['td_title']['moreAttributes'] : '';
                $align = !empty($field['td_title']['align']) ? $field['td_title']['align'] : $dictionary->getAlignFlagForField($fieldName);

                print '<td align="' . $align . '"' . ($idx_column == $numColumns && $idx_column < $max_column ? ' colspan="' . ($max_column - $idx_column + 1) . '"' : '') . $moreClasses . $moreAttributes . '>';
                if (!empty($field['is_require'])) {
                    $has_required_fields = true;
                    print '<span class="fieldrequired">';
                }
                if (!empty($field['help'])) {
                    if (preg_match('/^http(s*):/i', $field['help'])) print '<a href="' . $field['help'] . '" target="_blank">' . $label . ' ' . img_help(1, $label) . '</a>';
                    else print $form->textwithpicto($label, $langs->trans($field['help']));   // Tooltip on hover
                } elseif (!empty($field['help_button'])) {
                    print $form->textwithpicto($label, $langs->trans($field['help_button']), 1, 'help', '', 0, 2, $fieldName);   // Tooltip on click
                } else print $label;
                if (!empty($field['is_require'])) {
                    print ' *</span>';
                }
                print '</td>';
                $idx_column++;
            }
            print '<td style="min-width: 26px;"></td>';
            print '</tr>';

            // Line to enter new values
            print '<tr class="oddeven nodrag nodrop nohover">';
            $idx_column = 1;
            foreach ($fields as $fieldName => $field) {
                $moreClasses = !empty($field['td_input']['moreClasses']) ? ' class="' . $field['td_input']['moreClasses'] . '"' : '';
                $moreAttributes = !empty($field['td_input']['moreAttributes']) ? ' ' . $field['td_input']['moreAttributes'] : '';
                $align = !empty($field['td_input']['align']) ? $field['td_input']['align'] : $dictionary->getAlignFlagForField($fieldName);
                if (isset($fieldsValue[$fieldName])) $dictionary_line->fields[$fieldName] = $fieldsValue[$fieldName];

                print '<td align="' . $align . '"' . ($idx_column == $numColumns && $idx_column < $max_column ? ' colspan="' . ($max_column - $idx_column + 1) . '"' : '') . $moreClasses . $moreAttributes . '>';
                print $dictionary_line->showInputField($fieldName, null, $dictionary->edit_in_add_form && $action == 'edit' ? 'edit_' : 'add_');
                print '</td>';
                $idx_column++;
            }

            // Button
            if ($numLines != $idx) {
                print '<td style="min-width: 26px;"></td>';
            } else {
                print '<td align="center">';
                if ($action == 'edit' && $dictionary->edit_in_add_form) {
                    print '<input type="submit" class="button" name="actionedit" value="' . $langs->trans("Edit") . '">';
                    print '<input type="submit" class="button" name="actioncancel" value="' . $langs->trans("Cancel") . '">';
                } else {
                    print '<input type="submit" class="button" name="actionadd" value="' . $langs->trans("Add") . '">';
                }
                print '</td>';
            }
            print "</tr>";
            $idx++;
        }

        print '</table>';
        print '</div>';

        print '</form>';

        if ($has_required_fields) print '<i>* ' . $langs->trans("AdvanceDictionariesFieldRequired") . '</i><br>';

        print '<br>';
    }

    //------------------------------------------------------------------------------------------------------------------
    // Show list of values
    //------------------------------------------------------------------------------------------------------------------

    // Confirmation de la suppression de la ligne
    if ($action == 'delete') {
        print $form->formconfirm($_SERVER["PHP_SELF"] . '?' . $param . '&rowid=' . $rowid . '&prevrowid=' . $prevrowid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete', '', 0, 1);
    }

    if ($dictionary->fetch_lines($search_active, $search_filters, $order_by, $offset, $limit + 1) > 0) {
        $nbtotalofrecords = '';
        if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
            $nbtotalofrecords = $dictionary->fetch_lines($search_active, $search_filters, array(), 0, 0, true);
        }
        $num = count($dictionary->lines);

        print '<form action="' . $_SERVER['PHP_SELF'] . '?module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name) . '" method="POST">';
        print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
        print '<input type="hidden" name="action" value="confirm_edit">';
        if (!empty($sortfield)) print '<input type="hidden" name="sortfield" value="'.dol_escape_htmltag($sortfield).'">';
        if (!empty($sortorder)) print '<input type="hidden" name="sortorder" value="'.dol_escape_htmltag($sortorder).'">';
        if (!empty($page)) print '<input type="hidden" name="page" value="'.dol_escape_htmltag($page).'">';
        if ($limit > 0 && $limit != $conf->liste_limit) print '<input type="hidden" name="limit" value="'.dol_escape_htmltag($limit).'">';
        if ($search_active != 1) print '<input type="hidden" name="search_'.$dictionary->active_field.'" value="'.dol_escape_htmltag($search_active).'">';

        print_barre_liste('', $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, '', 0, '', '', $limit);

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

        print '<div class="div-table-responsive">';
        print '<table class="noborder" width="100%">';

        // Title line with search boxes
        print '<tr class="liste_titre">';
        foreach ($dictionary->fields as $fieldName => $field) {
            if (!$field['is_not_show']) {
                $moreClasses = !empty($field['td_search']['moreClasses']) ? ' class="' . $field['td_search']['moreClasses'] . '"' : '';
                $moreAttributes = !empty($field['td_search']['moreAttributes']) ? ' ' . $field['td_search']['moreAttributes'] : '';
                $align = !empty($field['td_search']['align']) ? $field['td_search']['align'] : $dictionary->getAlignFlagForField($fieldName);

                print '<td align="' . $align . '"' . $moreClasses . $moreAttributes . '>';
                if (!$field['is_not_searchable']) {
                    print $dictionary->showInputSearchField($fieldName);
                }
                print '</td>';
            }
        }
        // Fields from hook
        $parameters = array();
        $reshook = $hookmanager->executeHooks('printFieldListOption', $parameters, $dictionary, $action);
        print $hookmanager->resPrint;
        print '<td align="center">';
        print $form->selectyesno('search_' . $dictionary->active_field, $search_active, 1, false, 1);
        print '</td>';
        print '<td class="liste_titre" align="right">';
        print $form->showFilterButtons();
        print '</td>';
        print '</tr>';

        // Title of lines
        print '<tr class="liste_titre">';
        foreach ($dictionary->fields as $fieldName => $field) {
            if (!$field['is_not_show']) {
                $moreClasses = !empty($field['td_title']['moreClasses']) ? ' class="' . $field['td_title']['moreClasses'] . '"' : '';
                $moreAttributes = !empty($field['td_title']['moreAttributes']) ? ' ' . $field['td_title']['moreAttributes'] : '';
                $align = !empty($field['td_title']['align']) ? $field['td_title']['align'] : $dictionary->getAlignFlagForField($fieldName);
                $moreAttributes .= ' align="' . $align . '"';

                print getTitleFieldOfList($langs->trans($field['label']), 0, $_SERVER["PHP_SELF"], ($field['is_not_sortable'] ? '' : $fieldName), '', $param, $moreAttributes, $sortfield, $sortorder, $moreClasses);
            }
        }
        // Fields from hook
        $parameters = array();
        $reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $dictionary, $action);
        print $hookmanager->resPrint;
        print getTitleFieldOfList($langs->trans("Status"), 0, $_SERVER["PHP_SELF"], $dictionary->active_field, '', $param, 'width="10%" align="center"', $sortfield, $sortorder);
        print getTitleFieldOfList('', 0, "", "", "", "", 'width="32px"');
        print '</tr>';

        // Lines with values
        $var = false;
        $last_rowid = 0;
        foreach ($dictionary->lines as $line) {
            $var = !$var;
            if ($action == 'edit' && !$dictionary->edit_in_add_form && $dictionary->lineCanBeUpdated && $canUpdate && $line->id == $rowid) {
                $dictionary_line = clone $line;
                $fieldsValue = $dictionary->getFieldsValueFromForm('edit_');

                print '<input type="hidden" name="rowid" value="' . $line->id . '">';

                // Line to enter new values
                print '<tr class="oddeven nodrag nodrop nohover">';
                foreach ($dictionary->fields as $fieldName => $field) {
                    if (!$field['is_not_show']) {
                        $moreClasses = !empty($field['td_input']['moreClasses']) ? ' class="' . $field['td_input']['moreClasses'] . '"' : '';
                        $moreAttributes = !empty($field['td_input']['moreAttributes']) ? ' ' . $field['td_input']['moreAttributes'] : '';
                        $align = !empty($field['td_input']['align']) ? $field['td_input']['align'] : $dictionary->getAlignFlagForField($fieldName);

                        print '<td align="' . $align . '"' . $moreClasses . $moreAttributes . '>';
                        if (!$field['is_not_addable']) {
                            if (isset($fieldsValue[$fieldName])) $dictionary_line->fields[$fieldName] = $fieldsValue[$fieldName];
                            print $dictionary_line->showInputField($fieldName, null, 'edit_');
                        } else {
                            print $line->showOutputField($fieldName);
                        }
                        print '</td>';
                    }
                }

                // Fields from hook
                $parameters = array();
                $reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $line, $action);
                print $hookmanager->resPrint;

                // Button
                print '<td colspan="2" align="center">';
                print '<input type="submit" class="button" name="actionedit" value="' . $langs->trans("Edit") . '">';
                print '<input type="submit" class="button" name="actioncancel" value="' . $langs->trans("Cancel") . '">';
                print '</td>';
                print "</tr>";
            } else {
                // Output line
                print '<tr class="' . $bc[$var] . '" id="rowid-' . $line->id . '">';
                foreach ($dictionary->fields as $fieldName => $field) {
                    if (!$field['is_not_show']) {
                        $moreClasses = !empty($field['td_output']['moreClasses']) ? ' class="' . $field['td_output']['moreClasses'] . '"' : '';
                        $moreAttributes = !empty($field['td_output']['moreAttributes']) ? ' ' . $field['td_output']['moreAttributes'] : '';
                        $align = !empty($field['td_output']['align']) ? $field['td_output']['align'] : $dictionary->getAlignFlagForField($fieldName);

                        print '<td align="' . $align . '"' . $moreClasses . $moreAttributes . '>';
                        print $line->showOutputField($fieldName);
                        print '</td>';
                    }
                }

                // Fields from hook
                $parameters = array();
                $reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $line, $action);
                print $hookmanager->resPrint;

                // Active
                print '<td align="center" class="nowrap">';
                $isLineCanBeDisabled = $dictionary->isLineCanBeDisabled($line);
                if ($isLineCanBeDisabled === null) {
                    print $langs->trans("AlwaysActive");
                } elseif ($isLineCanBeDisabled === true && $canDisable) {
                    print '<a href="' . $_SERVER["PHP_SELF"] . '?' . $param . '&action=activate_' . ($line->active ? 'off' : 'on') . '&rowid=' . $line->id . '#rowid-' . $line->id . '">' .
                        img_picto($langs->trans($line->active ? 'Activated' : 'Disabled'), $line->active ? 'switch_on' : 'switch_off') . '</a>';
                } elseif (is_string($isLineCanBeDisabled)) {
                    print $langs->trans($isLineCanBeDisabled);
                } else {
                    print img_picto($langs->trans($line->active ? 'Activated' : 'Disabled'), $line->active ? 'switch_on' : 'switch_off');
                }
                print "</td>";

                print '<td align="center">';
                // Modify link
                if ($dictionary->lineCanBeUpdated && $canUpdate) print '<a class="reposition" href="' . $_SERVER["PHP_SELF"] . '?' . $param . '&rowid=' . $line->id . '&action=edit' . ($dictionary->edit_in_add_form ? '#addform' : '#rowid-' . $line->id) . '">' . img_edit() . '</a>';
                // Delete link
                if ($dictionary->lineCanBeDeleted && $canDelete) print '<a href="' . $_SERVER["PHP_SELF"] . '?' . $param . '&rowid=' . $line->id . '&prevrowid=' . $last_rowid . '&action=delete' . '&rowid=' . $line->id . '">' . img_delete() . '</a>';
                print '</td>';
                print "</tr>";
            }
            $last_rowid = $line->id;
        }

        $parameters = array();
        $reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters, $dictionary, $action);
        print $hookmanager->resPrint;

        print '</table>';
        print '</div>';

        print '</form>';
    } else {
        setEventMessage($dictionary->errorsToString(), 'errors');
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

    $var = false;
    $lastfamily = '';
    foreach ($dictionaries as $dictionary) {
        $var = !$var;
        if ($dictionary->enabled) {
            $langs->loadLangs($dictionary->langs);

            if ($lastfamily != $dictionary->family) {
                $var = false;
                $lastfamily = $dictionary->family;
                print '<tr class="' . $bc[$var] . ' family_title"><td colspan="4">' . $langs->trans($dictionary->familyLabel) . '</td></tr>';
            }

            print '<tr class="' . $bc[$var] . '"><td width="10px"></td>' .
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
