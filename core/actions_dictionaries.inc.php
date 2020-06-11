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
 *	    \file       htdocs/advancedictionaries/core/actions_dictionaries.inc.php
 *		\ingroup    advancedictionaries
 *		\brief      Include this page in first for manage actions
 */

global $bc, $conf, $db, $langs, $user;

// Libraries
dol_include_once('/advancedictionaries/lib/advancedictionaries.lib.php');
dol_include_once('/advancedictionaries/class/dictionary.class.php');

$langs->load("advancedictionaries@advancedictionaries");

if (!$canRead) accessforbidden();

// Select current dictionary informations
$dictionary = null;
if ($id > 0 || (!empty($module) && !empty($name))) {
    // Select the dictionary
    $dictionary = Dictionary::getDictionary($db, $module, $name, $id);
}

$massaction = GETPOST('massaction','alpha');
$show_files = GETPOST('show_files','int');
$toselect = GETPOST('toselect', 'array');
$search_btn=GETPOST('button_search','alpha');
$search_remove_btn=GETPOST('button_removefilter','alpha');

// Load variable for pagination
$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST('sortfield', 'alpha');
$sortorder = GETPOST('sortorder', 'alpha');
$page = GETPOST('page', 'int');
if (empty($page) || $page == -1 || !empty($search_btn) || !empty($search_remove_btn) || (empty($toselect) && $massaction === '0')) { $page = 0; }     // If $page is not defined, or '' or -1
$order_by = array();
if (empty($sortfield)) {
    $orders = explode(',', $dictionary->listSort);
    foreach ($orders as $order) {
        $tmp = explode(' ', trim($order));
        $order_by[$tmp[0]] = $tmp[1];
    }
} else {
    $order_by[$sortfield] = $sortorder;
}
// If $page is not defined, or '' or -1
if (empty($page) || $page == -1) {
    $page = 0;
}
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Init hook
$contextpage = isset($dictionary) ? $dictionary->module . '_' . $dictionary->name . '_dictionarylineslist' : 'dictionarieslist';
$hookmanager->initHooks(array($contextpage));

// Params
$param = 'module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name);
if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit=' . $limit;

$search_active = 1;
$search_filters = array();
$arrayfields = array();

if (isset($dictionary) && $dictionary->enabled) {
    // Set columns
    $arrayfields = array();
    foreach ($dictionary->fields as $fieldName => $field) {
        if (!empty($field['is_not_show'])) continue;
        $arrayfields[$fieldName] = array(
            'label' => $langs->trans($field['label']),
            'checked' => !isset($field['show_column_by_default']) ? 1 : (!empty($field['show_column_by_default']) ? 1 : 0),
        );
        if (isset($field['position_column'])) $arrayfields[$fieldName]['position'] = $field['position_column'];
        if (isset($field['enabled_column'])) $arrayfields[$fieldName]['enabled'] = $field['enabled_column'];
    }

    // Filters
    $search_active = GETPOST('search_' . $dictionary->active_field, 'int');
    if ($search_active === '') $search_active = 1;
    $search_filters = $dictionary->getSearchFieldsValueFromForm();
}


/*
 * Actions
 */

$error = 0;

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $dictionary, $action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

// Do we click on purge search criteria ? All tests are required to be compatible with all browsers
if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') || GETPOST('button_removefilter','alpha')) {
    $search_filters=array();
    $search_active = 1;
    $toselect=array();
}

if (isset($dictionary) && $dictionary->enabled) {
    // Params
    foreach ($search_filters as $fieldName => $filter) $param .= '&search_' . $fieldName . '=' . urlencode($filter);
    if ($search_active != 1) $param .= '&search_' . $dictionary->active_field . '=' . urlencode($search_active);
    $param2 = $param;
    if (!empty($page)) $param2 .= '&page=' . urlencode($page);
    $param3 = $param2;
    if (!empty($sortfield)) $param3 .= '&sortfield=' . urlencode($sortfield);
    if (!empty($sortorder)) $param3 .= '&sortorder=' . urlencode($sortorder);
}

if (empty($reshook)) {
    if (isset($dictionary) && $dictionary->enabled) {
        $result = 0;
        if (method_exists($dictionary, 'doActions')) {
            $result = $dictionary->doActions();
        }
        if ($result < 0) {
            setEventMessages($dictionary->error, $dictionary->errors, 'errors');
        } elseif ($result == 0) {
            // Actions add an entry into a dictionary
            if ($action == 'confirm_add_line' && $confirm == 'yes' && $dictionary->lineCanBeAdded && $canCreate) {
                $fieldsValue = $dictionary->getFieldsValueFromForm('add_');
				$fieldsValue += $dictionary->getFixedFieldsValue();

                if ($dictionary->addLine($fieldsValue, $user) > 0) {
                    setEventMessage($langs->transnoentities("RecordSaved"));
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param2);
                    exit;
                } else {
                    setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                    $action = 'add_line';
                    $error++;
                }
            } // Actions edit an entry into a dictionary
            elseif ($action == 'confirm_edit_line' && $confirm == 'yes' && $dictionary->lineCanBeUpdated && $canUpdate) {
                $fieldsValue = $dictionary->getFieldsValueFromForm('edit_', '', 1);
				$fieldsValue += $dictionary->getFixedFieldsValue();

                if ($dictionary->updateLine($rowid, $fieldsValue, $user) > 0) {
                    setEventMessage($langs->transnoentities("RecordSaved"));
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param2 . '#rowid-' . $rowid);
                    exit;
                } else {
                    setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                    $action = 'edit_line';
                    $error++;
                }
            } // Actions delete an entry into a dictionary
            elseif ($action == 'confirm_delete_line' && $confirm == 'yes' && $dictionary->lineCanBeDeleted && $canDelete) {
                if ($dictionary->deleteLine($rowid, $user) > 0) {
                    setEventMessage($langs->transnoentities("RecordDeleted"));
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param2 . '#rowid-' . $prevrowid);
                    exit;
                } else {
                    setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                    $error++;
                }
            } // Actions activate an entry into a dictionary
            elseif ($action == 'activate_on' && $canDisable) {
                $res = $dictionary->activeLine($rowid, 1, $user);
                if ($res > 0) {
                    setEventMessage($langs->transnoentities("RecordSaved"));
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param2 . '#rowid-' . $rowid);
                    exit;
                } elseif ($res < 0) {
                    setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                    $error++;
                }
            } // Actions disable an entry into a dictionary
            elseif ($action == 'activate_off' && $canDisable) {
                $res = $dictionary->activeLine($rowid, 0, $user);
                if ($res > 0) {
                    setEventMessage($langs->transnoentities("RecordSaved"));
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param2 . '#rowid-' . $rowid);
                    exit;
                } elseif ($res < 0) {
                    setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                    $error++;
                }
            }

            $dicLine = $dictionary->getNewDictionaryLine();
            $objectclass = get_class($dicLine);
            $objectlabel = 'AdvanceDictionariesDictionaryLines';
            $permtoread = $canRead;
            $permtodelete = $canDelete;
            $uploaddir = $conf->advancedictionaries->dir_output;
            include DOL_DOCUMENT_ROOT . '/core/actions_massactions.inc.php';
        }
    }
}
