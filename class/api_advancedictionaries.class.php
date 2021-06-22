<?php
/* Copyright (C) 2018	Open-DSI	        <support@open-dsi.fr>
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

use Luracast\Restler\RestException;

dol_include_once('/advancedictionaries/class/dictionary.class.php');

/**
 * API class for Advanced Dictionaries
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class AdvanceDictionariesApi extends DolibarrApi {
    /**
     * @var Dictionary[]        $dictionary_cached          Cached dictionary object list
     */
    static protected $dictionary_cached;
    /**
     * @var DictionaryLine[]    $dictionary_line_cached     Cached dictionary line object list
     */
    static protected $dictionary_line_cached;


    /**
     *  Constructor
     */
	public function __construct()
    {
        global $db, $user;

        $user = DolibarrApiAccess::$user;
        if (version_compare(DOL_VERSION, "12.0.0") >= 0) {
			$this->db = $db;
		} else {
			self::$db = $db;
		}
    }

    /**
     *  Get the line
     *
     * @url	GET /{module}/{name}/{id}
     *
     * @param   int             $id                 ID of the line
     * @param   string          $module             Name of the module containing the dictionary
     * @param   string          $name               Name of dictionary
     * @param   int             $old_id             Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  array                               Line data without useless information
     *
     * @throws  401             RestException       Insufficient rights
     * @throws  404             RestException       Dictionary not found
     * @throws  404             RestException       Line not found
     * @throws  500             RestException       Error when retrieve the line
     */
	public function get($id, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->read) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);
        $this->_getLineObject($dictionary_line, $id);

        return $this->_cleanLineObjectData($dictionary_line);
    }

    /**
     *  Get the list of lines
     *
     * @url	GET /{module}/{name}
     *
     * @param   string      $module             Name of the module containing the dictionary
     * @param   string      $name               Name of dictionary
     * @param   int         $old_id             Id of the dictionary for old dolibarr dictionary (not supported)
     * @param   string	    $sort_field         Sort field (field name)
     * @param   string	    $sort_order         Sort order (ASC or DESC)
     * @param   int		    $limit		        Limit for list
     * @param   int		    $page		        Page number
     *
     * @return  array                           Array of line objects
     *
     * @throws  400         RestException       Error when validating parameter 'sqlfilters'
     * @throws  401         RestException       Insufficient rights
     * @throws  500         RestException       Error when retrieve line list
     */
	public function index($module, $name, $old_id=0, $sort_field='', $sort_order='ASC', $limit=100, $page=0)
    {
        $obj_ret = array();

        if (!DolibarrApiAccess::$user->rights->advancedictionaries->read) {
            throw new RestException(401, "Insufficient rights");
        }

        // Orders
        $orders = array();
        if (!empty($sort_field)) $orders = array($sort_field => (!empty($sort_order) ? $sort_order : 'ASC'));

        // Offset and limit
        $offset = 0;
        if ($limit) {
            if (!($page > 0)) $page = 0;
            $offset = $limit * $page;
        }

        // Get dictionary object
        $dictionary = $this->_getDictionaryObject($module, $name, $old_id);
        $result = $dictionary->fetch_lines(-1, array(), $orders, $offset, $limit);
        if ($result < 0) {
            throw new RestException(500, "Error when retrieve line list", [ 'details' => $this->_getErrors($dictionary) ]);
        }

        foreach ($dictionary->lines as $line) {
            $obj_ret[] = $this->_cleanLineObjectData($line);
        }

        return $obj_ret;
    }

    /**
     *  Create a line
     *
     * @url	POST /{module}/{name}
     *
     * @param   array       $line_data      Line data
     * @param   string      $module         Name of the module containing the dictionary
     * @param   string      $name           Name of dictionary
     * @param   int         $old_id         Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  int                         ID of the line created
     *
     * @throws  401     RestException       Insufficient rights
     * @throws  401     RestException       Line cannot be added
     * @throws  404     RestException       Dictionary not found
     * @throws  404     RestException       Line not found
     * @throws  500     RestException       Error when retrieve the line
     * @throws  500     RestException       Error while creating the line
     */
	public function post($line_data, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->create) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);

        if (!$dictionary_line->dictionary->lineCanBeAdded) {
            throw new RestException(401, "Line cannot be added");
        }

        // Check fields
        $this->_checkFields($dictionary_line, $line_data);

        if ($dictionary_line->insert($line_data, DolibarrApiAccess::$user) < 0) {
            throw new RestException(500, "Error while creating the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }

        return $dictionary_line->id;
    }

    /**
     *  Update a line
     *
     * @url	PUT /{module}/{name}/{id}
     *
     * @param   int         $id             ID of the line
     * @param   array       $line_data      Line data
     * @param   string      $module         Name of the module containing the dictionary
     * @param   string      $name           Name of dictionary
     * @param   int         $old_id         Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  array                       Line data updated
     *
     * @throws  401         RestException   Insufficient rights
     * @throws  401         RestException   Line cannot be updated
     * @throws  404         RestException   Dictionary not found
     * @throws  404         RestException   Line not found
     * @throws  500         RestException   Error when retrieve the line
     * @throws  500         RestException   Error while updating the line
     */
	public function put($id, $line_data, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->create) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);

        if (!$dictionary_line->dictionary->lineCanBeUpdated) {
            throw new RestException(401, "Line cannot be updated");
        }

        // Load line
        $this->_getLineObject($dictionary_line, $id);

        // Check fields
        $this->_checkFields($dictionary_line, $line_data);

        if ($dictionary_line->update($line_data, DolibarrApiAccess::$user) > 0) {
            return $this->get($id, $module, $name, $old_id);
        } else {
            throw new RestException(500, "Error while updating the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }
    }

    /**
     *  Delete line
     *
     * @url	DELETE /{module}/{name}/{id}
     *
     * @param   int     $id             ID of the line
     * @param   string  $module         Name of the module containing the dictionary
     * @param   string  $name           Name of dictionary
     * @param   int     $old_id         Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  array
     *
     * @throws  401     RestException   Insufficient rights
     * @throws  401     RestException   Line cannot be deleted
     * @throws  404     RestException   Dictionary not found
     * @throws  404     RestException   Line not found
     * @throws  500     RestException   Error when retrieve the line
     */
	public function delete($id, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->delete) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);

        if (!$dictionary_line->dictionary->lineCanBeDeleted) {
            throw new RestException(401, "Line cannot be deleted");
        }

        // Load line
        $this->_getLineObject($dictionary_line, $id);

        if ($dictionary_line->delete(DolibarrApiAccess::$user) < 0) {
            throw new RestException(500, "Error while deleting the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }

        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Line deleted'
            )
        );
    }

    /**
     *  Activate line
     *
     * @url	PUT /{module}/{name}/{id}/activate
     *
     * @param   int     $id             ID of the line
     * @param   string  $module         Name of the module containing the dictionary
     * @param   string  $name           Name of dictionary
     * @param   int     $old_id         Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  array
     *
     * @throws  401     RestException   Insufficient rights
     * @throws  401     RestException   Line cannot be deleted
     * @throws  404     RestException   Dictionary not found
     * @throws  404     RestException   Line not found
     * @throws  500     RestException   Error when retrieve the line
     */
	public function activate($id, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->disable) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);

        if (!$dictionary_line->dictionary->isLineCanBeDisabled($dictionaryLine)) {
            throw new RestException(401, "Line cannot be activated");
        }

        // Load line
        $this->_getLineObject($dictionary_line, $id);

        if ($dictionary_line->active(1, DolibarrApiAccess::$user) < 0) {
            throw new RestException(500, "Error while deleting the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }

        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Line activated'
            )
        );
    }

    /**
     *  Deactivate line
     *
     * @url	PUT /{module}/{name}/{id}/deactivate
     *
     * @param   int     $id             ID of the line
     * @param   string  $module         Name of the module containing the dictionary
     * @param   string  $name           Name of dictionary
     * @param   int     $old_id         Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  array
     *
     * @throws  401     RestException   Insufficient rights
     * @throws  401     RestException   Line cannot be deleted
     * @throws  404     RestException   Dictionary not found
     * @throws  404     RestException   Line not found
     * @throws  500     RestException   Error when retrieve the line
     */
	public function deactivate($id, $module, $name, $old_id=0)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->disable) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get line object
        $dictionary_line = $this->_getDictionaryLineObject($module, $name, $old_id);

        if (!$dictionary_line->dictionary->isLineCanBeDisabled($dictionaryLine)) {
            throw new RestException(401, "Line cannot be deactivated");
        }

        // Load line
        $this->_getLineObject($dictionary_line, $id);

        if ($dictionary_line->active(0, DolibarrApiAccess::$user) < 0) {
            throw new RestException(500, "Error while deleting the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }

        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Line deactivated'
            )
        );
    }

    /**
     *  Get list of available dictionaries
     *
     * @url	GET /dictionaries
     *
     * @param   string  $module         Name of the module containing the dictionary
     * @param   string  $family         Name of dictionary
     * @param   int     $enabled        Get: -1:all, 0:disable, 1:enabled
     * @param   int     $hidden         Get: -1:all, 0:hidden, 1:not hidden
     *
     * @return  array
     *
     * @throws  401     RestException   Insufficient rights
     */
	public function indexDictionaries($module='', $family='', $enabled=1, $hidden=-1)
    {
        if (!DolibarrApiAccess::$user->rights->advancedictionaries->read) {
            throw new RestException(401, "Insufficient rights");
        }

        // Get dictionaries
        $obj_ret = array();
        $db = version_compare(DOL_VERSION, "12.0.0") >= 0 ? $this->db : self::$db;
        $dictionaries = Dictionary::fetchAllDictionaries($db, $module, $family);
        foreach ($dictionaries as $dictionary) {
            if (($enabled < 0 || ($enabled == 0 && !$dictionary->enabled) || ($enabled == 1 && $dictionary->enabled)) &&
                ($hidden < 0 || ($hidden == 0 && !$dictionary->hidden) || ($hidden == 1 && $dictionary->hidden))
            ) {
                $obj_ret[] = $this->_cleanDictionaryObjectData($dictionary);
            }
        }

        return $obj_ret;
    }

    /**
     *  Get dictionary object
     *
     * @param   string          $module             Name of the module containing the dictionary
     * @param   string          $name               Name of dictionary
     * @param   int             $old_id             Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  Dictionary
     *
     * @throws  404             RestException       Dictionary not found
     */
	protected function _getDictionaryObject($module, $name, $old_id=0)
    {
		$db = version_compare(DOL_VERSION, "12.0.0") >= 0 ? $this->db : self::$db;
        if (!isset(self::$dictionary_cached[$module][$name][$old_id])) {
            self::$dictionary_cached[$module][$name][$old_id] = Dictionary::getDictionary($db, $module, $name, $old_id);
        }

        if (!isset(self::$dictionary_cached[$module][$name][$old_id])) {
            throw new RestException(404, "Dictionary not found");
        }

        return self::$dictionary_cached[$module][$name][$old_id];
    }

    /**
     *  Get dictionary line object
     *
     * @param   string          $module             Name of the module containing the dictionary
     * @param   string          $name               Name of dictionary
     * @param   int             $old_id             Id of the dictionary for old dolibarr dictionary (not supported)
     *
     * @return  DictionaryLine
     *
     * @throws  404             RestException       Dictionary not found
     */
	protected function _getDictionaryLineObject($module, $name, $old_id=0)
    {
        if (!isset(self::$dictionary_line_cached[$module][$name][$old_id])) {
            $dictionary = $this->_getDictionaryObject($module, $name, $old_id=0);
            self::$dictionary_line_cached[$module][$name][$old_id] = $dictionary->getNewDictionaryLine();
        }

        if (!isset(self::$dictionary_line_cached[$module][$name][$old_id])) {
            throw new RestException(404, "Dictionary not found");
        }

        return self::$dictionary_line_cached[$module][$name][$old_id];
    }

    /**
     *  Get line object
     *
     * @param   DictionaryLine  $dictionary_line    DictionaryLine instance
     * @param   int             $line_id            Id of the line
     *
     * @return  void
     *
     * @throws  404             RestException       Line not found
     * @throws  500             RestException       Error when retrieve the line
     */
	protected function _getLineObject(&$dictionary_line, $line_id)
    {
        $result = $dictionary_line->fetch($line_id);
        if ($result == 0) {
            throw new RestException(404, "Line not found");
        } elseif ($result < 0) {
            throw new RestException(500, "Error when retrieve the line", [ 'details' => $this->_getErrors($dictionary_line) ]);
        }
    }

    /**
     *  Check fields if exist into the dictionary
     *
     * @param   DictionaryLine  $dictionary_line    DictionaryLine instance
     * @param   array           $data               Array with data to check
     *
     * @return  array                               Array checked
     */
	protected function _checkFields(&$dictionary_line, $data)
    {
        $res = array();
        $dictionary_fields = $dictionary_line->dictionary->fields;
        foreach ($data as $field_name => $value) {
            if (isset($dictionary_fields[$field_name])) {
                $res[$field_name] = $value;
            }
        }

        return $res;
    }

    /**
     *  Clean sensible line object data
     *
     * @param   DictionaryLine      $line       Line to clean
     *
     * @return  array                           Array of cleaned object properties
     */
	protected function _cleanLineObjectData($line)
    {
        $data = array();
        $data[$line->dictionary->rowid_field] = $line->id;
        foreach ($line->dictionary->fields as $field) {
            if (DolibarrApiAccess::$user->societe_id>0) {
                // show only rowid and label field for external user and active line
                if ($field['name']=='label') {
                    $data[$field['name']] = $line->fields[$field['name']];
                }
				
            } else {
                // show all fields for internal users
                $data[$field['name']] = $line->fields[$field['name']];
            }
        }

        // show associated data of each line
            $data[$line->dictionary->active_field] = $line->active;
            if ($line->dictionary->has_entity) $data[$line->dictionary->entity_field] = $line->entity;

        return $data;
    }

    /**
     *  Clean sensible dictionary object data
     *
     * @param   Dictionary      $dictionary     Dictionary object to clean
     *
     * @return  array                           Array of cleaned dictionary object properties
     */
	protected function _cleanDictionaryObjectData($dictionary)
    {
        global $langs;
        $langs->loadLangs($dictionary->langs);

        return [
            'version' => $dictionary->version,
            'titlePicto' => (!empty($dictionary->titlePicto) ? img_picto('', $dictionary->titlePicto, '', '', 1) : ''),
            'family' => $dictionary->family,
            'familyLabel' => $langs->trans($dictionary->familyLabel),
            'familyPosition' => $dictionary->familyPosition,
            'hidden' => $dictionary->hidden,
            'module' => $dictionary->module,
            'moduleLabel' => $langs->trans($dictionary->moduleLabel),
            'modulePicto' => (!empty($dictionary->modulePicto) ? img_picto('', $dictionary->modulePicto, '', '', 1) : ''),
            'name' => $dictionary->name,
            'nameLabel' => $langs->trans($dictionary->nameLabel),
            'fields' => array_keys($dictionary->fields),
            'rowid_field' => $dictionary->rowid_field,
            'active_field' => $dictionary->active_field,
            'entity_field' => $dictionary->entity_field,
            'has_entity' => $dictionary->has_entity,
            'enabled' => $dictionary->enabled,
        ];
    }

    /**
     * Get all errors
     *
     * @param  object   $object     Object
     * @return array                Array of errors
     */
	protected function _getErrors(&$object) {
	    $errors = is_array($object->errors) ? $object->errors : array();
	    $errors = array_merge($errors, (!empty($object->error) ? array($object->error) : array()));

	    return $errors;
    }
}
