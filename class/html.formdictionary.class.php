<?php
/* Copyright (c) 2002-2007  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2004       Benoit Mortier          <benoit.mortier@opensides.be>
 * Copyright (C) 2004       Sebastien Di Cintio     <sdicintio@ressource-toi.org>
 * Copyright (C) 2004       Eric Seigne             <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2017  Regis Houssin           <regis.houssin@capnetworks.com>
 * Copyright (C) 2006       Andre Cianfarani        <acianfa@free.fr>
 * Copyright (C) 2006       Marc Barilley/Ocebo     <marc@ocebo.com>
 * Copyright (C) 2007       Franky Van Liedekerke   <franky.van.liedekerker@telenet.be>
 * Copyright (C) 2007       Patrick Raguin          <patrick.raguin@gmail.com>
 * Copyright (C) 2010       Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2010-2014  Philippe Grand          <philippe.grand@atoo-net.com>
 * Copyright (C) 2011       Herve Prot              <herve.prot@symeos.com>
 * Copyright (C) 2012-2016  Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2012       Cedric Salvador         <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2015  Raphaël Doursenaud      <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2014       Alexandre Spangaro      <aspangaro.dolibarr@gmail.com>
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
 *	\file       /htdocs/advancedictionaries/core/class/html.formdictionary.class.php
 *  \ingroup    advancedictionaries
 *	\brief      File of class with all html predefined components for dictionaries
 */


/**
 *	Class to manage generation of HTML components
 *	Only common components must be here.
 *
 */
class FormDictionary
{
    var $db;
    var $error;
    var $num;

