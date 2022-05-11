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
	public function select_dictionary($module, $name, $selected = '', $htmlname = 'dictionaryid', $showempty = '', $key='rowid', $label='{{label}}', $filters=array(), $orders=array('label'=>'ASC'), $forcecombo = 0, $events = array(), $usesearchtoselect=0, $limit = 0, $morecss = 'minwidth100', $moreparam = '', $selected_input_value = '', $hidelabel = 1, $selectlabel = '', $autofocus=0, $ajaxoptions = array(), $options_only=false, $ismultiselect=0)
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
            $out .= ajax_autocompleter($selected, $htmlname, dol_buildpath('/advancedictionaries/ajax/dictionary.php', 1), $urloption, $usesearchtoselect, 0, $ajaxoptions);
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
	public function select_dictionary_list($module, $name, $selected='', $htmlname='dictionaryid', $showempty='', $key='rowid', $label='{{label}}', $filters=array(), $orders=array(), $forcecombo=0, $events=array(), $usesearchtoselect=0, $outputmode=0, $limit=0, $morecss='minwidth100', $moreparam='', $options_only=false, $ismultiselect=0)
    {
        global $conf, $langs;

        dol_syslog(__METHOD__, LOG_DEBUG);

        // Get lines
        dol_include_once('/advancedictionaries/class/dictionary.class.php');
        $dictionary = Dictionary::getDictionary($this->db, $module, $name);
        $lines = $filters === null ? array() : $dictionary->fetch_array($key, $label, $filters, $orders, $limit, 1, false);
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

                $tmp = array('key' => $k, 'value' => $l, 'label' => $l);
                $tmp2 = array_intersect_key(is_array($dictionary->lines[$k]->fields) ? $dictionary->lines[$k]->fields : array(), is_array($dictionary->fields) ? $dictionary->fields : array());
                $tmp = array_merge($tmp, $tmp2);
                array_push($outarray, $tmp);

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
	public function add_select_events($htmlname, $events)
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
	public function multiselect_javascript_code($selected, $htmlname, $elemtype='')
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

	/**
	 *     Show a confirmation HTML form or AJAX popup.
	 *     Easiest way to use this is with useajax=1.
	 *     If you use useajax='xxx', you must also add jquery code to trigger opening of box (with correct parameters)
	 *     just after calling this method. For example:
	 *       print '<script type="text/javascript">'."\n";
	 *       print 'jQuery(document).ready(function() {'."\n";
	 *       print 'jQuery(".xxxlink").click(function(e) { jQuery("#aparamid").val(jQuery(this).attr("rel")); jQuery("#dialog-confirm-xxx").dialog("open"); return false; });'."\n";
	 *       print '});'."\n";
	 *       print '</script>'."\n";
	 *
	 *     @param  	string		$page        	   	Url of page to call if confirmation is OK
	 *     @param	string		$title       	   	Title
	 *     @param	string		$question    	   	Question
	 *     @param 	string		$action      	   	Action
	 *	   @param  	array		$formquestion	   	An array with complementary inputs to add into forms: array(array('label'=> ,'type'=> , ))
	 * 	   @param  	string		$selectedchoice  	"" or "no" or "yes"
	 * 	   @param  	int			$useajax		   	0=No, 1=Yes, 2=Yes but submit page with &confirm=no if choice is No, 'xxx'=Yes and preoutput confirm box with div id=dialog-confirm-xxx
	 *     @param  	int			$height          	Force height of box
	 *     @param	int			$width				Force width of box ('999' or '90%'). Ignored and forced to 90% on smartphones.
	 *     @param	int			$post				Send by form POST.
	 *     @param	int			$post				Resizable box (0=no, 1=yes).
	 *     @param	int			$disableformtag		1=Disable form tag. Can be used if we are already inside a <form> section.
	 *     @return 	string      	    			HTML ajax code if a confirm ajax popup is required, Pure HTML code if it's an html form
	 */
	public function formconfirm($page, $title, $question, $action, $formquestion=array(), $selectedchoice="", $useajax=0, $height=200, $width=500, $post=0, $resizable=0, $disableformtag = 0)
	{
		global $langs, $conf, $form;

		if (!is_object($form)) {
			require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
			$form = new Form($this->db);
		}

		$more = '<!-- formconfirm before calling page='.dol_escape_htmltag($page).' -->';
		$formconfirm = '';
		$inputok = array();
		$inputko = array();

		// Clean parameters
		$newselectedchoice = empty($selectedchoice) ? "no" : $selectedchoice;
		if ($conf->browser->layout == 'phone') {
			$width = '95%';
		}

		// Set height automatically if not defined
		if (empty($height)) {
			$height = 220;
			if (is_array($formquestion) && count($formquestion) > 2) {
				$height += ((count($formquestion) - 2) * 24);
			}
		}

		if (is_array($formquestion) && !empty($formquestion)) {
			if ($post) {
				$more .= '<form id="form_dialog_confirm" name="form_dialog_confirm" action="'.$page.'" method="POST" enctype="multipart/form-data">';
				$more .= '<input type="hidden" id="confirm" name="confirm" value="yes">' . "\n";
				$more .= '<input type="hidden" id="action" name="action" value="'.$action.'">' . "\n";
				$more .= '<input type="hidden" id="token" name="token" value="'.newToken().'">' . "\n";
			}
			// First add hidden fields and value
			foreach ($formquestion as $key => $input) {
				if (is_array($input) && !empty($input)) {
					if ($post && ($input['name'] == "confirm" || $input['name'] == "action")) continue;
					if ($input['type'] == 'hidden') {
						$more .= '<input type="hidden" id="' . $input['name'] . '" name="' . $input['name'] . '" value="' . dol_escape_htmltag($input['value'], 1, 1) . '">' . "\n";
					}
				}
			}

			// Now add questions
			$more .= '<table class="paddingtopbottomonly centpercent noborderspacing">' . "\n";
			$more .= '<tr><td colspan="3">' . (!empty($formquestion['text']) ? $formquestion['text'] : '') . '</td></tr>' . "\n";
			foreach ($formquestion as $key => $input) {
				if (is_array($input) && !empty($input)) {
					$size = (!empty($input['size']) ? ' size="' . $input['size'] . '"' : '');

					if ($input['type'] == 'text') {
						$more .= '<tr class="oddeven"><td class="titlefield">' . $input['label'] . '</td><td colspan="2" align="left"><input type="text" class="flat" id="' . $input['name'] . '" name="' . $input['name'] . '"' . $size . ' value="' . $input['value'] . '" /></td></tr>' . "\n";
					} else if ($input['type'] == 'password') {
						$more .= '<tr class="oddeven"><td class="titlefield">' . $input['label'] . '</td><td colspan="2" align="left"><input type="password" class="flat" id="' . $input['name'] . '" name="' . $input['name'] . '"' . $size . ' value="' . $input['value'] . '" /></td></tr>' . "\n";
					} else if ($input['type'] == 'select') {
						$more .= '<tr class="oddeven"><td class="titlefield">';
						if (!empty($input['label'])) $more .= $input['label'] . '</td><td valign="top" colspan="2" align="left">';
						$more .= $form->selectarray($input['name'], $input['values'], $input['default'], 1);
						$more .= '</td></tr>' . "\n";
					} else if ($input['type'] == 'checkbox') {
						$more .= '<tr class="oddeven">';
						$more .= '<td class="titlefield">' . $input['label'] . ' </td><td align="left">';
						$more .= '<input type="checkbox" class="flat" id="' . $input['name'] . '" name="' . $input['name'] . '"';
						if (!is_bool($input['value']) && $input['value'] != 'false') $more .= ' checked';
						if (is_bool($input['value']) && $input['value']) $more .= ' checked';
						if (isset($input['disabled'])) $more .= ' disabled';
						$more .= ' /></td>';
						$more .= '<td align="left">&nbsp;</td>';
						$more .= '</tr>' . "\n";
					} else if ($input['type'] == 'radio') {
						$i = 0;
						foreach ($input['values'] as $selkey => $selval) {
							$more .= '<tr class="oddeven">';
							if ($i == 0) $more .= '<td class="tdtop titlefield">' . $input['label'] . '</td>';
							else $more .= '<td>&nbsp;</td>';
							$more .= '<td width="20"><input type="radio" class="flat" id="' . $input['name'] . '" name="' . $input['name'] . '" value="' . $selkey . '"';
							if ($input['disabled']) $more .= ' disabled';
							$more .= ' /></td>';
							$more .= '<td align="left">';
							$more .= $selval;
							$more .= '</td></tr>' . "\n";
							$i++;
						}
					} else if ($input['type'] == 'date') {
						$more .= '<tr class="oddeven"><td class="titlefield">' . $input['label'] . '</td>';
						$more .= '<td colspan="2" align="left">';
						$more .= $form->select_date($input['value'], $input['name'], $input['hour'] ? 1 : 0, $input['minute'] ? 1 : 0, 0, '', 1, $input['addnowlink'] ? 1 : 0, 1);
						$more .= '</td></tr>' . "\n";
						$formquestion[] = array('name' => $input['name'] . 'day');
						$formquestion[] = array('name' => $input['name'] . 'month');
						$formquestion[] = array('name' => $input['name'] . 'year');
						$formquestion[] = array('name' => $input['name'] . 'hour');
						$formquestion[] = array('name' => $input['name'] . 'min');
					} else if ($input['type'] == 'other') {
						$more .= '<tr class="oddeven"><td class="titlefield">';
						if (!empty($input['label'])) $more .= $input['label'] . '</td><td colspan="2" align="left">';
						$more .= $input['value'];
						$more .= '</td></tr>' . "\n";
					} else if ($input['type'] == 'onecolumn') {
						$more .= '<tr class="oddeven"><td class="titlefield" colspan="3" align="left">';
						$more .= $input['value'];
						$more .= '</td></tr>' . "\n";
					}
				}
			}
			$more .= '</table>' . "\n";
			if ($post) $more .= '</form>';
		}

		// JQUERY method dialog is broken with smartphone, we use standard HTML.
		// Note: When using dol_use_jmobile or no js, you must also check code for button use a GET url with action=xxx and check that you also output the confirm code when action=xxx
		// See page product/card.php for example
		if (!empty($conf->dol_use_jmobile)) {
			$useajax = 0;
		}
		if (empty($conf->use_javascript_ajax)) {
			$useajax = 0;
		}

		if ($useajax) {
			$autoOpen = true;
			$dialogconfirm = 'dialog-confirm';
			$button = '';
			if (!is_numeric($useajax)) {
				$button = $useajax;
				$useajax = 1;
				$autoOpen = false;
				$dialogconfirm .= '-' . $button;
			}
			$pageyes = $page . (preg_match('/\?/', $page) ? '&' : '?') . 'action=' . $action . '&confirm=yes';
			$pageno = ($useajax == 2 ? $page . (preg_match('/\?/', $page) ? '&' : '?') . 'action=' . $action . '&confirm=no' : '');
			// Add input fields into list of fields to read during submit (inputok and inputko)
			if (is_array($formquestion)) {
				foreach ($formquestion as $key => $input) {
					//print "xx ".$key." rr ".is_array($input)."<br>\n";
					// Add name of fields to propagate with the GET when submitting the form with button OK.
					if (is_array($input) && isset($input['name'])) {
						// Modification Open-DSI - Begin
						if (is_array($input['name'])) $inputok = array_merge($inputok, $input['name']);
						else array_push($inputok, $input['name']);
						// Modification Open-DSI - End
					}
					if (isset($input['inputko']) && $input['inputko'] == 1) array_push($inputko, $input['name']);
				}
			}

			// Show JQuery confirm box.
			$formconfirm .= '<div id="' . $dialogconfirm . '" title="' . dol_escape_htmltag($title) . '" style="display: none;">';
			if (!empty($more)) {
				$formconfirm .= '<div class="confirmquestions">'.$more.'</div>'."\n";
			}
			$formconfirm .= ($question ? '<div class="confirmmessage">' . img_help('', '') . ' ' . $question . '</div>' : '');
			$formconfirm .= '</div>' . "\n";

			$formconfirm .= "\n<!-- begin code of popup for formconfirm page=".$page." -->\n";
			$formconfirm .= '<script type="text/javascript">' . "\n";
			$formconfirm .= "/* Code for the jQuery('#dialogforpopup').dialog() */\n";
			$formconfirm .= 'jQuery(document).ready(function() {
            $(function() {
            	$( "#' . $dialogconfirm . '" ).dialog(
            	{
                    autoOpen: ' . ($autoOpen ? "true" : "false") . ',';
			if ($newselectedchoice == 'no') {
				$formconfirm .= '
						open: function() {
            				$(this).parent().find("button.ui-button:eq(2)").focus();
						},';
			}
			if ($post) {
				$formconfirm .= '
                    resizable: ' . ($resizable ? 'true' : 'false') . ',
                    height: "' . $height . '",
                    width: "' . $width . '",
                    modal: true,
                    closeOnEscape: false,
                    buttons: {
                        "' . dol_escape_js($langs->transnoentities("Yes")) . '": function() {
                            var form_dialog_confirm = $("form#form_dialog_confirm");
                            form_dialog_confirm.find("input#confirm").val("yes");
                            form_dialog_confirm.submit();
                            $(this).dialog("close");
                        },
                        "' . dol_escape_js($langs->transnoentities("No")) . '": function() {
                            if (' . ($useajax == 2 ? '1' : '0') . ' == 1) {
                                var form_dialog_confirm = $("form#form_dialog_confirm");
                                form_dialog_confirm.find("input#confirm").val("no");
                                form_dialog_confirm.submit();
                            }
                            $(this).dialog("close");
                        }
                    }
                }
                );

            	var button = "' . $button . '";
            	if (button.length > 0) {
                	$( "#" + button ).click(function() {
                		$("#' . $dialogconfirm . '").dialog("open");
        			});
                }
            });
            });
            </script>';
			} else {
				$formconfirm .= '
                    resizable: false,
                    height: "' . $height . '",
                    width: "' . $width . '",
                    modal: true,
                    closeOnEscape: false,
                    buttons: {
                        "' . dol_escape_js($langs->transnoentities("Yes")) . '": function() {
                        	var options = "&token='.urlencode(newToken()).'";
                        	var inputok = '.json_encode($inputok).';	/* List of fields into form */
                         	var pageyes = "' . dol_escape_js(!empty($pageyes) ? $pageyes : '') . '";
                         	if (inputok.length>0) {
                         		$.each(inputok, function(i, inputname) {
                         			var more = "";
									var inputvalue;
                         			if ($("input[name=\'" + inputname + "\']").attr("type") == "radio") {
										inputvalue = $("input[name=\'" + inputname + "\']:checked").val();
									} else {
                         			if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
                         				inputvalue = $("#" + inputname + more).val();
									}
                         			if (typeof inputvalue == "undefined") { inputvalue=""; }
									console.log("formconfirm check inputname="+inputname+" inputvalue="+inputvalue);
                         			options += "&" + inputname + "=" + encodeURIComponent(inputvalue);
                         		});
                         	}
                         	var urljump = pageyes + (pageyes.indexOf("?") < 0 ? "?" : "") + options;
            				if (pageyes.length > 0) { location.href = urljump; }
                            $(this).dialog("close");
                        },
                        "' . dol_escape_js($langs->transnoentities("No")) . '": function() {
                        	var options = "&token='.urlencode(newToken()).'";
                         	var inputko = '.json_encode($inputko).';	/* List of fields into form */
                         	var pageno="' . dol_escape_js(!empty($pageno) ? $pageno : '') . '";
                         	if (inputko.length>0) {
                         		$.each(inputko, function(i, inputname) {
                         			var more = "";
                         			if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
                         			var inputvalue = $("#" + inputname + more).val();
                         			if (typeof inputvalue == "undefined") { inputvalue=""; }
                         			options += "&" + inputname + "=" + encodeURIComponent(inputvalue);
                         		});
                         	}
                         	var urljump=pageno + (pageno.indexOf("?") < 0 ? "?" : "") + options;
                         	//alert(urljump);
            				if (pageno.length > 0) { location.href = urljump; }
                            $(this).dialog("close");
                        }
                    }
                }
                );

            	var button = "' . $button . '";
            	if (button.length > 0) {
                	$( "#" + button ).click(function() {
                		$("#' . $dialogconfirm . '").dialog("open");
        			});
                }
            });
            });
            </script>';
			}
			$formconfirm .= "<!-- end ajax formconfirm -->\n";
		} else {
			$formconfirm .= "\n<!-- begin formconfirm page=".dol_escape_htmltag($page)." -->\n";

			if (empty($disableformtag)) {
				$formconfirm .= '<form method="POST" action="' . $page . '" class="notoptoleftroright">' . "\n";
			}

			$formconfirm .= '<input type="hidden" name="action" value="' . $action . '">' . "\n";
			$formconfirm .= '<input type="hidden" name="token" value="'.newToken().'">'."\n";

			$formconfirm .= '<table class="valid centpercent">'."\n";

			// Line title
			$formconfirm .= '<tr class="validtitre"><td class="validtitre" colspan="2">';
			$formconfirm .= img_picto('', 'recent').' '.$title;
			$formconfirm .= '</td></tr>'."\n";

			// Line text
			if (is_array($formquestion) && !empty($formquestion['text'])) {
				$formconfirm .= '<tr class="valid"><td class="valid" colspan="2">'.$formquestion['text'].'</td></tr>'."\n";
			}

			// Line form fields
			if ($more) {
				$formconfirm .= '<tr class="valid"><td class="valid" colspan="2">'."\n";
				$formconfirm .= $more;
				$formconfirm .= '</td></tr>' . "\n";
			}

			// Line with question
			$formconfirm .= '<tr class="valid">';
			$formconfirm .= '<td class="valid">' . $question . '</td>';
			$formconfirm .= '<td class="valid center">';
			$formconfirm .= $this->selectyesno("confirm", $newselectedchoice, 0, false, 0, 0, 'marginleftonly marginrightonly');
			$formconfirm .= '<input class="button valignmiddle confirmvalidatebutton small" type="submit" value="'.$langs->trans("Validate").'">';
			$formconfirm .= '</td>';
			$formconfirm .= '</tr>' . "\n";

			$formconfirm .= '</table>' . "\n";

			if (empty($disableformtag)) {
				$formconfirm .= "</form>\n";
			}
			$formconfirm .= '<br>';

			if (!empty($conf->use_javascript_ajax)) {
				$formconfirm .= '<!-- code to disable button to avoid double clic -->';
				$formconfirm .= '<script type="text/javascript">'."\n";
				$formconfirm .= '
				$(document).ready(function () {
					$(".confirmvalidatebutton").on("click", function() {
						console.log("We click on button");
						$(this).attr("disabled", "disabled");
						setTimeout(\'$(".confirmvalidatebutton").removeAttr("disabled")\', 3000);
						//console.log($(this).closest("form"));
						$(this).closest("form").submit();
					});
				});
				';
				$formconfirm .= '</script>'."\n";
			}

			$formconfirm .= "<!-- end formconfirm -->\n";
		}

		return $formconfirm;
	}

	/**
	 *	Show a multiselect form from an array.
	 *
	 *	@param	string	$htmlname		Name of select
	 *	@param	array	$array			Array with key+value
	 *	@param	array	$selected		Array with key+value preselected
	 *	@param	int		$key_in_label   1 to show key like in "[key] value"
	 *	@param	int		$value_as_key   1 to use value as key
	 *	@param  string	$morecss        Add more css style
	 *	@param  int		$translate		Translate and encode value
	 *  @param	int		$width			Force width of select box. May be used only when using jquery couch. Example: 250, 95%
	 *  @param	string	$moreattrib		Add more options on select component. Example: 'disabled'
	 *  @param	string	$elemtype		Type of element we show ('category', ...). Will execute a formating function on it. To use in readonly mode if js component support HTML formatting.
	 *  @param	string	$placeholder	String to use as placeholder
	 *  @param	int		$addjscombo		Add js combo
	 *	@return	string					HTML multiselect string
	 *  @see selectarray(), selectArrayAjax(), selectArrayFilter()
	 */
	public static function multiselectarrayWithOrder($htmlname, $array, $selected = array(), $key_in_label = 0, $value_as_key = 0, $morecss = '', $translate = 0, $width = 0, $moreattrib = '', $elemtype = '', $placeholder = '', $addjscombo = -1)
	{
		global $conf, $langs;

		$out = '';

		if ($addjscombo < 0) {
			if (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) $addjscombo = 1;
			else $addjscombo = 0;
		}

		// Add code for jquery to use multiselect
		if (!empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) || defined('REQUIRE_JQUERY_MULTISELECT'))
		{
			$out .= "\n".'<!-- JS CODE TO ENABLE select for id '.$htmlname.', addjscombo='.$addjscombo.' -->
						<script>'."\n";
			if ($addjscombo == 1)
			{
				$tmpplugin = "ADselect2Sortable";
				$out .= 'function formatResult(record) {'."\n";
				if ($elemtype == 'category')
				{
					$out .= 'return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png"> \'+record.text+\'</span>\';';
				} else {
					$out .= 'return record.text;';
				}
				$out .= '};'."\n";
				$out .= 'function formatSelection(record) {'."\n";
				if ($elemtype == 'category')
				{
					$out .= 'return \'<span><img src="'.DOL_URL_ROOT.'/theme/eldy/img/object_category.png"> \'+record.text+\'</span>\';';
				} else {
					$out .= 'return record.text;';
				}
				$out .= '};'."\n";
				$out .= '$(document).ready(function () {
							$(\'#'.$htmlname.'\').'.$tmpplugin.'({
								dir: \'ltr\',
								// Specify format function for dropdown item
								formatResult: formatResult,
							 	templateResult: formatResult,		/* For 4.0 */
								// Specify format function for selected item
								formatSelection: formatSelection,
							 	templateSelection: formatSelection		/* For 4.0 */
							});

							/* Add also morecss to the css .select2 that is after the #htmlname, for component that are show dynamically after load, because select2 set
								 the size only if component is not hidden by default on load */
							$(\'#'.$htmlname.' + .select2\').addClass(\''.$morecss.'\');
						});'."\n";
			} elseif ($addjscombo == 2 && !defined('DISABLE_MULTISELECT'))
			{
				// Add other js lib
				// TODO external lib multiselect/jquery.multi-select.js must have been loaded to use this multiselect plugin
				// ...
				$out .= 'console.log(\'addjscombo=2 for htmlname='.$htmlname.'\');';
				$out .= '$(document).ready(function () {
							$(\'#'.$htmlname.'\').multiSelect({
								containerHTML: \'<div class="multi-select-container">\',
								menuHTML: \'<div class="multi-select-menu">\',
								buttonHTML: \'<span class="multi-select-button '.$morecss.'">\',
								menuItemHTML: \'<label class="multi-select-menuitem">\',
								activeClass: \'multi-select-container--open\',
								noneText: \''.$placeholder.'\'
							});
						})';
			}
			$out .= '</script>';
		}

		// Try also magic suggest
		$out .= '<select id="'.$htmlname.'" class="multiselect'.($morecss ? ' '.$morecss : '').'" multiple name="'.$htmlname.'[]"'.($moreattrib ? ' '.$moreattrib : '').($width ? ' style="width: '.(preg_match('/%/', $width) ? $width : $width.'px').'"' : '').'>'."\n";
		if (is_array($array) && !empty($array))
		{
			if ($value_as_key) $array = array_combine($array, $array);

			if (!empty($array))
			{
				$sortedDataArray = array(); //Will contain all data but with selected value sorted
				foreach($selected as $selectedKey) {
					if(!empty($array[$selectedKey])) {
						$sortedDataArray[$selectedKey] = $array[$selectedKey];
						unset($array[$selectedKey]);
					}
				}
				$sortedDataArray = $sortedDataArray + $array;
				foreach ($sortedDataArray as $key => $value)
				{
					$newval = ($translate ? $langs->trans($value) : $value);
					$newval = ($key_in_label ? $key.' - '.$newval : $newval);

					$out .= '<option value="'.$key.'"';
					if (is_array($selected) && !empty($selected) && in_array((string) $key, $selected) && ((string) $key != ''))
					{
						$out .= ' selected';
					}
					$out .= ' data-html="'.dol_escape_htmltag($newval).'"';
					$out .= '>';
					$out .= dol_htmlentitiesbr($newval);
					$out .= '</option>'."\n";
				}
			}
		}
		$out .= '</select>'."\n";

		return $out;
	}

}

