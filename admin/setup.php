<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2017      Open-DSI             <support@open-dsi.fr>
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
 *	    \file       htdocs/advancedictionaries/admin/setup.php
 *		\ingroup    advancedictionaries
 *		\brief      Page to setup advancedictionaries module
 */

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';		// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
dol_include_once('/advancedictionaries/lib/advancedictionaries.lib.php');

$langs->load("admin");
$langs->load("advancedictionaries@advancedictionaries");
$langs->load("opendsi@advancedictionaries");

if (!$user->admin) accessforbidden();

$action = GETPOST('action','alpha');


/*
 *	Actions
 */

if (preg_match('/set_(.*)/',$action,$reg))
{
    $code=$reg[1];
    $value=(GETPOST($code) ? GETPOST($code) : 1);
    $error = 0;

    if (! $error) {
        if (dolibarr_set_const($db, $code, $value, 'chaine', 0, '', $conf->entity) <= 0) {
            $error++;
        }
    }

    if (! $error)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
} elseif (preg_match('/del_(.*)/',$action,$reg)) {
    $code=$reg[1];
    $error = 0;

    if (! $error) {
        if (dolibarr_del_const($db, $code, $conf->entity) <= 0) {
            $error++;
        }
    }

    if (! $error)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

/*
 *	View
 */

$formother = new FormOther($db);

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("AdvanceDictionariesSetup"),$linkback,'title_setup');
print "<br>\n";

$head=advancedictionaries_prepare_head();

dol_fiche_head($head, 'settings', $langs->trans("Module163017Name"), 0, 'action');


print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

$var=true;

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center">&nbsp;</td>'."\n";
print '<td align="right">'.$langs->trans("Value").'</td>'."\n";
print "</tr>\n";

// ADVANCEDICTIONARIES_REPLACE_OLD_DICTIONARIES
$var = !$var;
print '<tr ' . $bc[$var] . '>' . "\n";
print '<td>' . $langs->trans("AdvanceDictionariesReplaceOldDictionariesPage") . '</td>' . "\n";
print '<td align="center">&nbsp;</td>' . "\n";
print '<td align="right">' . "\n";
if (!empty($conf->use_javascript_ajax)) {
    print ajax_constantonoff('ADVANCEDICTIONARIES_REPLACE_OLD_DICTIONARIES_PAGE');
} else {
    if (empty($conf->global->ADVANCEDICTIONARIES_REPLACE_OLD_DICTIONARIES_PAGE)) {
        print '<a href="' . $_SERVER['PHP_SELF'] . '?action=set_ADVANCEDICTIONARIES_REPLACE_OLD_DICTIONARIES_PAGE">' . img_picto($langs->trans("Disabled"), 'switch_off') . '</a>';
    } else {
        print '<a href="' . $_SERVER['PHP_SELF'] . '?action=del_ADVANCEDICTIONARIES_REPLACE_OLD_DICTIONARIES_PAGE">' . img_picto($langs->trans("Enabled"), 'switch_on') . '</a>';
    }
}
print '</td></tr>' . "\n";

print '</table>';

print '<br>';
print '<div align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</div>';

print '</form>';

dol_fiche_end();

llxFooter();

$db->close();