    /**
     * Constructor
     *
     * @param   DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     *  Output html form to select a third party
     *
     * @param   string      $module                 Name of the module containing the dictionary
     * @param   string      $name                   Name of dictionary
     * @param   string      $selected               Preselected type
     * @param   string      $htmlname               Name of field in form
     * @param   string      $key                    Field name of the dictionary for the key of the line
     * @param   string      $label                  Label pattern for the label of the line (replace {{FieldName}} by this value)
     * @param   array       $filters                List of filters: array(fieldName => value), value is a array search a list of rowid, if $filters = null then return no lines
     * @param   array       $orders                 Order by: array(fieldName => order, ...)
     * @param   string      $showempty              Add an empty field (Can be '1' or text key to use on empty line like 'SelectThirdParty')
     * @param   int         $forcecombo             Force to use combo box
     * @param   array       $events                 Ajax event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
     * @param  	int		    $usesearchtoselect	    Minimum length of input string to start autocomplete
     * @param   int         $limit                  Maximum number of elements
     * @param   string      $morecss                Add more css styles to the SELECT component
     * @param   string      $moreparam              Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
     * @param   string      $selected_input_value   Value of preselected input text (for use with ajax)
     * @param   int         $hidelabel              Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
     * @param   string      $selectlabel            Text of the label (can be translated)
     * @param   int         $autofocus              Autofocus the field in form (1 auto focus, 0 not)
     * @param   array       $ajaxoptions            Options for ajax_autocompleter
     * @param   bool        $options_only           Return options only (for ajax treatment)
     * @param   int         $ismultiselect          0=Not multiselect, 1=Multiselect
     * @return  string                              HTML string with select box for thirdparty.
     */
    function select_dictionary($module, $name, $selected = '', $htmlname = 'dictionaryid', $showempty = '', $key='rowid', $label='{{label}}', $filters=array(), $orders=array('label'=>'ASC'), $forcecombo = 0, $events = array(), $usesearchtoselect=0, $limit = 0, $morecss = 'minwidth100', $moreparam = '', $selected_input_value = '', $hidelabel = 1, $selectlabel = '', $autofocus=0, $ajaxoptions = array(), $options_only=false, $ismultiselect=0)
    {
        global $conf, $langs;

        $out = '';

        if (!empty($conf->use_javascript_ajax) && !empty($usesearchtoselect) && !$forcecombo && !$options_only) {
            // No immediate load of all database
            $placeholder = '';
            if ($selected && empty($selected_input_value)) {
                dol_include_once('/advancedictionaries/class/dictionary.class.php');
                $dictionaryLine = Dictionary::getDictionaryLine($this->db, $module, $name);
                $dictionaryLine->fetch($selected);
                $selected_input_value = $dictionaryLine->getLabel($label);
                unset($dictionaryLine);
            }
            // mode 1
            $urloption = 'module=' . $module . '&name=' . $name . '&htmlname=' . $htmlname . '&key=' . $key . '&label=' . $label . '&showempty=' . $showempty . '&outjson=1&' . http_build_query(array('filters' => $filters));
            $out .= ajax_autocompleter($selected, $htmlname, dol_buildpath('/advancedictionaries/ajax/dictionary.php', 2), $urloption, $usesearchtoselect, 0, $ajaxoptions);
            $out .= '<style type="text/css">
					.ui-autocomplete {
						z-index: 250;
					}
				</style>';
            if (empty($hidelabel)) print $langs->trans($selectlabel) . ' : ';
            else if ($hidelabel > 1) {
                if (!empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder = ' placeholder="' . $langs->trans($selectlabel) . '"';
                else $placeholder = ' title="' . $langs->trans($selectlabel) . '"';
                if ($hidelabel == 2) {
                    $out .= img_picto($langs->trans("Search"), 'search');
                }
            }
            $out .= '<input type="text" class="' . $morecss . '" name="search_' . $htmlname . '" id="search_' . $htmlname . '" value="' . $selected_input_value . '"' . $placeholder . ' ' . (!empty($autofocus) ? 'autofocus' : '') . ' />';
            if ($hidelabel == 3) {
                $out .= img_picto($langs->trans("Search"), 'search');
            }
        } else {
            // Immediate load of all database
            $out .= $this->select_dictionary_list($module, $name, $selected, $htmlname, $showempty, $key, $label, $filters, $orders, $forcecombo, $events, $usesearchtoselect, 0, $limit, $morecss, $moreparam, $options_only, $ismultiselect);
        }

        return $out;
    }

    /**
     *  Output html form to select a dictionary.
     *  Note, you must use the select_dictionary to get the component to select a dictionary. This function must only be called by select_dictionary.
     *
     * @param   string          $module                 Name of the module containing the dictionary
     * @param   string          $name                   Name of dictionary
     * @param	string|array	$selected               Preselected values
     * @param   string	        $htmlname               Name of field in form
     * @param   string          $key                    Field name for the key of the line
     * @param   string          $label                  Label pattern for the label of the line (replace {{FieldName}} by this value)
     * @param   array           $filters                List of filters: array(fieldName => value), value is a array search a list of rowid, if $filters = null then return no lines
     * @param   array           $orders                 Order by: array(fieldName => order, ...)
     * @param	string	        $showempty		        Add an empty field (Can be '1' or text to use on empty line like 'SelectThirdParty')
     * @param	int		        $forcecombo		        Force to use combo box
     * @param	array	        $events			        Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
     * @param  	int		        $usesearchtoselect	    Minimum length of input string to start autocomplete
     * @param	int		        $outputmode		        0=HTML select string, 1=Array
     * @param	int		        $limit			        Limit number of answers
     * @param	string	        $morecss		        Add more css styles to the SELECT component
     * @param   string	        $moreparam              Add more parameters onto the select tag. For example 'style="width: 95%"' to avoid select2 component to go over parent container
     * @param   bool            $options_only           Return options only (for ajax treatment)
     * @param   int             $ismultiselect          0=Not multiselect, 1=Multiselect
     * @return	string					                HTML string with
     */
    function select_dictionary_list($module, $name, $selected='', $htmlname='dictionaryid', $showempty='', $key='rowid', $label='{{label}}', $filters=array(), $orders=array(), $forcecombo=0, $events=array(), $usesearchtoselect=0, $outputmode=0, $limit=0, $morecss='minwidth100', $moreparam='', $options_only=false, $ismultiselect=0)
    {
        global $conf, $langs;

        dol_syslog(__METHOD__, LOG_DEBUG);

        // Get lines
        dol_include_once('/advancedictionaries/class/dictionary.class.php');
        $dictionary = Dictionary::getDictionary($this->db, $module, $name);
        $lines = $filters === null ? array() : $dictionary->fetch_array($key, $label, $filters, $orders, $limit);
        if (empty($dictionary->error)) {
            $out = '';
            $outarray = array();

            // Build output string
            if ($conf->use_javascript_ajax && !$forcecombo && !$options_only) {
                include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
                if ($ismultiselect) {
                    $comboenhancement = $this->multiselect_javascript_code($selected, $htmlname, $elemtype='');
                    $comboenhancement .= $this->add_select_events($htmlname, $events);
                } else {
                    $comboenhancement = ajax_combobox($htmlname, $events, $usesearchtoselect);
                }
                $out .= $comboenhancement;
            }

            // Construct $out and $outarray
            if (!$options_only) $out .= '<select id="' . $htmlname . '" class="flat' . ($morecss ? ' ' . $morecss : '') . '"' . ($moreparam ? ' ' . $moreparam : '') . ' name="' . $htmlname . '">' . "\n";

            $textifempty = '';
            // Do not use textifempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
            //if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
            if (!empty($usesearchtoselect)) {
                if ($showempty && !is_numeric($showempty)) $textifempty = $langs->trans($showempty);
                else $textifempty .= $langs->trans("All");
            }
            if ($showempty) $out .= '<option value="-1">' . $textifempty . '</option>' . "\n";

            $selected = !empty($selected) ? (is_array($selected) ? $selected : explode(',', $selected)) : array();

            $num = count($lines);
            $i = 0;
            foreach ($lines as $k => $l) {
                if (in_array($k, $selected)) {
                    $out .= '<option value="' . $k . '" selected>' . $l . '</option>';
                } else {
                    $out .= '<option value="' . $k . '">' . $l . '</option>';
                }

                array_push($outarray, array('key' => $k, 'value' => $l, 'label' => $l));

                if (($i % 10) == 0) $out .= "\n";
                $i++;
            }
            if (!$options_only) $out .= '</select>' . "\n";

            $this->result = array('nboflines' => $num);

            $this->num = $num;
            if ($outputmode) return $outarray;
            return $out;
        } else {
            $this->error = $dictionary->error;
            if ($outputmode) return array();
            return -1;
        }
    }

    /**
   	 * Return list of labels (translated) of education
   	 *
     * @param	string	$htmlname	Name of html select field ('myid' or '.myclass')
     * @param	array	$events		Event options. Example: array(array('action'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'done_action'=>array('disabled' => array('add-customer-contact'))))
     *                                  'url':          string,  Url of the Ajax script
     *                                  'action':       string,  Action name for the Ajax script
     *                                  'params':       array(), Others parameters send for Ajax script (exclude name: id, action, htmlname), if value = '{{selector}}' get value of the 'selector' input
     *                                  'htmlname':     string,  Id of the select updated with new options from Ajax script
     *                                  'done_action':  array(), List of action done when new options get successfully
     *                                      'empty_select': array(), List of html ID of select to empty
     *                                      'disabled'    : array(), List of html ID to disable if no options
   	 * @return  string
   	 */
   	function add_select_events($htmlname, $events)
    {
        global $conf;

        $out = '';
        if (!empty($conf->use_javascript_ajax)) {
            $out .= '<script type="text/javascript">
            $(document).ready(function () {
                jQuery("#'.$htmlname.'").change(function () {
                    var obj = '.json_encode($events).';
                    $.each(obj, function(key,values) {
                        if (values.action.length) {
                            runJsCodeForEvent'.$htmlname.'(values);
                        }
                    });
                });
    
                function runJsCodeForEvent'.$htmlname.'(obj) {
                    console.log("Run runJsCodeForEvent'.$htmlname.'");
                    var id = $("#'.$htmlname.'").val();
                    var action = obj.action;
                    var url = obj.url;
                    var htmlname = obj.htmlname;
                    var datas = {
                        action: action,
                        id: id,
                        htmlname: htmlname,
                    };
                    var selector_regex = new RegExp("^\\{\\{(.*)\\}\\}$", "i");
                    $.each(obj.params, function(key, value) {
                        var match = null;
                        if ($.type(value) === "string") match = value.match(selector_regex);
                        if (match) {
                            datas[key] = $(match[1]).val();
                        } else {
                            datas[key] = value;
                        }
                    });
                    var input = $("select#" + htmlname);
                    var inputautocomplete = $("#inputautocomplete"+htmlname);
                    $.getJSON(url, datas,
                        function(response) {
                            input.html(response.value);
                            if (response.num) {
                                var selecthtml_dom = $.parseHTML(response.value);
                                inputautocomplete.val(selecthtml_dom.innerHTML);
                            } else {
                                inputautocomplete.val("");
                            }
                                
                            var num = response.num;
                            $.each(obj.done_action, function(key, action) {
                                switch (key) {
                                    case "empty_select":
                                        $.each(action, function(id) {
                                            $("select#" + id).html("");
                                        });
                                        break;
                                    case "disabled":
                                        $.each(action, function(id) {
                                            if (num > 0) {
                                                $("#" + id).removeAttr("disabled");
                                            } else {
                                                $("#" + id).attr("disabled", "disabled");
                                            }
                                        });
                                        break;
                                }
                            });
                                
                            input.change();	/* Trigger event change */
                            
                            if (response.num < 0) {
                                console.error(response.error);
                            }
                        }
                    );
                }
            });
            </script>';
        }

        return $out;
    }

    /**
     *	Return multiselect javascript code
     *
     *  @param	array	$selected       Preselected values
     *  @param  string	$htmlname       Field name in form
     *  @param	string	$elemtype		Type of element we show ('category', ...)
     *  @return	string
     */
    function multiselect_javascript_code($selected, $htmlname, $elemtype='')
    {
        global $conf;

        $out = '';

        // Add code for jquery to use multiselect
       	if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) || defined('REQUIRE_JQUERY_MULTISELECT'))
       	{
            $selected = array_values($selected);
       		$tmpplugin=empty($conf->global->MAIN_USE_JQUERY_MULTISELECT)?constant('REQUIRE_JQUERY_MULTISELECT'):$conf->global->MAIN_USE_JQUERY_MULTISELECT;
      			$out.='<!-- JS CODE TO ENABLE '.$tmpplugin.' for id '.$htmlname.' -->
       			<script type="text/javascript">
   	    			function formatResult(record) {'."\n";
   						if ($elemtype == 'category')
   						{
   							$out.='	//return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png'.'"> <a href="'.DOL_URL_ROOT.'/categories/viewcat.php?type=0&id=\'+record.id+\'">\'+record.text+\'</a></span>\';
   								  	return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png'.'"> \'+record.text+\'</span>\';';
   						}
   						else
   						{
   							$out.='return record.text;';
   						}
   			$out.= '	};
       				function formatSelection(record) {'."\n";
   						if ($elemtype == 'category')
   						{
   							$out.='	//return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png'.'"> <a href="'.DOL_URL_ROOT.'/categories/viewcat.php?type=0&id=\'+record.id+\'">\'+record.text+\'</a></span>\';
   								  	return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png'.'"> \'+record.text+\'</span>\';';
   						}
   						else
   						{
   							$out.='return record.text;';
   						}
   			$out.= '	};
   	    			$(document).ready(function () {
   	    			    $(\'#'.$htmlname.'\').attr("name", "'.$htmlname.'[]");
   	    			    $(\'#'.$htmlname.'\').attr("multiple", "multiple");
   	    			    //$.map('.json_encode($selected).', function(val, i) {
   	    			        $(\'#'.$htmlname.'\').val('.json_encode($selected).');
   	    			    //});
   	    			
       					$(\'#'.$htmlname.'\').'.$tmpplugin.'({
       						dir: \'ltr\',
   							// Specify format function for dropdown item
   							formatResult: formatResult,
       					 	templateResult: formatResult,		/* For 4.0 */
   							// Specify format function for selected item
   							formatSelection: formatSelection,
       					 	templateResult: formatSelection		/* For 4.0 */
       					});
       				});
       			</script>';
       	}

       	return $out;
    }
}

