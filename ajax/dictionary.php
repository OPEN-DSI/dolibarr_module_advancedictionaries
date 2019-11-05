<?php
/* Copyright (C) 2019       Open-Dsi        <support@open-dsi.fr>
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
 * \file    htdocs/advancedictionaries/ajax/dictionary.php
 * \brief   File to return Ajax response on dictionary lines request
 */
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1); // Disables token renewal
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU', '1');
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');
if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX', '1');
//if (! defined('NOREQUIRESOC')) define('NOREQUIRESOC', '1');
//if (! defined('NOCSRFCHECK')) define('NOCSRFCHECK', '1');

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");	// For "custom" directory

$module = GETPOST('module', 'alpha');
$name = GETPOST('name', 'alpha');
$htmlname = GETPOST('htmlname', 'alpha');
$action = GETPOST('action', 'alpha');
$id = GETPOST('id', 'int');
$outjson = (GETPOST('outjson', 'int') ? GETPOST('outjson', 'int') : 0);
$key = GETPOST('key', 'alpha');
$label = GETPOST('label', 'alpha');
$showempty = GETPOST('showempty', 'int');
$filters = GETPOST('filters', 'array');
$orders = GETPOST('orders', 'array');

/*
 * View
 */

top_httphead();
$out = array();

if (!empty($module) && !empty($name)) {
    if (!empty($action) && $action == 'fetch' && !empty($id)) {
        dol_include_once('/advancedictionaries/class/dictionary.class.php');

        $object = Dictionary::getDictionaryLine($db, $module, $name);
        $ret = $object->fetch($id);
        if ($ret > 0) {
            $out = $object->fields;
        }
    } elseif (!empty($htmlname)) {
        dol_include_once('/advancedictionaries/class/html.formdictionary.class.php');
        $langs->load("main");

        $match = preg_grep('/(' . $htmlname . '[0-9]+)/', array_keys($_GET));
        sort($match);
        $idline = (!empty($match[0]) ? $match[0] : '');
        if (GETPOST($htmlname, 'alpha') == '' && (!$idline || !GETPOST($idline, 'alpha'))) {
            print json_encode(array());
            return;
        }

        // When used from jQuery, the search term is added as GET param "term".
        $searchkey = (($idline && GETPOST($idline, 'alpha')) ? GETPOST($idline, 'alpha') : (GETPOST($htmlname, 'alpha') ? GETPOST($htmlname, 'alpha') : ''));

        if (empty($key)) $key = 'rowid';
        if (empty($label)) $label = '{{label}}';
        if (empty($orders)) $orders = array('label' => 'ASC');

        $formdictionary = new FormDictionary($db);
        $out = $formdictionary->select_dictionary_list($module, $name, '', $htmlname, $showempty, $key, $label, $filters, $orders, 0, array(), 0, $outjson);
    }
}

if ($outjson) print json_encode($out);
$db->close();
