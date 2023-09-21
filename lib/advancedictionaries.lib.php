<?php
/* Copyright (C) 2017      Open-DSI                 <support@open-dsi.fr>
 * Copyright (C) 2017      fatpratmatt              <fatpratmatt@gmail.com>
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
 *	\file       htdocs/advancedictionaries/lib/advancedictionaries.lib.php
 * 	\ingroup	advancedictionaries
 *	\brief      Functions for the module advancedictionaries
 */

/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to show
 */
function advancedictionaries_prepare_head()
{
    global $langs, $conf, $user;
    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/advancedictionaries/admin/setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;

    $head[$h][0] = dol_buildpath("/advancedictionaries/admin/dictionaries.php", 1);
    $head[$h][1] = $langs->trans("AdvanceDictionaries");
    $head[$h][2] = 'dictionaries';
    $h++;

    $head[$h][0] = dol_buildpath("/advancedictionaries/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About") . " / " . $langs->trans("Support");
    $head[$h][2] = 'about';
    $h++;

    $head[$h][0] = dol_buildpath("/advancedictionaries/admin/changelog.php", 1);
    $head[$h][1] = $langs->trans("OpenDsiChangeLog");
    $head[$h][2] = 'changelog';
    $h++;

    complete_head_from_modules($conf,$langs,null,$head,$h,'advancedictionaries_admin');

    return $head;
}
