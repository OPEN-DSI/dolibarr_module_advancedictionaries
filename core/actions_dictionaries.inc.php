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

$canRead = $user->rights->advancedictionaries->read;
$canCreate = $user->rights->advancedictionaries->create;
$canUpdate = $user->rights->advancedictionaries->create;
$canDelete = $user->rights->advancedictionaries->delete;
$canDisable = $user->rights->advancedictionaries->disable;

if (!$canRead) accessforbidden();

$action      = GETPOST('action', 'alpha');
$confirm     = GETPOST('confirm', 'alpha');
$id          = GETPOST('id', 'int');
$rowid       = GETPOST('rowid', 'int');
$prevrowid   = GETPOST('prevrowid', 'int');
$module      = GETPOST('module', 'alpha');
$name        = GETPOST('name', 'alpha');

// Select current dictionary informations
$dictionary = null;
if ($id > 0 || (!empty($module) && !empty($name))) {
    // Select the dictionary
    $dictionary = Dictionary::getDictionary($db, $module, $name, $id);
}

// Load variable for pagination
$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST('sortfield', 'alpha');
$sortorder = GETPOST('sortorder', 'alpha');
$page = GETPOST('page', 'int');
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
$contextpage = isset($dictionary) ? 'dictionary' : 'dictionarylist';
$hookmanager->initHooks(array($contextpage));

// Params
$param = 'module=' . urlencode($dictionary->module) . '&name=' . urlencode($dictionary->name);
if (!empty($sortfield)) $param .= '&sortfield=' . urlencode($sortfield);
if (!empty($sortorder)) $param .= '&sortorder=' . urlencode($sortorder);
if (!empty($page)) $param .= '&page=' . urlencode($page);
if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit=' . $limit;

$search_active = 1;
$search_filters = array();

if (isset($dictionary) && $dictionary->enabled) {
    // Filter active
    $search_active = GETPOST('search_' . $dictionary->active_field, 'int');
    if ($search_active === '') $search_active = 1;

    // Params
    $search_filters = $dictionary->getSearchFieldsValueFromForm();
    foreach ($search_filters as $fieldName => $filter) {
        $param .= '&search_' . $fieldName . '=' . urlencode($filter);
    }
    if ($search_active != 1) $param .= '&search_' . $dictionary->active_field . '=' . urlencode($search_active);
}


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $dictionary, $action);
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook)) {
    if (isset($dictionary) && $dictionary->enabled) {
        // Actions add an entry into a dictionary
        if ($action == 'add' && $dictionary->lineCanBeAdded && $canCreate) {
            $fieldsValue = $dictionary->getFieldsValueFromForm('add_');
            $fieldsValue = array_merge($fieldsValue, $dictionary->getFixedFieldsValue());

            if ($dictionary->addLine($fieldsValue, $user) > 0) {
                setEventMessage($langs->transnoentities("RecordSaved"));
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param);
                exit;
            } else {
                setEventMessages($dictionary->error, $dictionary->errors, 'errors');
            }
        } // Actions edit an entry into a dictionary
        elseif ($action == 'confirm_edit' && !isset($_POST['actioncancel']) && $rowid > 0 && $dictionary->lineCanBeUpdated && $canUpdate) {
            $fieldsValue = $dictionary->getFieldsValueFromForm('edit_');

            if ($dictionary->updateLine($rowid, $fieldsValue, $user) > 0) {
                setEventMessage($langs->transnoentities("RecordSaved"));
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param . '#rowid-' . $rowid);
                exit;
            } else {
                setEventMessages($dictionary->error, $dictionary->errors, 'errors');
                $action = 'edit';
            }
        } // Actions delete an entry into a dictionary
        elseif ($action == 'confirm_delete' && $confirm == 'yes' && $rowid > 0 && $dictionary->lineCanBeDeleted && $canDelete) {
            if ($dictionary->deleteLine($rowid, $user) > 0) {
                setEventMessage($langs->transnoentities("RecordDeleted"));
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param . '#rowid-' . $prevrowid);
                exit;
            } else {
                setEventMessages($dictionary->error, $dictionary->errors, 'errors');
            }
        } // Actions activate an entry into a dictionary
        elseif ($action == 'activate_on' && $canDisable && $rowid > 0) {
            $res = $dictionary->activeLine($rowid, 1, $user);
            if ($res > 0) {
                setEventMessage($langs->transnoentities("RecordSaved"));
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param . '#rowid-' . $rowid);
                exit;
            } elseif ($res < 0) {
                setEventMessages($dictionary->error, $dictionary->errors, 'errors');
            }
        } // Actions disable an entry into a dictionary
        elseif ($action == 'activate_off' && $canDisable && $rowid > 0) {
            $res = $dictionary->activeLine($rowid, 0, $user);
            if ($res > 0) {
                setEventMessage($langs->transnoentities("RecordSaved"));
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $param . '#rowid-' . $rowid);
                exit;
            } elseif ($res < 0) {
                setEventMessages($dictionary->error, $dictionary->errors, 'errors');
            }
        }
    }
}
