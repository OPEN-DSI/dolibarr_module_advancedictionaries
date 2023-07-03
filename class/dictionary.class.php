<?php
/* Copyright (C) 2018  Open-Dsi <support@open-dsi.fr>
 * Copyright (C) 2021  Alexis LAURIER <contact@alexislaurier.fr>
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
 * \file        class/dictionary.class.php
 * \ingroup     Dictionary
 * \brief       Base class for dictionaries
 */

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobjectline.class.php';

/**
 * Class for Dictionary
 */
class Dictionary extends CommonObject
{
    /**
     * @var int         Version of this dictionary
     */
    public $version = 0;

    /**
     * @var array       List of languages to load
     */
    public $langs = array();

    /**
     * @var string      Title picto when this dictionary is selected
     */
    public $titlePicto = '';

    /**
     * @var string      Family name of which this dictionary belongs
     */
    public $family = '';

    /**
     * @var string      Family label for show in the list, translated if key found
     */
    public $familyLabel = '';

    /**
     * @var int         Position of the dictionary into the family
     */
    public $familyPosition = 1;

    /**
     * @var bool        Hide this dictionary in the list
     */
    public $hidden = false;

    /**
     * @var string      Module name of which this dictionary belongs
     */
    public $module = '';

    /**
     * @var string      Module name for show in the list, translated if key found
     */
    public $moduleLabel = '';

    /**
     * @var string      Module picto for show in the list
     */
    public $modulePicto = '';

    /**
     * @var string      Name of this dictionary (set in the constructor)
     */
    public $name = '';

    /**
     * @var string      Name of this dictionary for show in the list, translated if key found
     */
    public $nameLabel = '';

	/**
	 * @var string      Custom root path of this dictionary (set in getDictionary())
	 */
	public $root_path = '';

    /**
     * @var bool        Hide the title block in the values list screen
     */
    public $hideTitleBlock = false;

    /**
     * @var string      Custom name for the title in the values list screen
     */
    public $customTitle = '';

    /**
     * @var string      Custom back link in the values list screen
     */
    public $customBackLink = '';

    /**
     * @var bool        Hide the custom back link in the values list screen
     */
    public $hideCustomBackLink = false;

    /**
     * @var string      List title
     */
    public $listTitle = '';

    /**
     * @var string      Name of the dictionary table without prefix (ex: c_country)
     */
    public $table_name = '';

    /**
     * @var array  Fields of the dictionary table
     * 'name' => array(
     *   'name'                   => string,         			// Name of the field
     *   'label'                  => string,         			// Label of the field, translated if key found
     *   'label_in_add_edit'      => string,         			// Label of the field in add/edit box if defined, translated if key found
     *   'type'                   => string,         			// Type of the field (varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url,
     *                                               			                      password, select, sellist, radio, checkbox, chkbxlst, chkbxlstwithorder, link, custom)
     *   'database'               => array(          			// Description of the field in the database always rewrite default value if set
     *     'type'                 => string,         			// Data type
     *     'length'               => string,         			// Length of the data type (require)
     *     'default'              => string,         			// Default value in the database
     *   ),
     *   'is_require'             => bool,           			// Set at true if this field is required
     *   'is_fixed_value'         => bool,           			// Set at true if this field is a set automatically
	 *   'update_list_values'     => array(name_field, ...), 	// Update list of values of the specified name_field by AJAX when this field is modified (update field type: select, checkbox, sellist, chkbxlst)
     *   'options'                => array()|string, 			// Parameters same as extrafields (ex: 'table:label:rowid::active=1' or array(1=>'value1', 2=>'value2') )
     *                                               			   string: sellist, chkbxlst, chkbxlstwithorder, link | array: select, radio, checkbox
     *                                               			   The key of the value must be not contains the character ',' and for chkbxlst and chkbxlstwithorder it's a rowid
	 * 															   Parameter: 0:1:2:3:4:5:6:7
	 *																	0 : tableName ({{DB_PREFIX}} can be used on case: table AS t1 LEFT JOIN {{DB_PREFIX}}table2 AS t2 ON t2.rowid = t1.fk_table2)
	 *																	1 : label field name (can use | for multiple label field, can use 'AS', example: t1.label AS t1_label|t2.label AS t2_label)
	 *																	2 : key fields name (if differ of rowid)
	 *																	3 : key field parent (for dependent lists)
	 *																	4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
	 *																	5 : ObjectName
	 *																	6 : classPath
	 *																	7 : lang (can use | for multiple field with lang file to load, can use 'AS', example: t1.lang AS t1_lang|t2.lang AS t2_lang)
	 *   'empty_options'          => array,        				// List of value who is considered as empty (for select, sellist, radio, checkbox, chkbxlst, chkbxlstwithorder)
	 *   'association_table'      => array (                    // Options on fields with an association table (type "chkbxlst" and "chkbxlstwithorder")
	 *      'name'                => string                     // Custom association table name given here
	 *      'fk_line_name'        => string                     // Custom column name for line's id of this dictionary
	 *      'fk_target_name'      => string                     // Custom column name for target's id of this field
	 *   ),
     *   'truncate'        	  	  => integer,        			// Truncate string in "sellist", "chkbxlst", "chkbxlstwithorder" type
	 *   'no_wysiwyg'        	  => bool,         	 			// Disabled the WYSIWYG for the "text" type (Default = false)
     *   'label_separator'        => string,         			// Separator when use | in the label into the options value
     *   'unselected_values'      => array,          			// List of values for unselected values in select and sellist type (=array(-1) if not defined)
     *   'translate_prefix'       => string,         			// Prefix for translation of the value
     *   'translate_suffix'       => string,         			// Suffix for translation of the value
     *   'show_column_by_default' => bool,           			// Show the column by default when show the list (Default = true)
     *   'position_column'        => integer,        			// Position of the column in the list show
     *   'enabled_column'         => bool,           			// Enable the column in the list show (Not )
     *   'td_title'               => array (
     *      'moreAttributes'      => string,         			// Add more attributes in the title balise td
     *      'align'               => string,         			// Overwrirte the align by default
     *   ),
     *   'td_output'              => array (
     *      'moreClasses'         => string,         			// Add more classes in the output balise td
     *      'moreAttributes'      => string,         			// Add more attributes in the output balise td
     *      'align'               => string,         			// Overwrirte the align by default
     *   ),
     *   'show_output'            => array (
     *      'moreAttributes'      => string,         			// Add more attributes in when show output field
     *   ),
     *   'is_not_show'            => bool,           			// Set at true if this field is not show must be set at true if you want to search or edit (don't used if is edited in add form)
     *   'is_not_searchable'      => bool,           			// Set at true if this field is not searchable
     *   'td_search'              => array (
     *      'moreClasses'         => string,         			// Add more classes in the search input balise td
     *      'moreAttributes'      => string,         			// Add more attributes in the search input balise td
     *      'align'               => string,         			// Overwrirte the align by default
     *   ),
     *   'show_search_input'      => array (
     *      'size'                => int,            			// Size attribute of the search input field (input text)
     *      'moreClasses'         => string,         			// Add more classes in the search input field
     *      'moreAttributes'      => string,         			// Add more attributes in the search input field
     *   ),
     *   'is_not_addable'         => bool,           			// Set at true if this field is not addable
     *   'is_not_editable'        => bool,           			// Set at true if this field is not editable
     *   'is_not_show_in_add'     => bool,           			// Set at true if this field is not addable
     *   'is_not_show_in_edit'    => bool,           			// Set at true if this field is not editable
     *   'td_input'               => array (
     *      'moreClasses'         => string,         			// Add more classes in the input balise td
     *      'moreAttributes'      => string,         			// Add more attributes in the input balise td
     *      'align'               => string,         			// Overwrirte the align by default
     *      'positionLine'        => int,            			// Move the input into new line in function of the position (only in add form, 0 by default)
     *      'colspan'             => int,            			// TD colspan
     *   ),
     *   'show_input'             => array (
     *      'moreClasses'         => string,         			// Add more classes in the input field
     *      'moreAttributes'      => string,         			// Add more attributes in the input field
     *   ),
     *   'add_params_in_add_edit' => array,          			// List of additional param name in the formconfirm who is send other than who is showed
     *   'help'                   => '',             			// Help text for this field or url, translated if key found
     *   'help_button'            => '',             			// Help text for this field access by a click
     *   'is_not_sortable'        => bool,           			// Set at true if this field is not sortable
     *   'min'                    => int,            			// Value minimum (include) if type is int, float, double or price
     *   'max'                    => int,            			// Value maximum (include) if type is int, float, double or price
     * )
     */
    public $fields = array();

    /**
     * @var array  List of index for the database
     * idx_number => array(
     *   'fields'    => array( ... ), // List of field name who constitute this index
     *   'is_unique' => '',           // Set at 1 if this index is unique
     * )
     */
    public $indexes = array();

    /**
     * @var array  List of fields/indexes added, updated or deleted for a version
     * array(
     *   'version' => array(
     *     'fields' => array('field_name'=>'u', ...), // List of field name who is updated(u) for a version
     *     'deleted_fields' => array('field_name'=> array('name', 'type', other_custom_data_required_for_delete), ...), // List of field name who is deleted for a version
	 *     'indexes' => array('idx_number'=>'u', 'idx_number'=>'d', ...), // List of indexes number who is updated(u) or deleted(d) for a version
	 *     'primary_key' => 'a' or 'u' or 'd', // The primary key is added(a) or updated(u) or deleted(d) for a version
     *   ),
     * )
     */
    public $updates = array();

    /**
     * @var string  Name of the rowid field
     */
    public $rowid_field = 'rowid';

    /**
     * @var bool    Is rowid auto increment (false : rowid = defined by the option $is_rowid_defined_by_code)
     */
    public $is_rowid_auto_increment = true;

    /**
     * @var bool    Is rowid defined by code (true: rowid = $this->id of the DictionaryLine; false: rowid = 'last rowid in the table' + 1)
     */
    public $is_rowid_defined_by_code = false;

    /**
     * @var string  Name of the active field
     */
    public $active_field = 'active';

    /**
     * @var string  Name of the entity field
     */
    public $entity_field = 'entity';

	/**
	 * @var array  List of fields composing the primary key
	 */
	public $primary_key = array('rowid');

    /**
     * @var bool    Use entity field
     */
    public $has_entity = true;

    /**
     * @var bool    Is multi entity (false = shared, true = by entity)
     */
    public $is_multi_entity = false;

	/**
	 * @var bool    Show the management of the entity of the dictionary lines (show column entity and the mass action for change the entity of the lines) (false = show, true = hide)
	 */
	public $show_entity_management = true;

    /**
     * @var string  Name of the DictionaryLine class
     */
    public $dictionaryLineClassName = 'DictionaryLine';

    /**
     * @var bool    Determine if this dictionary is enabled or not (must be defined in the function initialize())
     */
    public $enabled = true;

    /**
     * @var bool    Determine if lines can be added or not (must be defined in the function initialize())
     */
    public $lineCanBeAdded = true;

    /**
     * @var bool    Determine if lines can be updated or not (must be defined in the function initialize())
     */
    public $lineCanBeUpdated = true;

    /**
     * @var bool    Determine if lines can be deleted or not (must be defined in the function initialize())
     */
    public $lineCanBeDeleted = true;

    /**
     * @var bool    Determine if the rowid must be show in the list
     */
    public $showTechnicalID = false;

    /**
     * @var bool    Edit in the add form
     */
    public $edit_in_add_form = false;

    /**
     * @var string    Default sort for the list of lines
     */
    public $listSort = 'rowid ASC';

    /**
   	 * @var DictionaryLine[]
   	 */
   	public $lines = array();

    /**
   	 * Constructor
   	 *
   	 * @param DoliDb $db Database handler
   	 */
   	public function __construct(DoliDB $db)
    {
        global $conf;
        $this->db = $db;
        $dictionaryLineClassName = get_class($this) . 'Line';
        if (class_exists($dictionaryLineClassName, false)) {
            $this->dictionaryLineClassName = $dictionaryLineClassName;
        }
        $this->name = strtolower(substr(get_class($this), 0, -10));
        $module = $this->module;
        $this->enabled = !empty($conf->$module->enabled);

        $this->initialize();
        // ICI
        // force_field -> initialiser à des valeurs par défaut
    }

    /**
   	 * Initialize the dictionary
   	 *
     * @return  void
   	 */
   	protected function initialize()
   	{
   	}

    /**
     * Overwrite default actions of the dictionary template page (After the hook "doActions")
     *
     * @return  int                 <0 if KO, =0 if do default actions, >0 if don't do default actions
     */
    public function doActions()
    {
        return 0;
    }

    /**
   	 * Definition table field instruction
   	 *
     * @param   array   $field      Description of the field
   	 * @return  string              Definition table field instruction
   	 */
   	protected function definitionTableFieldInstructionSQL($field)
    {
        if (!empty($field)) {
            $lengthdb = '';

            switch ($field['type']) {
                case 'varchar':
                    $typedb='varchar';
                    $lengthdb='255';
                    break;
                case 'text':
                    $typedb='text';
                    break;
                case 'int':
                    $typedb='integer';
                    break;
                case 'float':
                    $typedb='float';
                    break;
                case 'double':
                    $typedb='double';
                    $lengthdb='24,8';
                    break;
                case 'date':
                    $typedb='date';
                    break;
                case 'datetime':
                    $typedb='datetime';
                    break;
                case 'boolean':
                    $typedb='boolean';
                    break;
                case 'price':
                    $typedb='double';
                    $lengthdb='24,8';
                    break;
                case 'phone':
                    $typedb='varchar';
                    $lengthdb='20';
                    break;
                case 'mail':
                    $typedb='varchar';
                    $lengthdb='128';
                    break;
                case 'url':
                    $typedb='varchar';
                    $lengthdb='255';
                    break;
                case 'password':
                    $typedb='varchar';
                    $lengthdb='50';
                    break;
                case 'select':
                case 'sellist':
                case 'radio':
                    $typedb='varchar';
                    $lengthdb='255';
                    break;
                case 'checkbox':
                    $typedb='text';
                    break;
                case 'link':
                    $typedb='integer';
                    break;
                case 'custom':
                    return $this->definitionTableCustomFieldInstructionSQL($field);
                default: // chkbxlst, chkbxlstwithorder, unknown
                    return '';
            }

			$cq = $this->db->type == 'pgsql' ? '"' : '`';

			$typedb = isset($field['database']['type']) ? $field['database']['type'] : $typedb;
            $lengthdb = isset($field['database']['length']) ? $field['database']['length'] : $lengthdb;
            $nulldb = !empty($field['is_require']) ? ' NOT NULL' : ' NULL';
            $defaultdb = isset($field['database']['default']) ? " DEFAULT '" . $this->db->escape($field['database']['default']) . "'" : '';

            return $cq . $field['name'] . $cq . ' ' . $typedb . (!empty($lengthdb) ? '('.$lengthdb.')' : '') . $nulldb . $defaultdb;
        }

        return '';
    }

    /**
   	 * Definition table field instruction
   	 *
     * @param   array   $field      Description of the field
   	 * @return  string              Definition table field instruction
   	 */
    protected function definitionTableCustomFieldInstructionSQL($field) {
        return '';
    }

    /**
   	 * Create dictionary table
   	 *
   	 * @return int             <0 if not ok, >0 if ok
   	 */
	public function createTables()
    {
        if (!empty($this->table_name) && !empty($this->fields)) {
            $error = 0;
            $this->db->begin();

			$new_created = true;
			$cq = $this->db->type == 'pgsql' ? '"' : '`';

            // Create dictionary table
            $sql = 'CREATE TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' (' . $cq . $this->rowid_field . $cq . ' INTEGER NOT NULL';
            foreach ($this->fields as $field) {
                $instructionSQL = $this->definitionTableFieldInstructionSQL($field);
                $sql .= !empty($instructionSQL) ? ', ' . $instructionSQL : '';
            }
            $sql .= ', ' . $cq . $this->active_field . $cq . ' INTEGER DEFAULT 1 NOT NULL';
            if ($this->has_entity) $sql .= ', ' . $cq . $this->entity_field . $cq . ' INTEGER DEFAULT 1 NOT NULL';
            $sql .= ')' . ($this->db->type == 'pgsql' ? '' : ' ENGINE=innodb');

            $resql = $this->db->query($sql);
            if (!$resql) {
                if ($this->db->lasterrno() != 'DB_ERROR_TABLE_ALREADY_EXISTS' && $this->db->lasterrno() != 'DB_ERROR_TABLE_OR_KEY_ALREADY_EXISTS') {
                    $error++;
                    $this->error = $this->db->lasterror();
                } else {
					$new_created = false;
				}
            }

            if (!$error && $new_created) {
				// Add primary key
				$res = $this->createPrimaryKeyTable();
				if ($res < 0) {
					$error++;
				}
            }

			if (!$error) {
				// Create indexes of the tables
				$res = $this->createIndexesTable();
				if ($res < 0) {
					$error++;
				}
			}

            if (!$error) {
                // Create sub dictionary table
                foreach ($this->fields as $field) {
                    $res = $this->createSubTable($field);
                    if ($res < 0) {
                        $error++;
                        break;
                    }
                }
            }

            if (!$error) {
                // Add foreign key for sub dictionary table
                foreach ($this->fields as $field) {
                    $res = $this->addSubTableForeignKey($field);
                    if ($res < 0) {
                        $error++;
                        break;
                    }
                }
            }

            if (!$error) {
                // Update dictionary table
                $res = $this->updateTables();
                if ($res < 0) {
                    $error++;
                }
            }

            if (!$error) {
                $this->db->commit();
                return 1;
            } else {
            	$this->errors[] = 'Error table: ' . $this->table_name;
                $this->db->rollback();
                return -1;
            }
        }

        $this->error = 'Empty table name or fields list';
        return -2;
    }

    /**
   	 * Create indexes of the table
   	 *
     * @return  int                 <0 if not ok, >0 if ok
   	 */
   	protected function createIndexesTable()
    {
        // Create indexes of the table
        foreach ($this->indexes as $idx => $index) {
            if ($this->createIndexTable($idx) < 0)
                return -1;
        }

        return 1;
    }

	/**
	 * Create index of the table
	 *
	 * @param   int   $idx_number       Number of the index
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function createIndexTable($idx_number)
	{
		global $langs;

		if (!isset($this->indexes[$idx_number])) {
			$this->error = $langs->trans('AdvanceDictionariesErrorIndexNotDefined', $idx_number);
			return -1;
		}

		$cq = $this->db->type == 'pgsql' ? '"' : '`';

		$index = $this->indexes[$idx_number];

		if ($this->db->type == 'pgsql')
			$sql = 'CREATE ' . (!empty($index['is_unique']) ? 'UNIQUE ' : '') . 'INDEX idx_' . $this->table_name . '_' . $idx_number . ' ON ' . MAIN_DB_PREFIX . $this->table_name . ' (';
		else
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' ADD ' . (!empty($index['is_unique']) ? 'UNIQUE ' : '') . 'INDEX idx_' . $this->table_name . '_' . $idx_number . ' (';
		foreach ($index['fields'] as $field) {
			$sql .= $cq . $field . $cq . ', ';
		}
		if ($this->has_entity && $this->is_multi_entity)
			$sql .= $cq. $this->entity_field . $cq . ')';
		else
			$sql = substr($sql, 0, -2) . ')';

		$resql = $this->db->query($sql);
		if (!$resql) {
			if ($this->db->lasterrno() != 'DB_ERROR_KEY_NAME_ALREADY_EXISTS' && $this->db->lasterrno() != 'DB_ERROR_TABLE_OR_KEY_ALREADY_EXISTS') {
				$this->error = $this->db->lasterror();
				return -1;
			}
		}

		return 1;
	}

	/**
	 * Delete index of the table
	 *
	 * @param   int   $idx_number       Number of the index
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function deleteIndexTable($idx_number)
	{
		if ($this->db->type == 'pgsql')
			$sql = 'DROP INDEX IF EXISTS idx_' . $this->table_name . '_' . $idx_number;
		else
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP INDEX idx_' . $this->table_name . '_' . $idx_number;

		$resql = $this->db->query($sql);
		if (!$resql) {
			if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHFIELD') {
				$this->error = $this->db->lasterror();
				return -1;
			}
		}

		return 1;
	}

	/**
	 * Create primary key of the table
	 *
	 * @return  int            <0 if not ok, >0 if ok
	 */
	protected function createPrimaryKeyTable()
	{
		$this->primary_key = empty($this->primary_key) || !is_array($this->primary_key) ? array($this->rowid_field) : $this->primary_key;
		$cq = $this->db->type == 'pgsql' ? '"' : '`';

		$error = 0;
		$this->db->begin();

		$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' ADD CONSTRAINT pk_' . $this->table_name . ' PRIMARY KEY (' . $cq . implode($cq . ',' . $cq, $this->primary_key). $cq . ')';

		$resql = $this->db->query($sql);
		if (!$resql) {
			if ($this->db->lasterrno() != 'DB_ERROR_KEY_NAME_ALREADY_EXISTS' && $this->db->lasterrno() != 'DB_ERROR_TABLE_OR_KEY_ALREADY_EXISTS') {
				$this->error = $this->db->lasterror();
				$error++;
			}
		} elseif ($this->is_rowid_auto_increment) {
			if ($this->db->type != 'pgsql') {
				$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' MODIFY ' . $cq . $this->rowid_field . $cq . ' INTEGER NOT NULL AUTO_INCREMENT';
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->error = $this->db->lasterror();
					$error++;
				}
			} else {
				$sql = 'CREATE SEQUENCE ' . MAIN_DB_PREFIX . $this->table_name .'_rowid_seq OWNED BY '.MAIN_DB_PREFIX . $this->table_name.'.'.$this->rowid_field;
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->error = $this->db->lasterror();
					$error++;
				}
				$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name .' ALTER COLUMN '.$cq.$this->rowid_field.$cq.' SET DEFAULT nextval(\''.MAIN_DB_PREFIX . $this->table_name .'_rowid_seq\')';
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->error = $this->db->lasterror();
					$error++;
				}
				$sql = 'SELECT setval(\'' . MAIN_DB_PREFIX . $this->table_name .'_rowid_seq\', MAX('.$cq.$this->rowid_field.$cq.')) FROM '. MAIN_DB_PREFIX . $this->table_name;
				$resql = $this->db->query($sql);
				if (!$resql) {
					$this->error = $this->db->lasterror();
					$error++;
				}
			}
			$resql = $this->db->query($sql);
			if (!$resql) {
				$this->error = $this->db->lasterror();
				$error++;
			}
		}

		if ($error) {
			$this->db->rollback();
			return -1;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	/**
	 * Update primary key of the table
	 *
	 * @return  int            <0 if not ok, >0 if ok
	 */
	protected function updatePrimaryKeyTable()
	{
		$this->primary_key = empty($this->primary_key) || !is_array($this->primary_key) ? array($this->rowid_field) : $this->primary_key;
		$cq = $this->db->type == 'pgsql' ? '"' : '`';

		if ($this->db->type == 'pgsql') {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP ' . $this->table_name . '_pkey, ADD CONSTRAINT pk_' . $this->table_name . ' PRIMARY KEY (' . $cq . implode($cq . ',' . $cq, $this->primary_key). $cq . ')';
			$resql = $this->db->query($sql);
			if (!$resql) {
				$this->error = $this->db->lasterror();
				return -1;
			}

			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP pk_' . $this->table_name . ', ADD CONSTRAINT pk_' . $this->table_name . ' PRIMARY KEY (' . $cq . implode($cq . ',' . $cq, $this->primary_key). $cq . ')';
		} elseif ($this->db->type == 'mysqli') {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP PRIMARY KEY, ADD PRIMARY KEY (' . $cq . implode($cq . ',' . $cq, $this->primary_key). $cq . ')';
		} else {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP pk_' . $this->table_name . ', ADD CONSTRAINT pk_' . $this->table_name . ' PRIMARY KEY (' . $cq . implode($cq . ',' . $cq, $this->primary_key). $cq . ')';
		}

		$error = 0;
		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$this->error = $this->db->lasterror();
			$error++;
		}

		if ($error) {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' MODIFY ' . $cq . $this->rowid_field . $cq . ' INTEGER NOT NULL' . ($this->is_rowid_auto_increment ? ' AUTO_INCREMENT' : '');
			$resql = $this->db->query($sql);
			if (!$resql) {
				$this->error = $this->db->lasterror();
				$error++;
			}
		}

		if ($error) {
			$this->db->rollback();
			return -1;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	/**
	 * Delete primary key of the table
	 *
	 * @return  int            <0 if not ok, >0 if ok
	 */
	protected function deletePrimaryKeyTable()
	{
		if ($this->db->type == 'pgsql') {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP CONSTRAINT ' . $this->table_name . '_pkey';
			$resql = $this->db->query($sql);
			if (!$resql) {
				$this->error = $this->db->lasterror();
				return -1;
			}

			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP CONSTRAINT pk_' . $this->table_name;
		} elseif ($this->db->type == 'mysqli') {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP PRIMARY KEY';
		} else {
			$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP CONSTRAINT pk_' . $this->table_name;
		}

		$resql = $this->db->query($sql);
		if (!$resql) {
			if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHFIELD') {
				$this->error = $this->db->lasterror();
				return -1;
			}
		}

		return 1;
	}

    /**
   	 * Create sub table for the field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
   	protected function createSubTable($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
					$cq = $this->db->type == 'pgsql' ? '"' : '`';
					// Create association table for the multi-select list
					$sql = 'CREATE TABLE ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) .
						'(rowid INTEGER AUTO_INCREMENT PRIMARY KEY, ' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ' INTEGER NOT NULL, ' . $cq . $this->getDestinationColumnAssociationTableName($field) . $cq . ' INTEGER NOT NULL)  ENGINE=innodb;';

                    $resql = $this->db->query($sql);
                    if (!$resql) {
                        if ($this->db->lasterrno() != 'DB_ERROR_TABLE_ALREADY_EXISTS' && $this->db->lasterrno() != 'DB_ERROR_TABLE_OR_KEY_ALREADY_EXISTS') {
                            $this->error = $this->db->lasterror();
                            return -1;
                        }
                    }

					// Add rowid field if not exist
					$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' ADD COLUMN rowid INTEGER AUTO_INCREMENT PRIMARY KEY FIRST;';
					$resql = $this->db->query($sql);
					if (!$resql) {
						if ($this->db->lasterrno() != 'DB_ERROR_COLUMN_ALREADY_EXISTS') {
							$this->error = $this->db->lasterror();
							return -1;
						}
					}

					break;
                case 'custom':
                    $res = $this->createCustomSubTable($field);
                    if ($res < 0) {
                        return -1;
                    }

                    break;
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    break;
            }

            return 1;
        }

        return 0;
    }

    /**
   	 * Create sub table for custom field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
    protected function createCustomSubTable($field) {
        return 1;
    }

    /**
   	 * Add foreign key of sub table for the field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
   	protected function addSubTableForeignKey($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                	$table_name = $this->getAssociationTableName($field);
                    // Get table initial
                    $initial = '';
                    if (preg_match_all('/(?:^|_)(.)/', $table_name, $matches)) {
                        foreach ($matches[1] as $k => $v) {
                            $initial .= $v;
                        }
                    }

					$cq = $this->db->type == 'pgsql' ? '"' : '`';

                    // Add foreign constraint with association table for the multi-select list
//                    $sql = 'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()' .
//                        " AND CONSTRAINT_NAME   = 'fk_" . $initial . '_cbl_' . $field['name'] . "_a'" .
//                        " AND CONSTRAINT_TYPE   = 'FOREIGN KEY'";
//                    $resql = $this->db->query($sql);
//                    if (!$resql) {
//                        $this->error = 'Check foreign key "fk_' . $initial . '_cbl_' . $field['name'] . '_a" : ' . $this->db->lasterror();
//                        return -1;
//                    } else {
//                        if (!$this->db->num_rows($resql)) {
                            $sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $table_name .
                                ' ADD CONSTRAINT fk_' . $initial . '_cbl_' . $field['name'] . '_a FOREIGN KEY (' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq .') REFERENCES ' . MAIN_DB_PREFIX . $this->table_name . ' (' . $cq . $this->rowid_field . $cq . ');';

                            $resql = $this->db->query($sql);
                            if (!$resql) {
//                                $this->error = 'Add foreign key "fk_' . $initial . '_cbl_' . $field['name'] . '_a" : ' . $sql . $this->db->lasterror();
//                                return -1;
                            }
//                        }
//                    }
//
//                    // Add foreign constraint with association table for the multi-select list
//                    $sql = 'SELECT NULL FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()' .
//                        " AND CONSTRAINT_NAME   = 'fk_" . $initial . '_cbl_' . $field['name'] . "_b'" .
//                        " AND CONSTRAINT_TYPE   = 'FOREIGN KEY'";
//                    $resql = $this->db->query($sql);
//                    if (!$resql) {
//                        $this->error = 'Check foreign key "fk_' . $initial . '_cbl_' . $field['name'] . '_b" : ' . $this->db->lasterror();
//                        return -1;
//                    } else {
//                        if (!$this->db->num_rows($resql)) {

                            // 0 : tableName
                            // 1 : label field name
                            // 2 : key fields name (if differ of rowid)
                            // 3 : key field parent (for dependent lists)
                            // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                            $InfoFieldList = explode(":", (string)$field['options']);

                            $keyList = 'rowid';
                            if (count($InfoFieldList) >= 3) {
                                $keyList = $InfoFieldList[2];
                            }

                            $sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $table_name .
                                ' ADD CONSTRAINT fk_' . $initial . '_cbl_' . $field['name'] . '_b FOREIGN KEY (' . $cq .$this->getDestinationColumnAssociationTableName($field) . $cq .') REFERENCES ' . MAIN_DB_PREFIX . $InfoFieldList[0] . ' (' . $cq . $keyList . $cq . ');';

                            $resql = $this->db->query($sql);
                            if (!$resql) {
//                                $this->error = 'Add foreign key "fk_' . $initial . '_cbl_' . $field['name'] . '_b" : ' . $this->db->lasterror();
//                                return -1;
                            }
//                        }
//                    }

                    break;
                case 'custom':
                    $res = $this->addCustomSubTableForeignKey($field);
                    if ($res < 0) {
                        return -1;
                    }

                    break;
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    break;
            }

            return 1;
        }

        return 0;
    }

    /**
   	 * Add foreign key of sub table for custom field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
    protected function addCustomSubTableForeignKey($field) {
        return 1;
    }

    /**
   	 * Update dictionary table
   	 *
   	 * @return int             <0 if not ok, >0 if ok
   	 */
    protected function updateTables()
    {
        global $conf, $langs;

        $version_variable_name = strtoupper('ADVANCEDICTIONARIES_DICTIONARY_'.$this->name.'_VERSION');
        $current_version = isset($conf->global->$version_variable_name) ? $conf->global->$version_variable_name : 0;

        // TODO prevoir les mise a jour avec les sous tables

        foreach ($this->updates as $version => $datas) {
			if ($version > $current_version) {
				// Fields
				if (is_array($datas['fields'])) {
					foreach ($datas['fields'] as $field_name => $type) {
						switch ($type) {
							case 'a':
								if (isset($this->fields[$field_name])) {
									// Insert column of dictionary table
									$instructionSQL = $this->definitionTableFieldInstructionSQL($this->fields[$field_name]);
									if (!empty($instructionSQL)) {
										$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' ADD COLUMN ' . $instructionSQL;
										$resql = $this->db->query($sql);
										if (!$resql) {
											if ($this->db->lasterrno() != 'DB_ERROR_COLUMN_ALREADY_EXISTS') {
												$this->error = $this->db->lasterror();
												return -1;
											}
										}
									} else {
										$result = $this->createSubTable($this->fields[$field_name]);
										if ($result < 0) {
											return -1;
										}
									}
								}
								break;
							case 'u':
								if (isset($this->fields[$field_name])) {
									// Update column of dictionary table
									$instructionSQL = $this->definitionTableFieldInstructionSQL($this->fields[$field_name]);
									if (!empty($instructionSQL)) {
										$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' MODIFY COLUMN ' . $instructionSQL;
										$resql = $this->db->query($sql);
										if (!$resql) {
											if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHFIELD') {
												$this->error = $this->db->lasterror();
												return -1;
											}
										}
									} else {
										$field = $this->fields[$field_name];
										if (in_array($field['type'], array('chkbxlst', 'chkbxlstwithorder'))) {
											$isPgSql = $this->db->type == 'pgsql';
											$cq = $isPgSql ? '"' : '`';

											$sql = 'INSERT' . (!$isPgSql ? ' IGNORE' : '') . ' INTO ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) .
												' (' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ', ' . $cq . $this->getDestinationColumnAssociationTableName($field) . $cq . ') ' .
												' SELECT ' . $this->rowid_field . ', ' . $field_name . ' FROM ' . MAIN_DB_PREFIX . $this->table_name .
												($isPgSql ? ' ON CONFLICT DO NOTHING' : '');
											$resql = $this->db->query($sql);
											if (!$resql) {
												$this->error = $this->db->lasterror();
												return -1;
											}

											$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP COLUMN ' . $cq . $field_name . $cq;
											$resql = $this->db->query($sql);
										}
									}
								}
								break;
						}
					}
				}
				// Delete fields
				if (isset($datas['delete_fields']) && is_array($datas['delete_fields'])) {
					foreach ($datas['delete_fields'] as $field_name => $field) {
						// Delete column of dictionary table
						$instructionSQL = $this->definitionTableFieldInstructionSQL($field);
						if (!empty($instructionSQL)) {
							$sql = 'ALTER TABLE ' . MAIN_DB_PREFIX . $this->table_name . ' DROP COLUMN ' . $field_name;
							$resql = $this->db->query($sql);
							if (!$resql) {
								if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHFIELD') {
									$this->error = $this->db->lasterror();
									return -1;
								}
							}
						} else {
							$result = $this->deleteSubTable($field);
							if ($result < 0) {
								return -1;
							}
						}
					}
				}

				// Indexes
				if (isset($datas['indexes']) && is_array($datas['indexes'])) {
					foreach ($datas['indexes'] as $idx_number => $type) {
						switch ($type) {
							//case 'a':
							//// Insert index of dictionary table
							//if ($this->createIndexTable($idx_number) < 0)
							//    return -1;
							//break;
							case 'u':
								// Update index of dictionary table
								if ($this->deleteIndexTable($idx_number) < 0)
									return -1;
								if ($this->createIndexTable($idx_number) < 0)
									return -1;
								break;
							case 'd':
								// Delete index of dictionary table
								if ($this->deleteIndexTable($idx_number) < 0)
									return -1;
								break;
						}
					}
				}

				// Primary key
				if (!empty($datas['primary_key'])) {
					switch ($datas['primary_key']) {
						case 'a':
							// Add primary key
							if ($this->createPrimaryKeyTable() < 0)
							    return -1;
							break;
						case 'u':
							// Update primary key of dictionary table
							if ($this->updatePrimaryKeyTable() < 0)
								return -1;
							break;
						case 'd':
							// Delete primary key of dictionary table
							if ($this->deletePrimaryKeyTable() < 0)
								return -1;
							break;
					}
				}
			}
		}

        dolibarr_set_const($this->db, $version_variable_name, $this->version, 'chaine', 0, '', 0);

        return 1;
    }

    /**
   	 * Delete dictionary table
   	 *
   	 * @return int             <0 if not ok, >0 if ok
   	 */
	public function deleteTables()
    {
        if (!empty($this->table_name) && !empty($this->fields)) {
            $error = 0;
            $this->db->begin();

            // Delete sub dictionary table
            foreach ($this->fields as $field) {
                $res = $this->deleteSubTable($field);
                if (!$res) {
                    $error++;
                    break;
                }
            }

            if (!$error) {
                // Delete dictionary table
                $sql = 'DELETE TABLE ' . MAIN_DB_PREFIX . $this->table_name;
                $resql = $this->db->query($sql);
                if (!$resql) {
					if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHTABLE') {
						$error++;
						$this->error = $this->db->lasterror();
					}
                }
            }

            if (!$error) {
                $this->db->commit();
                return 1;
            } else {
                $this->db->rollback();
                return -1;
            }
        }

        $this->error = 'Empty table name or fields list';
        return -2;
    }

    /**
   	 * Delete sub table for the field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
   	protected function deleteSubTable($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    // Delete association table for the multi-select list
                    $sql = 'DROP TABLE ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field);
                    $resql = $this->db->query($sql);
                    if (!$resql) {
						if ($this->db->lasterrno() != 'DB_ERROR_NOSUCHTABLE') {
							$this->error = $this->db->lasterror();
							return -1;
						}
                    }

                    break;
                case 'custom':
                    $res = $this->deleteCustomSubTable($field);
                    if ($res < 0) {
                        return -1;
                    }

                    break;
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    break;
            }

            return 1;
        }

        return 0;
    }

    /**
   	 * Create sub table for custom field
   	 *
     * @param   array   $field      Description of the field
   	 * @return  int                 <0 if not ok, >0 if ok
   	 */
    protected function deleteCustomSubTable($field) {
   	    return 1;
    }

    /**
   	 * Get all dictionaries
   	 *
     * @param   DoliDb          $db         Database handler
     * @param   string|array    $module     Only dictionary of the module(s) name
	 * @param   string|array    $family     Only dictionary of the family(s) name
	 * @param   string    		$root_path  Specific relative root path of the dictionnaries (direct path)
   	 * @return  Dictionary[]                List of dictionary
   	 */
   	static function fetchAllDictionaries($db, $module='', $family='', $root_path = '')
    {
        global $conf;

		$root_path = trim(trim($root_path), '/');
        $dictionaries = array();

        if (empty($root_path)) {
			if (empty($module)) {
				$dirmodels = array('/');
				if (is_array($conf->modules_parts['dictionaries'])) {
					foreach ($conf->modules_parts['dictionaries'] as $path) {
						$dirmodels[] = str_replace('/core/modules/dictionaries/', '/', $path);
					}
				}
			} else {
				$dirmodels = array();
				if (is_array($module)) {
					foreach ($module as $name) {
						$dirmodels[] = '/' . $name . '/';
					}
				} else {
					$dirmodels[] = '/' . $module . '/';
				}
			}
		} else {
			$dirmodels = array('/' . $root_path . '/');
		}

        foreach ($dirmodels as $reldir) {
            $dirroot = $reldir . (empty($root_path) ? "core/dictionaries/" : '');
            $dir = dol_buildpath($dirroot);

            if (is_dir($dir)) {
                $handle = @opendir($dir);
                if (is_resource($handle)) {
                    while (($file = readdir($handle)) !== false) {
                        if (preg_match('/\.dictionary\.php$/i', $file)) {
                            $classname = substr($file, 0, dol_strlen($file) - 15) . 'Dictionary';

                            try {
                                dol_include_once($dirroot . $file);
                            } catch (Exception $e) {
                                dol_syslog($e->getMessage(), LOG_ERR);
                            }

                            $dictionary = new $classname($db);

                            if (empty($family) || $family == $dictionary->family) {
                                $dictionaries[] = $dictionary;
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }

        usort($dictionaries, function ($a, $b) {
            $result = '';

            $result .= strcmp($a->family, $b->family);
            $result .= ($a->familyPosition - $b->familyPosition);
            $result .= strcmp($a->module, $b->module);
            $result .= strcmp($a->name, $b->name);

            return $result;
        });

        return $dictionaries;
    }

    /**
     * Get dictionary
     *
     * @param   DoliDb              $db         Database handler
     * @param   string              $module     Name of the module containing the dictionary
     * @param   string              $name       Name of dictionary
     * @param   int                 $old_id     Id of the dictionary for old dolibarr dictionary
	 * @param   string    			$root_path  Specific relative root path of the dictionnaries (direct path)
     * @return  Dictionary|null                 List of dictionary
     */
    static function getDictionary($db, $module='', $name='', $old_id=0, $root_path = '')
	{
		$dictionary = null;

		if (empty($module) && empty($name)) {
			Dictionary::getDictionaryModuleAndNameFromID($old_id, $module, $name);
		}

		$root_path = trim(trim($root_path), '/');
		$classname = $name . "Dictionary";
		$file = "/" . (!empty($root_path) ? $root_path : $module . "/core/dictionaries") . "/" . strtolower($name) . ".dictionary.php";

		if (!class_exists($classname, false)) {
			dol_include_once($file);
		}

		$dictionary = new $classname($db);
		if (is_object($dictionary)) $dictionary->root_path = $root_path;

		return $dictionary;
	}

    /**
     * Get dictionary line
     *
     * @param   DoliDb                  $db         Database handler
     * @param   string                  $module     Name of the module containing the dictionary
     * @param   string                  $name       Name of dictionary
     * @param   int                     $old_id     Id of the dictionary for old dolibarr dictionary
	 * @param   string    				$root_path  Specific relative root path of the dictionnaries (direct path)
     * @return  DictionaryLine|null                 List of dictionary
     */
    static function getDictionaryLine($db, $module='', $name='', $old_id=0, $root_path = '')
    {
        $dictionary = self::getDictionary($db, $module, $name, $old_id, $root_path);
        if (!isset($dictionary))
            return null;

        return $dictionary->getNewDictionaryLine();
    }

    /**
     * Get dictionary module and name from old dolibarr dictionary ID
     *
     * @param   string              $module     Name of the module containing the dictionary
     * @param   string              $name       Name of dictionary
     * @param   int                 $old_id     Id of the dictionary for old dolibarr dictionary
     * @return  void
     */
    static function getDictionaryModuleAndNameFromID($old_id, &$module, &$name)
    {
        $module = '';
        $name = '';

        if ($old_id > 0) {
            switch ($old_id) {
                case 1:
                    $module = '';
                    $name = 'formejuridique';
                    break;
                // ....
            }
        }
    }

    /**
     * Get new dictionary line instance
     *
     * @return  DictionaryLine          Dictionary line instance
     */
	public function getNewDictionaryLine()
    {
        $dictionaryLine = new $this->dictionaryLineClassName($this->db, $this);

        return $dictionaryLine;
    }

    /**
   	 * Add line
   	 *
     * @param   array   $fieldsValues   Values of the fields array(name => value, ...)
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
   	 * @return  int                     <0 if not ok, >0 if ok
   	 */
	public function addLine($fieldsValues, $user, $noTrigger=0)
    {
        $this->db->begin();
        $error = 0;

        $dictionaryLine = new $this->dictionaryLineClassName($this->db, $this);

        $res = $dictionaryLine->insert($fieldsValues, $user, $noTrigger);
        if ($res < 0) {
            $error++;
            $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
   	 * Update line
   	 *
     * @param   int     $lineId         Id of the line
     * @param   array   $fieldsValues   Values of the fields array(name => value, ...)
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
   	 * @return  int                     <0 if not ok, >0 if ok
   	 */
	public function updateLine($lineId, $fieldsValues, $user, $noTrigger=0)
    {
        $this->db->begin();
        $error = 0;

        $dictionaryLine = new $this->dictionaryLineClassName($this->db, $this);

        $res = $dictionaryLine->fetch($lineId);
        if ($res > 0) {
            $res = $dictionaryLine->update($fieldsValues, $user, $noTrigger);
            if ($res < 0) {
                $error++;
                $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
            }
        } elseif ($res < 0) {
            $error++;
            $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
   	 * Delete line
   	 *
     * @param   int     $lineId         Id of the line
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
   	 * @return  int                     <0 if not ok, >0 if ok
   	 */
	public function deleteLine($lineId, $user, $noTrigger=0)
    {
        $this->db->begin();
        $error = 0;

        $dictionaryLine = new $this->dictionaryLineClassName($this->db, $this);

        $res = $dictionaryLine->fetch($lineId);
        if ($res > 0) {
            $res = $dictionaryLine->delete($user, $noTrigger);
            if ($res < 0) {
                $error++;
                $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
            }
        } elseif ($res < 0) {
            $error++;
            $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
   	 * Active line
   	 *
     * @param   int     $lineId         Id of the line
     * @param   int     $status         Status of the line, 0: desactived, 1: actived
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
   	 * @return  int                     <0 if not ok, >0 if ok
   	 */
	public function activeLine($lineId, $status, $user, $noTrigger=0)
    {
        $this->db->begin();
        $error = 0;

        $dictionaryLine = new $this->dictionaryLineClassName($this->db, $this);

        $res = $dictionaryLine->fetch($lineId);
        if ($res > 0) {
            if ($this->isLineCanBeDisabled($dictionaryLine) === true) {
                $res = $dictionaryLine->active($status, $user, $noTrigger);
                if ($res < 0) {
                    $error++;
                    $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
                }
            } else {
                return 0;
            }
        } elseif ($res < 0) {
            $error++;
            $this->errors = array_merge($this->errors, (array)$dictionaryLine->errors);
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
   	 *  Return code for the key/value search
   	 *
     * @param   string          $codePattern    Code pattern (replace {{FieldName}} by this value)
     * @param   array           $filters        List of filters: array(fieldName => value), value is a array search a list of rowid
   	 * @return  int|string                      Return code value, 0 if not found, -1 if found many, -2 if error
   	 */
	public function getCodeFromFilter($codePattern, $filters)
    {
        $lines = $this->fetch_lines(-1, $filters, array(), 0, 0, false, true);

        if (is_array($lines)) {
            $num = count($lines);
            if ($num > 1) {
                return -1;
            } elseif ($num == 0) {
                return 0;
            } else {
                foreach ($lines as $line) {
                    foreach ($line->fields as $fieldName => $fieldValue) {
                        $codePattern = str_replace('{{' . $fieldName . '}}', $fieldValue, $codePattern);
                    }
                    $codePattern = str_replace('{{' . $this->rowid_field . '}}', $line->id, $codePattern);
                    $codePattern = str_replace('{{' . $this->active_field . '}}', $line->active, $codePattern);
                    $codePattern = str_replace('{{' . $this->entity_field . '}}', $line->entity, $codePattern);
                }

                return $codePattern;
            }
        }

        return -2;
    }

    /**
   	 *  Load array lines with filters, orders
   	 *
     * @param   string  $key            Field name for the key of the line
     * @param   string  $label          Label pattern for the label of the line (replace {{FieldName}} by this value)
     * @param   array   $filters        List of filters: array(fieldName => value), value is a array search a list of rowid
     * @param   array   $orders         Order by: array(fieldName => order, ...)
     * @param   int     $limit          Length of the limit
     * @param   int     $filter_active  Filter on the active field (-1: all, 0: inactive, 1:active)
     * @param   bool    $return_array   Don't fetch lines in $this->lines
   	 * @return  array                   Lines array(key => label)
   	 */
	public function fetch_array($key, $label, $filters=array(), $orders=array(), $limit=0, $filter_active=1, $return_array=true)
    {
        $lines = $this->fetch_lines($filter_active, $filters, $orders, 0, $limit, false, $return_array);
        if (!$return_array) $lines = $this->lines;

        $results = array();
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $k = $key == 'rowid' ? $line->id : $line->fields[$key];
                $l = $label;
                foreach ($line->fields as $fieldName => $fieldValue) {
                    $l = str_replace('{{' . $fieldName . '}}', $fieldValue, $l);
                }
                $l = str_replace('{{' . $this->rowid_field . '}}', $line->id, $l);
                $l = str_replace('{{' . $this->active_field . '}}', $line->active, $l);
                $l = str_replace('{{' . $this->entity_field . '}}', $line->entity, $l);

                $results[$k] = $l;
            }
        }

        return $results;
    }

    /**
   	 *  Load array lines with filters, orders and limit
   	 *
     * @param   int                     $filter_active                  Filter on the active field (-1: all, 0: inactive, 1:active)
     * @param   array                   $filters                        List of filters: array(fieldName => value), value is a array search a list of rowid
     * @param   array                   $orders                         Order by: array(fieldName => order, ...)
     * @param   int                     $offset                         Offset of the limit
     * @param   int                     $limit                          Length of the limit
     * @param   bool                    $nb_lines                       Return only the nb line of the request if ok
     * @param   bool                    $return_array                   Return a array
     * @param   string                  $additionalWhereStatement       Additionnal lines of statement for where statement, [[fieldName]] replaced by this field name in the request, {{ }} if for the field id of multi-select field
     * @param   string                  $additionalHavingStatement      Additionnal lines of statement for having statement, [[fieldName]] replaced by this field name in the request, {{ }} if for the field id of multi-select field
	 * @param   string                  $filter_entity					Filter on the entity (by default with getEntity(), -1: all)
   	 * @return  int|DictionaryLine[]                                    <0 if KO, >0 if OK
   	 */
	public function fetch_lines($filter_active=-1, $filters=array(), $orders=array(), $offset=0, $limit=0, $nb_lines=false, $return_array=false, $additionalWhereStatement='', $additionalHavingStatement='', $filter_entity = '')
    {
        global $conf, $hookmanager;

        $hookmanager2 = clone $hookmanager; // Génère des erreurs de resultat disparaissant si appelé dans une autre hooks donc on copie la hook
        $select = array();
		$group_by = array();
        $from = "";
        foreach ($this->fields as $field) {
            // Select clause
            $sqlStatement = $this->selectFieldSqlStatement($field);
            if (!empty($sqlStatement)) {
				$select[] = $sqlStatement . (!empty($sqlStatement) ? ' AS ' . $field['name'] : '');
				if ($field['type'] != 'chkbxlst' && $field['type'] != 'chkbxlstwithorder') $group_by[] = $sqlStatement;
			}
            // From clause
            $from .= $this->fromFieldSqlStatement($field);
        }
        $where = array();
        $having = array();
        foreach ($filters as $fieldName => $value) {
            if (isset($this->fields[$fieldName])) {
                // From clause
                $from .= $this->fromFilterFieldSqlStatement($this->fields[$fieldName], $value);
                // Where clause
                $sqlStatement = $this->whereFieldSqlStatement($this->fields[$fieldName], $value);
                if (!empty($sqlStatement)) {
                    $where[] = $sqlStatement;
                }
                // Having clause
                $sqlStatement = $this->havingFieldSqlStatement($this->fields[$fieldName], $value);
                if (!empty($sqlStatement)) {
                    $having[] = $sqlStatement;
                }
            } elseif ($fieldName == $this->rowid_field || $fieldName == $this->active_field || $fieldName == $this->entity_field) {
                $where[] = natural_search('d.' . $fieldName, empty($value) ? '-1' : implode(',', $value), 2, 1);
            }
        }
		// TODO to complete substitution
		if (!empty($additionalWhereStatement)) $where[] = $additionalWhereStatement;
		if (!empty($additionalHavingStatement)) $having[] = $additionalHavingStatement;
        $sortfield = implode(',', array_keys($orders));
        $sortorder = implode(',', array_values($orders));

        $hookmanager2->initHooks(array('dictionarydao'));

        // TODO revoir le retour des valeurs pour le type chkbxlst ou chkbxlstwithorder quand on fournit un filtre avec tableau d'ids, seul les valeurs du tableau sont retourner et pas ttes les possibiltés avec ce filtre

        $parameters = array();
        $sql = 'SELECT d.' . $this->rowid_field . ', ' . implode(', ', $select) . ', d.' . $this->active_field;
        if ($this->has_entity) $sql .= ', d.' . $this->entity_field;
        // Add where from hooks
        $reshook = $hookmanager2->executeHooks('printADFetchLinesSelect', $parameters, $this);
        $sql .= $hookmanager2->resPrint;
        $sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_name . ' as d ' . $from;
        // Add where from hooks
        $reshook = $hookmanager2->executeHooks('printADFetchLinesFrom', $parameters, $this);
        $sql .= $hookmanager2->resPrint;
        if($filter_active >= 0) $where[] = 'd.' . $this->active_field . ' = ' . $filter_active;
        if($this->is_multi_entity && $this->has_entity && $filter_entity != -1) {
			if ($filter_entity === '') $filter_entity = array();
        	if (!is_array($filter_entity)) $filter_entity = explode(',', $filter_entity);
			if (empty($filter_entity) && !in_array($conf->entity, $filter_entity)) $filter_entity[] = $conf->entity;
			if (!in_array(0, $filter_entity)) $filter_entity[] = 0;
			if (!in_array(1, $filter_entity)) $filter_entity[] = 1;
        	$where[] = 'd.' . $this->entity_field . ' IN (' . implode(',', $filter_entity) . ')';
		}
        $sql .= !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
        // Add where from hooks
        $reshook = $hookmanager2->executeHooks('printADFetchLinesWhere', $parameters, $this);
        $sql .= $hookmanager2->resPrint;
        $sql .= ' GROUP BY d.' . $this->rowid_field . ', ' . implode(', ', $group_by) . ', d.' . $this->active_field;
		if ($this->has_entity) $sql .= ', d.' . $this->entity_field;
        $sql .= !empty($having) ? ' HAVING ' . implode(' AND ', $having) : '';
        // Add where from hooks
        $reshook = $hookmanager2->executeHooks('printADFetchLinesHaving', $parameters, $this);
        $sql .= (empty($having) && !empty($hookmanager2->resPrint) ? ' HAVING ' : '') . $hookmanager2->resPrint;
        $sql .= $this->db->order($sortfield, $sortorder);
        $sql .= $this->db->plimit($limit, $offset);

        dol_syslog(__METHOD__, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($nb_lines) {
                $result = $this->db->num_rows($resql);
            } else {
                $lines = array();
                while ($obj = $this->db->fetch_array($resql)) {
                    $line = new $this->dictionaryLineClassName($this->db, $this);

                    $line->id = $obj[$this->rowid_field];
                    $line->active = $obj[$this->active_field];
                    if ($this->has_entity) {
                        $line->entity = $obj[$this->entity_field];
                    }
                    $line->fields = array();
                    foreach ($this->fields as $k => $v) {
                        $line->fields[$k] = $line->formatFieldValueFromSQL($k, $obj[$k]);
                    }

                    $lines[$line->id] = $line;
                }
                $result = 1;

                if ($return_array) {
                    return $lines;
                } else {
                    $this->lines = $lines;
                }
            }

            $this->db->free($resql);

            return $result;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(__METHOD__ . " Sql: " . $sql . "; Error: " . $this->error, LOG_ERR);
            return -3;
        }
    }

    /**
   	 * Return the sql statement for the field in the select clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the field in the select clause
   	 */
	protected function selectFieldSqlStatement($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    return 'GROUP_CONCAT(DISTINCT cbl_' . $field['name'] . '.' . $this->getDestinationColumnAssociationTableName($field) .
						($field['type'] == 'chkbxlstwithorder' ? ' ORDER BY cbl_' . $field['name'] . '.rowid ASC' : '') . ' SEPARATOR \',\')';
                case 'custom':
                    return $this->selectCustomFieldSqlStatement($field);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    return 'd.' . $field['name'];
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the select clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the custom field in the select clause
   	 */
    protected function selectCustomFieldSqlStatement($field) {
   	    return '';
    }

    /**
   	 * Return the sql statement for the field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the field in the from clause
   	 */
	protected function fromFieldSqlStatement($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'sellist':
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    $sqlStatement = "";
                    if ($field['type'] == 'chkbxlst' || $field['type'] == 'chkbxlstwithorder') {
                        $sqlStatement .= ' LEFT JOIN ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' AS cbl_' . $field['name'] .
                            ' ON (cbl_' . $field['name'] . '.' . $this->getCurrentColumnAssociationTableName($field) . ' = d.' . $this->rowid_field . ')';
                    }

                    // 0 : tableName
                    // 1 : label field name
                    // 2 : key fields name (if differ of rowid)
                    // 3 : key field parent (for dependent lists)
                    // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                    $InfoFieldList = explode(":", (string)$field['options']);

                    $keyList = 'rowid';
                    if (count($InfoFieldList) >= 3) {
                        $keyList = $InfoFieldList[2] . ' as rowid';
                    }
                    $fields_label = explode('|', $InfoFieldList[1]);

                    $sqlStatement .= ' LEFT JOIN (';
                    $sqlStatement .= '   SELECT ' . $keyList;
                    $sqlStatement .= '   FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]);
                    if (strpos($InfoFieldList[4], 'extra') !== false) {
                        $sqlStatement .= ' as main';
                    }
                    $sqlStatement .= ') AS cbl_val_' . $field['name'] . ' ON (cbl_val_' . $field['name'] . '.rowid = '.($field['type'] == 'chkbxlst' || $field['type'] == 'chkbxlstwithorder' ? 'cbl_' . $field['name'] . '.' . $this->getDestinationColumnAssociationTableName($field) : 'd.' . $field['name']).')';

                    return $sqlStatement;
                case 'custom':
                    return $this->fromCustomFieldSqlStatement($field);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    return '';
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the custom field in the from clause
   	 */
    protected function fromCustomFieldSqlStatement($field) {
   	    return '';
    }

    /**
   	 * Return the sql filter statement for the field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the field in the having clause
   	 */
	protected function fromFilterFieldSqlStatement($field, $value)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    if (!is_array($value)) {
                        // 0 : tableName
                        // 1 : label field name
                        // 2 : key fields name (if differ of rowid)
                        // 3 : key field parent (for dependent lists)
                        // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                        $InfoFieldList = explode(":", (string)$field['options']);

                        $keyList = 'rowid';
                        if (count($InfoFieldList) >= 3) {
                            $keyList = $InfoFieldList[2] . ' as rowid';
                        }
                        $fields_label = explode('|', $InfoFieldList[1]);
                        if (is_array($fields_label)) {
                            $keyList .= ', ';
                            $keyList .= implode(', ', $fields_label);
                        }

                        $sqlStatement  = ' INNER JOIN (';
                        $sqlStatement .= '   SELECT DISTINCT l.' . $this->getCurrentColumnAssociationTableName($field);
                        $sqlStatement .= '   FROM ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' AS l';
                        $sqlStatement .= '   INNER JOIN (';
                        $sqlStatement .= '     SELECT ' . $keyList;
                        $sqlStatement .= '     FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]) . (strpos($InfoFieldList[4], 'extra') !== false ? ' as main' : '');
                        $sqlStatement .= '     WHERE ' . natural_search($fields_label, $value, 0, 1);
                        $sqlStatement .= '   ) AS v ON (l.' . $this->getDestinationColumnAssociationTableName($field) . ' = v.rowid)';
                        $sqlStatement .= ') AS search_cbl_' . $field['name'] . ' ON (search_cbl_' . $field['name'] . '.' . $this->getCurrentColumnAssociationTableName($field) . ' = d.' . $this->rowid_field . ')';

                        return $sqlStatement;
                    } else {
                        return '';
                    }
                case 'custom':
                    return $this->fromFilterCustomFieldSqlStatement($field, $value);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    return '';
            }
        }

        return '';
    }

    /**
   	 * Return the sql filter statement for the custom field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the custom field in the having clause
   	 */
    protected function fromFilterCustomFieldSqlStatement($field, $value) {
   	    return '';
    }

    /**
   	 * Return the sql statement for the field in the where clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the field in the where clause
   	 */
	protected function whereFieldSqlStatement($field, $value)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'varchar':
                case 'text':
                case 'phone':
                case 'mail':
                case 'url':
                case 'password':
                case 'link':
                    return natural_search('d.' . $field['name'], $value, 0, 1);
                case 'select':
                case 'radio':
                case 'checkbox':
                    $values = array();

                    if (is_array($value)) {
                        foreach ($value as $val) {
                            $values[$val] = $val;
                        }
                    } else {
                        foreach ($field['options'] as $key => $val) {
                            if (preg_match('/' . preg_quote($value, '/') . '/i', Dictionary::removeAccents(dol_html_entity_decode($val, ENT_QUOTES)))) {
                                $values[$key] = $key;
                            }
                        }
                    }

                    return 'd.' . $field['name'] . ' IN (' . (empty($values) ? '-1' : "'" . implode("','", $values) . "'") . ")";
                case 'sellist':
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            return natural_search('d.' . $field['name'], implode(',', $value), 3, 1);
                        } else {
                            return '';
                        }
                    } else {
                        // 0 : tableName
                        // 1 : label field name
                        // 2 : key fields name (if differ of rowid)
                        // 3 : key field parent (for dependent lists)
                        // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                        $InfoFieldList = explode(":", (string)$field['options']);

                        $fields_label = explode('|', $InfoFieldList[1]);
                        $fields = array();
                        foreach ($fields_label as $label) {
                            $tmp = explode('.', $label);
                            $fields[] = 'cbl_val_' . $field['name'] . '.' . (isset($tmp[1]) ? $tmp[1] : $label);
                        }

                        return natural_search($fields, $value, 0, 1);
                    }
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            return natural_search('cbl_' . $field['name'] . '.' . $this->getDestinationColumnAssociationTableName($field), implode(',', is_array($value[0])?$value[0]:$value), 2, 1);
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                case 'int':
                case 'float':
                case 'double':
                case 'price':
                    return natural_search('d.' . $field['name'], $value, 1, 1);
                case 'date':
                case 'datetime':
                    $where = array();
                    if (isset($value['date_start']) && $value['date_start'] !== "") $where[] = 'd.' . $field['name'] . " >= '" . $this->db->idate($value['date_start']) . "'";
                    if (isset($value['date_end']) && $value['date_end'] !== "") $where[] = 'd.' . $field['name'] . " <= '" . $this->db->idate($value['date_end']) . "'";
                    return implode(' AND ', $where);
                case 'boolean':
                    if (!empty($value)) {
						return 'd.' . $field['name'] . ' = ' . ($value > 0 ? '1' : '0');
					} else {
						return 'd.' . $field['name'] . ' IS NULL';
					}
                case 'custom':
                    return $this->whereCustomFieldSqlStatement($field, $value);
                default: // unknown
                    return '';
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the where clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the custom field in the where clause
   	 */
    protected function whereCustomFieldSqlStatement($field, $value) {
   	    return '';
    }

    /**
   	 * Return the sql statement for the field in the having clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the field in the having clause
   	 */
	protected function havingFieldSqlStatement($field, $value)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'custom':
                    return $this->havingCustomFieldSqlStatement($field, $value);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, chkbxlst, chkbxlstwithorder, radio, checkbox, link, unknown
                    return '';
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the having clause
   	 *
     * @param   array       $field      Description of the field
     * @param   mixed       $value      Value searched
     * @return  string                  Return the sql statement for the custom field in the having clause
   	 */
    protected function havingCustomFieldSqlStatement($field, $value) {
   	    return '';
    }

    /**
     * Return HTML string to put an search input field into a page
     *
     * @param   string   $fieldName         Name of the field
     * @param   array    $search_filters    List of values searched
     * @return  string
     */
	public function showInputSearchField($fieldName, $search_filters)
	{
	    global $langs, $form;

		if (isset($this->fields[$fieldName])) {
			$field = $this->fields[$fieldName];
			$fieldHtmlName = 'search_' . $fieldName;
			$type = $field['type'];

            if (!is_object($form)) {
                require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
                $form = new Form($this->db);
            }

			$size = $field['show_search_input']['size'] ?? null;
			if (empty($size)) {
				switch ($type) {
					case 'varchar':
					case 'text':
					case 'phone':
					case 'mail':
					case 'url':
					case 'password':
					case 'link':
						$size = 8;
						break;
					case 'int':
					case 'float':
					case 'double':
					case 'price':
					case 'date':
					case 'datetime':
						$size = 5;
						break;
					default: // unknown
						$size = 0;
						break;
				}
			}
			$size = !empty($size) ? ' size="' . $size . '"' : '';

			//$moreClasses = trim($field['show_search_input']['moreClasses']);
			//$moreClasses = !empty($moreClasses) ? ' ' . $moreClasses : '';
            $moreClasses = empty($field['show_search_input']['moreClasses']) ? '' : ' '.trim($field['show_search_input']['moreClasses']);

			// $moreAttributes = trim($field['show_search_input']['moreAttributes']);
			// $moreAttributes = !empty($moreAttributes) ? ' ' . $moreAttributes : '';
            $moreAttributes = empty($field['show_search_input']['moreAttributes']) ? '' : ' '.trim($field['show_search_input']['moreAttributes']);

			$dictionaryLine = $this->getNewDictionaryLine();

			switch ($type) {
				case 'varchar':
				case 'text':
				case 'phone':
				case 'mail':
				case 'url':
				case 'password':
				case 'link':
				case 'int':
				case 'float':
				case 'double':
				case 'price':
					return '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" name="' . $fieldHtmlName . '"' . $size .
						' value="' . dol_escape_htmltag($search_filters[$fieldName] ?? '') . '"' . $moreAttributes . '>';
                case 'date':
                case 'datetime':
                    $hm = $type == 'datetime' ? 1 : 0;
                    $out = '<div class="nowrap">';
                    $out .= $langs->trans('From') . ' ';
                    $out .= $form->selectDate(!empty($search_filters[$fieldName]['date_start']) ? $search_filters[$fieldName]['date_start'] : -1, $fieldHtmlName . '_start_', $hm, $hm, 1);
                    $out .= '</div>';
                    $out .= '<div class="nowrap">';
                    $out .= $langs->trans('to') . ' ';
                    $out .= $form->selectDate( !empty($search_filters[$fieldName]['date_end']) ? $search_filters[$fieldName]['date_end'] : -1, $fieldHtmlName . '_end_', $hm, $hm, 1);
                    $out .= '</div>';
                    return $out;
				case 'radio':
				case 'select':
				case 'checkbox':
					$old_type = $this->fields[$fieldName]['type'];
					$this->fields[$fieldName]['type'] = 'select';
					$out = $dictionaryLine->showInputFieldAD($fieldName, (isset($search_filters[$fieldName]) && is_array($search_filters[$fieldName])) ? $search_filters[$fieldName][0] : '', 'search_');
					$this->fields[$fieldName]['type'] = $old_type;
					return $out;
				case 'sellist':
				case 'chkbxlst':
                case 'chkbxlstwithorder':
					$old_type = $this->fields[$fieldName]['type'];
					$this->fields[$fieldName]['type'] = 'sellist';
					$out = $dictionaryLine->showInputFieldAD($fieldName, (isset($search_filters[$fieldName]) && is_array($search_filters[$fieldName])) ? $search_filters[$fieldName][0] : '', 'search_');
					$this->fields[$fieldName]['type'] = $old_type;
					return $out;
				case 'boolean':
					return $form->selectyesno($fieldHtmlName, !empty($search_filters[$fieldName]), 1, false, 1);
				case 'custom':
					return $this->showInputSearchCustomField($fieldName);
				default: // unknown
					return '';
			}
		}

		return '';
	}

    /**
     * Return HTML string to put an search input field into a page
     *
     * @param   string   $fieldName     Name of the field
     * @return  string
     */
    protected function showInputSearchCustomField($fieldName)
    {
        return '';
    }

    /**
   	 * Return tag to describe alignment to use for this field
   	 *
     * @param   string      $fieldName      Name of the field
   	 * @return	string					    Alignment value
   	 */
	public function getAlignFlagForField($fieldName)
    {
        $align = "left";

        if (isset($this->fields[$fieldName])) {
            $field = $this->fields[$fieldName];

            switch ($field['type']) {
                case 'boolean':
                case 'date':
                case 'datetime':
                    $align = "center";
                break;
//                case 'int':
//                case 'float':
//                case 'double':
                case 'price':
                    $align = "right";
                    break;
                case 'custom':
                    $align = $this->getAlignFlagForCustomField($fieldName);
                    break;
                default: // varchar, text, phone, mail, url, password, select, sellist, chkbxlst, chkbxlstwithorder, link, unknown
                    return $align;
            }
        }

        return $align;
    }

    /**
   	 * Return tag to describe alignment to use for this custom field
   	 *
     * @param   string      $fieldName      Name of the field
   	 * @return	string					    Alignment value
   	 */
    protected function getAlignFlagForCustomField($fieldName)
    {
        return 'left';
    }

    /**
   	 * Get value for each fields of the dictionary sent by a form
   	 *
   	 * @param  string   $keyprefix      Prefix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  string   $keysuffix      Suffix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  int      $mode           0: Add, 1: Edit, 2: All
   	 * @return array                    Values of each field
   	 */
	public function getFieldsValueFromForm($keyprefix='', $keysuffix='', $mode=0)
    {
        $fields = array();

        // Get fields
        foreach ($this->fields as $fieldName => $field) {
            $fieldHtmlName = $keyprefix . $fieldName . $keysuffix;

            if (($mode == 0 && empty($field['is_not_addable'])) || ($mode == 1 && empty($field['is_not_editable'])) || $mode == 2) {
                switch ($field['type']) {
                    case 'varchar':
                    case 'phone':
                    case 'mail':
                    case 'url':
                    case 'password':
                    case 'select':
                    case 'sellist':
                    case 'radio':
                    case 'link':
                        $value_key = GETPOST($fieldHtmlName, 'alpha');
                        break;
                    case 'text':
						$value_key = GETPOST($fieldHtmlName, $field['no_wysiwyg'] ? 'alphanohtml' : 'restricthtml');
                        break;
                    case 'checkbox':
                    case 'chkbxlst':
                    case 'chkbxlstwithorder':
                        $value_key = implode(',', GETPOST($fieldHtmlName, 'array'));
                        break;
                    case 'int':
                    case 'boolean':
                        $value_key = price2num(GETPOST($fieldHtmlName, 'int'));
                    break;
                    case 'float':
                    case 'double':
                    case 'price':
                        $value_key = price2num(GETPOST($fieldHtmlName, 'alpha'));
                        break;
                    case 'date':
                    case 'datetime':
                        $value_key = dol_mktime(
                            GETPOST($fieldHtmlName . "hour", 'int'),
                            GETPOST($fieldHtmlName . "min", 'int'),
                            0,
                            GETPOST($fieldHtmlName . "month", 'int'),
                            GETPOST($fieldHtmlName . "day", 'int'),
                            GETPOST($fieldHtmlName . "year", 'int')
                        );
                        break;
                    case 'custom':
                        $value_key = $this->getCustomFieldsValueFromForm($fieldName, $keyprefix, $keysuffix);
                        break;
                    default: // unknown
                        $value_key = '';
                        break;
                }

                $fields[$fieldName] = $value_key;
            }
        }

        return $fields;
    }

    /**
   	 * Return value for custom field of the dictionary sent by a form
   	 *
     * @param  string   $fieldName      Name of the field
   	 * @param  string   $keyprefix      Prefix string to add into name and id of field (can be used to avoid duplicate names)
   	 * @param  string   $keysuffix      Suffix string to add into name and id of field (can be used to avoid duplicate names)
   	 * @return mixed                    Return value for custom field of the dictionary sent by a form
   	 */
    protected function getCustomFieldsValueFromForm($fieldName, $keyprefix='', $keysuffix='')
    {
        return '';
    }

    /**
     * Get fixed value for parameters in url query
   	 *
   	 * @return string                    Fixed value for parameters
   	 */
	public function getFixedParameters()
    {
        return '';
    }

    /**
     * Get fixed value for each fixed fields of the dictionary
     *
     * @return array                    Values of each field
     */
    public function getFixedFieldsValue()
    {
        return array();
    }

    /**
   	 * Get search value for each fields of the dictionary sent by a form
   	 *
   	 * @return array                    Values of each field
   	 */
	public function getSearchFieldsValueFromForm()
    {
        $fields = array();

        // Get fields
        foreach ($this->fields as $fieldName => $field) {
			if (empty($field['is_not_searchable']) || !$field['is_not_searchable']) {
				$fieldHtmlName = 'search_' . $fieldName;

				switch ($field['type']) {
					case 'varchar':
					case 'phone':
					case 'mail':
					case 'url':
					case 'password':
					case 'link':
					case 'int':
					case 'float':
					case 'double':
					case 'price':
						$value_key = GETPOST($fieldHtmlName, 'alpha');
						if ($value_key === '') $value_key = null;
						break;
					case 'date':
					case 'datetime':
						$value_key = array();

						$date = GETPOST($fieldHtmlName . '_start', 'int');
						if (!is_numeric($date) || $date === "") {
							$year = GETPOST($fieldHtmlName . '_start_year', 'int');
							$month = GETPOST($fieldHtmlName . '_start_month', 'int');
							$day = GETPOST($fieldHtmlName . '_start_day', 'int');
							$hour = GETPOST($fieldHtmlName . '_start_hour', 'int');
							$minute = GETPOST($fieldHtmlName . '_start_min', 'int');
							$hour = $field['type'] == 'date' ? 0 : $hour;
							$minute = $field['type'] == 'date' ? 0 : $minute;
							$date = dol_mktime($hour, $minute, 0, $month, $day, $year);
						}
						if ($date !== "") $value_key['date_start'] = $date;

						$date = GETPOST($fieldHtmlName . '_end', 'int');
						if (!is_numeric($date) || $date === "") {
							$year = GETPOST($fieldHtmlName . '_end_year', 'int');
							$month = GETPOST($fieldHtmlName . '_end_month', 'int');
							$day = GETPOST($fieldHtmlName . '_end_day', 'int');
							$hour = GETPOST($fieldHtmlName . '_end_hour', 'int');
							$minute = GETPOST($fieldHtmlName . '_end_min', 'int');
							$hour = $field['type'] == 'date' || ($hour == -1 && ($year !== "" || $month !== "" || $day !== "")) ? 23 : $hour;
							$minute = $field['type'] == 'date' || ($minute == -1 && ($year !== "" || $month !== "" || $day !== "")) ? 59 : $minute;
							$date = dol_mktime($hour, $minute, 59, $month, $day, $year);
						}
						if ($date !== "") $value_key['date_end'] = $date;

						if (empty($value_key)) $value_key = null;
						break;
					case 'select':
					case 'sellist':
					case 'radio':
					case 'checkbox':
					case 'chkbxlst':
                    case 'chkbxlstwithorder':
						$value_key = GETPOST($fieldHtmlName, 'alpha');
						if ($value_key === '' || (is_array($field['empty_options']) && in_array($value_key, $field['empty_options']))) $value_key = null;
						else $value_key = array($value_key);
						break;
					case 'boolean':
						$value_key = GETPOST($fieldHtmlName, 'int');
						if ($value_key < 0 || $value_key === '') $value_key = null;
						break;
					case 'custom':
						$value_key = $this->getSearchCustomFieldsValueFromForm($fieldName);
						break;
					default: // unknown
						$value_key = null;
						break;
				}

				if ($value_key !== null) {
					$fields[$fieldName] = $value_key;
				}
			}
		}

        return $fields;
    }

    /**
   	 * Return search value for custom field of the dictionary sent by a form
   	 *
     * @param  string   $fieldName      Name of the field
   	 * @return mixed                    Return value for custom field of the dictionary sent by a form
   	 */
    protected function getSearchCustomFieldsValueFromForm($fieldName)
    {
        return null;
    }

	/**
	 * Return HTML string to put the script for update the list values of a select input into a page
	 *
	 * @param  array  	$default_values    	Default values when the page is reloaded and a select has not the options loaded
	 * @param  string  	$keyprefix      	Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  	$keysuffix      	Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @return string
	 */
	public function showUpdateListValuesScript($default_values = array(), $keyprefix='', $keysuffix='')
	{
		$fields_to_watch = array();
		foreach ($this->fields as $field_name => $field_info) {
			if (!empty($field_info['update_list_values'])) $fields_to_watch[] = $field_name;
		}
		if (!is_array($default_values)) $default_values = array();
		$default_values = array_values($default_values);

		$js_url = dol_escape_js(dol_buildpath('/advancedictionaries/js/advancedictionaries.js.php', 1));
		$module = dol_escape_js($this->module);
		$name = dol_escape_js($this->name);
		$root_path = dol_escape_js($this->root_path);
		$default_values = json_encode($default_values);
		$fields_to_watch = json_encode($fields_to_watch);
		$key_prefix = dol_escape_js($keyprefix);
		$key_suffix = dol_escape_js($keysuffix);

		return <<<SCRIPT
	<!-- Advanced Dictionaries - Update list values - Begin -->
	<script type="text/javascript" src="$js_url"></script>
	<script type="text/javascript">
		jQuery(document).ready(function () {
			advanced_dictionaries_watch_input('$module', '$name', '$root_path', $default_values, $fields_to_watch, '$key_prefix', '$key_suffix');
		});
	</script>
	<!-- Advanced Dictionaries - Update list values - End -->
SCRIPT;
	}

    /**
   	 * Determine if lines can be disabled or not
   	 *
     * @param  DictionaryLine   $dictionaryLine     Line instance
   	 * @return mixed                                =null: Show "Always active" text
     *                                              =true: Show button
     *                                              =string: Show the text returned, translated if key found
     *                                              other: Show disabled button
   	 */
	public function isLineCanBeDisabled(&$dictionaryLine)
    {
        return true;
    }

	/**
	 * Determine if lines can be updated or not
	 *
	 * @param  DictionaryLine   $dictionaryLine     Line instance
	 * @return bool
	 */
	public function isLineCanBeUpdated(&$dictionaryLine)
	{
		return true;
	}

	/**
	 * Determine if lines can be deleted or not
	 *
	 * @param  DictionaryLine   $dictionaryLine     Line instance
	 * @return bool
	 */
	public function isLineCanBeDeleted(&$dictionaryLine)
	{
		return true;
	}

    /**
     *  Get last row ID of the dictionary
     *
     * @return  int              Last row ID
     */
	public function getNextRowID()
    {
        $last_rowid = 0;
        $sql = 'SELECT MAX(' . $this->rowid_field . ') AS last_rowid FROM ' . MAIN_DB_PREFIX . $this->table_name;
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($obj = $this->db->fetch_object($resql)) {
                $last_rowid = $obj->last_rowid;
            }
        }

        return $last_rowid + 1;
    }

	/**
	 *  Get entity list for entity filter in SQL for the dictionary
	 *
	 * @return  string              Entity filter list
	 */
	public function getEntity()
	{
		global $conf;

		$filter_entity = array();
		if (!in_array(0, $filter_entity)) $filter_entity[] = 0;
		if (!in_array(1, $filter_entity)) $filter_entity[] = 1;
		if (!in_array($conf->entity, $filter_entity)) $filter_entity[] = $conf->entity;

		return implode(',', $filter_entity);
	}

	/**
	 * Function to get association table for chkbxlst or chkbxlstwithorder type field
	 * @param   array       $field      Description of the field
	 * @return string
	 */
	public function getAssociationTableName($field)
	{
		return !empty($field['association_table']['name']) ? $field['association_table']['name'] :  $this->table_name . '_cbl_' . $field['name'];
	}

	/**
	 * Function to get current object column name for chkbxlst or chkbxlstwithorder relation
	 * @param   array       $field      Description of the field
	 * @return string
	 */
	public function getCurrentColumnAssociationTableName($field)
	{
		return !empty($field['association_table']['fk_line_name']) ? $field['association_table']['fk_line_name'] : 'fk_line';
	}

	/**
	 * Function to get destination object column name for chkbxlst or chkbxlstwithorder relation
	 * @param   array       $field      Description of the field
	 * @return string
	 */
	public function getDestinationColumnAssociationTableName($field)
	{
		return !empty($field['association_table']['fk_target_name']) ? $field['association_table']['fk_target_name'] : 'fk_target';
	}

    /**
     * Function from wordpress v4.9.6
     *
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * **Accent characters converted:**
     *
     * Currency signs:
     *
     * |   Code   | Glyph | Replacement |     Description     |
     * | -------- | ----- | ----------- | ------------------- |
     * | U+00A3   | £     | (empty)     | British Pound sign  |
     * | U+20AC   | €     | E           | Euro sign           |
     *
     * Decompositions for Latin-1 Supplement:
     *
     * |  Code   | Glyph | Replacement |               Description              |
     * | ------- | ----- | ----------- | -------------------------------------- |
     * | U+00AA  | ª     | a           | Feminine ordinal indicator             |
     * | U+00BA  | º     | o           | Masculine ordinal indicator            |
     * | U+00C0  | À     | A           | Latin capital letter A with grave      |
     * | U+00C1  | Á     | A           | Latin capital letter A with acute      |
     * | U+00C2  | Â     | A           | Latin capital letter A with circumflex |
     * | U+00C3  | Ã     | A           | Latin capital letter A with tilde      |
     * | U+00C4  | Ä     | A           | Latin capital letter A with diaeresis  |
     * | U+00C5  | Å     | A           | Latin capital letter A with ring above |
     * | U+00C6  | Æ     | AE          | Latin capital letter AE                |
     * | U+00C7  | Ç     | C           | Latin capital letter C with cedilla    |
     * | U+00C8  | È     | E           | Latin capital letter E with grave      |
     * | U+00C9  | É     | E           | Latin capital letter E with acute      |
     * | U+00CA  | Ê     | E           | Latin capital letter E with circumflex |
     * | U+00CB  | Ë     | E           | Latin capital letter E with diaeresis  |
     * | U+00CC  | Ì     | I           | Latin capital letter I with grave      |
     * | U+00CD  | Í     | I           | Latin capital letter I with acute      |
     * | U+00CE  | Î     | I           | Latin capital letter I with circumflex |
     * | U+00CF  | Ï     | I           | Latin capital letter I with diaeresis  |
     * | U+00D0  | Ð     | D           | Latin capital letter Eth               |
     * | U+00D1  | Ñ     | N           | Latin capital letter N with tilde      |
     * | U+00D2  | Ò     | O           | Latin capital letter O with grave      |
     * | U+00D3  | Ó     | O           | Latin capital letter O with acute      |
     * | U+00D4  | Ô     | O           | Latin capital letter O with circumflex |
     * | U+00D5  | Õ     | O           | Latin capital letter O with tilde      |
     * | U+00D6  | Ö     | O           | Latin capital letter O with diaeresis  |
     * | U+00D8  | Ø     | O           | Latin capital letter O with stroke     |
     * | U+00D9  | Ù     | U           | Latin capital letter U with grave      |
     * | U+00DA  | Ú     | U           | Latin capital letter U with acute      |
     * | U+00DB  | Û     | U           | Latin capital letter U with circumflex |
     * | U+00DC  | Ü     | U           | Latin capital letter U with diaeresis  |
     * | U+00DD  | Ý     | Y           | Latin capital letter Y with acute      |
     * | U+00DE  | Þ     | TH          | Latin capital letter Thorn             |
     * | U+00DF  | ß     | s           | Latin small letter sharp s             |
     * | U+00E0  | à     | a           | Latin small letter a with grave        |
     * | U+00E1  | á     | a           | Latin small letter a with acute        |
     * | U+00E2  | â     | a           | Latin small letter a with circumflex   |
     * | U+00E3  | ã     | a           | Latin small letter a with tilde        |
     * | U+00E4  | ä     | a           | Latin small letter a with diaeresis    |
     * | U+00E5  | å     | a           | Latin small letter a with ring above   |
     * | U+00E6  | æ     | ae          | Latin small letter ae                  |
     * | U+00E7  | ç     | c           | Latin small letter c with cedilla      |
     * | U+00E8  | è     | e           | Latin small letter e with grave        |
     * | U+00E9  | é     | e           | Latin small letter e with acute        |
     * | U+00EA  | ê     | e           | Latin small letter e with circumflex   |
     * | U+00EB  | ë     | e           | Latin small letter e with diaeresis    |
     * | U+00EC  | ì     | i           | Latin small letter i with grave        |
     * | U+00ED  | í     | i           | Latin small letter i with acute        |
     * | U+00EE  | î     | i           | Latin small letter i with circumflex   |
     * | U+00EF  | ï     | i           | Latin small letter i with diaeresis    |
     * | U+00F0  | ð     | d           | Latin small letter Eth                 |
     * | U+00F1  | ñ     | n           | Latin small letter n with tilde        |
     * | U+00F2  | ò     | o           | Latin small letter o with grave        |
     * | U+00F3  | ó     | o           | Latin small letter o with acute        |
     * | U+00F4  | ô     | o           | Latin small letter o with circumflex   |
     * | U+00F5  | õ     | o           | Latin small letter o with tilde        |
     * | U+00F6  | ö     | o           | Latin small letter o with diaeresis    |
     * | U+00F8  | ø     | o           | Latin small letter o with stroke       |
     * | U+00F9  | ù     | u           | Latin small letter u with grave        |
     * | U+00FA  | ú     | u           | Latin small letter u with acute        |
     * | U+00FB  | û     | u           | Latin small letter u with circumflex   |
     * | U+00FC  | ü     | u           | Latin small letter u with diaeresis    |
     * | U+00FD  | ý     | y           | Latin small letter y with acute        |
     * | U+00FE  | þ     | th          | Latin small letter Thorn               |
     * | U+00FF  | ÿ     | y           | Latin small letter y with diaeresis    |
     *
     * Decompositions for Latin Extended-A:
     *
     * |  Code   | Glyph | Replacement |                    Description                    |
     * | ------- | ----- | ----------- | ------------------------------------------------- |
     * | U+0100  | Ā     | A           | Latin capital letter A with macron                |
     * | U+0101  | ā     | a           | Latin small letter a with macron                  |
     * | U+0102  | Ă     | A           | Latin capital letter A with breve                 |
     * | U+0103  | ă     | a           | Latin small letter a with breve                   |
     * | U+0104  | Ą     | A           | Latin capital letter A with ogonek                |
     * | U+0105  | ą     | a           | Latin small letter a with ogonek                  |
     * | U+01006 | Ć     | C           | Latin capital letter C with acute                 |
     * | U+0107  | ć     | c           | Latin small letter c with acute                   |
     * | U+0108  | Ĉ     | C           | Latin capital letter C with circumflex            |
     * | U+0109  | ĉ     | c           | Latin small letter c with circumflex              |
     * | U+010A  | Ċ     | C           | Latin capital letter C with dot above             |
     * | U+010B  | ċ     | c           | Latin small letter c with dot above               |
     * | U+010C  | Č     | C           | Latin capital letter C with caron                 |
     * | U+010D  | č     | c           | Latin small letter c with caron                   |
     * | U+010E  | Ď     | D           | Latin capital letter D with caron                 |
     * | U+010F  | ď     | d           | Latin small letter d with caron                   |
     * | U+0110  | Đ     | D           | Latin capital letter D with stroke                |
     * | U+0111  | đ     | d           | Latin small letter d with stroke                  |
     * | U+0112  | Ē     | E           | Latin capital letter E with macron                |
     * | U+0113  | ē     | e           | Latin small letter e with macron                  |
     * | U+0114  | Ĕ     | E           | Latin capital letter E with breve                 |
     * | U+0115  | ĕ     | e           | Latin small letter e with breve                   |
     * | U+0116  | Ė     | E           | Latin capital letter E with dot above             |
     * | U+0117  | ė     | e           | Latin small letter e with dot above               |
     * | U+0118  | Ę     | E           | Latin capital letter E with ogonek                |
     * | U+0119  | ę     | e           | Latin small letter e with ogonek                  |
     * | U+011A  | Ě     | E           | Latin capital letter E with caron                 |
     * | U+011B  | ě     | e           | Latin small letter e with caron                   |
     * | U+011C  | Ĝ     | G           | Latin capital letter G with circumflex            |
     * | U+011D  | ĝ     | g           | Latin small letter g with circumflex              |
     * | U+011E  | Ğ     | G           | Latin capital letter G with breve                 |
     * | U+011F  | ğ     | g           | Latin small letter g with breve                   |
     * | U+0120  | Ġ     | G           | Latin capital letter G with dot above             |
     * | U+0121  | ġ     | g           | Latin small letter g with dot above               |
     * | U+0122  | Ģ     | G           | Latin capital letter G with cedilla               |
     * | U+0123  | ģ     | g           | Latin small letter g with cedilla                 |
     * | U+0124  | Ĥ     | H           | Latin capital letter H with circumflex            |
     * | U+0125  | ĥ     | h           | Latin small letter h with circumflex              |
     * | U+0126  | Ħ     | H           | Latin capital letter H with stroke                |
     * | U+0127  | ħ     | h           | Latin small letter h with stroke                  |
     * | U+0128  | Ĩ     | I           | Latin capital letter I with tilde                 |
     * | U+0129  | ĩ     | i           | Latin small letter i with tilde                   |
     * | U+012A  | Ī     | I           | Latin capital letter I with macron                |
     * | U+012B  | ī     | i           | Latin small letter i with macron                  |
     * | U+012C  | Ĭ     | I           | Latin capital letter I with breve                 |
     * | U+012D  | ĭ     | i           | Latin small letter i with breve                   |
     * | U+012E  | Į     | I           | Latin capital letter I with ogonek                |
     * | U+012F  | į     | i           | Latin small letter i with ogonek                  |
     * | U+0130  | İ     | I           | Latin capital letter I with dot above             |
     * | U+0131  | ı     | i           | Latin small letter dotless i                      |
     * | U+0132  | Ĳ     | IJ          | Latin capital ligature IJ                         |
     * | U+0133  | ĳ     | ij          | Latin small ligature ij                           |
     * | U+0134  | Ĵ     | J           | Latin capital letter J with circumflex            |
     * | U+0135  | ĵ     | j           | Latin small letter j with circumflex              |
     * | U+0136  | Ķ     | K           | Latin capital letter K with cedilla               |
     * | U+0137  | ķ     | k           | Latin small letter k with cedilla                 |
     * | U+0138  | ĸ     | k           | Latin small letter Kra                            |
     * | U+0139  | Ĺ     | L           | Latin capital letter L with acute                 |
     * | U+013A  | ĺ     | l           | Latin small letter l with acute                   |
     * | U+013B  | Ļ     | L           | Latin capital letter L with cedilla               |
     * | U+013C  | ļ     | l           | Latin small letter l with cedilla                 |
     * | U+013D  | Ľ     | L           | Latin capital letter L with caron                 |
     * | U+013E  | ľ     | l           | Latin small letter l with caron                   |
     * | U+013F  | Ŀ     | L           | Latin capital letter L with middle dot            |
     * | U+0140  | ŀ     | l           | Latin small letter l with middle dot              |
     * | U+0141  | Ł     | L           | Latin capital letter L with stroke                |
     * | U+0142  | ł     | l           | Latin small letter l with stroke                  |
     * | U+0143  | Ń     | N           | Latin capital letter N with acute                 |
     * | U+0144  | ń     | n           | Latin small letter N with acute                   |
     * | U+0145  | Ņ     | N           | Latin capital letter N with cedilla               |
     * | U+0146  | ņ     | n           | Latin small letter n with cedilla                 |
     * | U+0147  | Ň     | N           | Latin capital letter N with caron                 |
     * | U+0148  | ň     | n           | Latin small letter n with caron                   |
     * | U+0149  | ŉ     | n           | Latin small letter n preceded by apostrophe       |
     * | U+014A  | Ŋ     | N           | Latin capital letter Eng                          |
     * | U+014B  | ŋ     | n           | Latin small letter Eng                            |
     * | U+014C  | Ō     | O           | Latin capital letter O with macron                |
     * | U+014D  | ō     | o           | Latin small letter o with macron                  |
     * | U+014E  | Ŏ     | O           | Latin capital letter O with breve                 |
     * | U+014F  | ŏ     | o           | Latin small letter o with breve                   |
     * | U+0150  | Ő     | O           | Latin capital letter O with double acute          |
     * | U+0151  | ő     | o           | Latin small letter o with double acute            |
     * | U+0152  | Œ     | OE          | Latin capital ligature OE                         |
     * | U+0153  | œ     | oe          | Latin small ligature oe                           |
     * | U+0154  | Ŕ     | R           | Latin capital letter R with acute                 |
     * | U+0155  | ŕ     | r           | Latin small letter r with acute                   |
     * | U+0156  | Ŗ     | R           | Latin capital letter R with cedilla               |
     * | U+0157  | ŗ     | r           | Latin small letter r with cedilla                 |
     * | U+0158  | Ř     | R           | Latin capital letter R with caron                 |
     * | U+0159  | ř     | r           | Latin small letter r with caron                   |
     * | U+015A  | Ś     | S           | Latin capital letter S with acute                 |
     * | U+015B  | ś     | s           | Latin small letter s with acute                   |
     * | U+015C  | Ŝ     | S           | Latin capital letter S with circumflex            |
     * | U+015D  | ŝ     | s           | Latin small letter s with circumflex              |
     * | U+015E  | Ş     | S           | Latin capital letter S with cedilla               |
     * | U+015F  | ş     | s           | Latin small letter s with cedilla                 |
     * | U+0160  | Š     | S           | Latin capital letter S with caron                 |
     * | U+0161  | š     | s           | Latin small letter s with caron                   |
     * | U+0162  | Ţ     | T           | Latin capital letter T with cedilla               |
     * | U+0163  | ţ     | t           | Latin small letter t with cedilla                 |
     * | U+0164  | Ť     | T           | Latin capital letter T with caron                 |
     * | U+0165  | ť     | t           | Latin small letter t with caron                   |
     * | U+0166  | Ŧ     | T           | Latin capital letter T with stroke                |
     * | U+0167  | ŧ     | t           | Latin small letter t with stroke                  |
     * | U+0168  | Ũ     | U           | Latin capital letter U with tilde                 |
     * | U+0169  | ũ     | u           | Latin small letter u with tilde                   |
     * | U+016A  | Ū     | U           | Latin capital letter U with macron                |
     * | U+016B  | ū     | u           | Latin small letter u with macron                  |
     * | U+016C  | Ŭ     | U           | Latin capital letter U with breve                 |
     * | U+016D  | ŭ     | u           | Latin small letter u with breve                   |
     * | U+016E  | Ů     | U           | Latin capital letter U with ring above            |
     * | U+016F  | ů     | u           | Latin small letter u with ring above              |
     * | U+0170  | Ű     | U           | Latin capital letter U with double acute          |
     * | U+0171  | ű     | u           | Latin small letter u with double acute            |
     * | U+0172  | Ų     | U           | Latin capital letter U with ogonek                |
     * | U+0173  | ų     | u           | Latin small letter u with ogonek                  |
     * | U+0174  | Ŵ     | W           | Latin capital letter W with circumflex            |
     * | U+0175  | ŵ     | w           | Latin small letter w with circumflex              |
     * | U+0176  | Ŷ     | Y           | Latin capital letter Y with circumflex            |
     * | U+0177  | ŷ     | y           | Latin small letter y with circumflex              |
     * | U+0178  | Ÿ     | Y           | Latin capital letter Y with diaeresis             |
     * | U+0179  | Ź     | Z           | Latin capital letter Z with acute                 |
     * | U+017A  | ź     | z           | Latin small letter z with acute                   |
     * | U+017B  | Ż     | Z           | Latin capital letter Z with dot above             |
     * | U+017C  | ż     | z           | Latin small letter z with dot above               |
     * | U+017D  | Ž     | Z           | Latin capital letter Z with caron                 |
     * | U+017E  | ž     | z           | Latin small letter z with caron                   |
     * | U+017F  | ſ     | s           | Latin small letter long s                         |
     * | U+01A0  | Ơ     | O           | Latin capital letter O with horn                  |
     * | U+01A1  | ơ     | o           | Latin small letter o with horn                    |
     * | U+01AF  | Ư     | U           | Latin capital letter U with horn                  |
     * | U+01B0  | ư     | u           | Latin small letter u with horn                    |
     * | U+01CD  | Ǎ     | A           | Latin capital letter A with caron                 |
     * | U+01CE  | ǎ     | a           | Latin small letter a with caron                   |
     * | U+01CF  | Ǐ     | I           | Latin capital letter I with caron                 |
     * | U+01D0  | ǐ     | i           | Latin small letter i with caron                   |
     * | U+01D1  | Ǒ     | O           | Latin capital letter O with caron                 |
     * | U+01D2  | ǒ     | o           | Latin small letter o with caron                   |
     * | U+01D3  | Ǔ     | U           | Latin capital letter U with caron                 |
     * | U+01D4  | ǔ     | u           | Latin small letter u with caron                   |
     * | U+01D5  | Ǖ     | U           | Latin capital letter U with diaeresis and macron  |
     * | U+01D6  | ǖ     | u           | Latin small letter u with diaeresis and macron    |
     * | U+01D7  | Ǘ     | U           | Latin capital letter U with diaeresis and acute   |
     * | U+01D8  | ǘ     | u           | Latin small letter u with diaeresis and acute     |
     * | U+01D9  | Ǚ     | U           | Latin capital letter U with diaeresis and caron   |
     * | U+01DA  | ǚ     | u           | Latin small letter u with diaeresis and caron     |
     * | U+01DB  | Ǜ     | U           | Latin capital letter U with diaeresis and grave   |
     * | U+01DC  | ǜ     | u           | Latin small letter u with diaeresis and grave     |
     *
     * Decompositions for Latin Extended-B:
     *
     * |   Code   | Glyph | Replacement |                Description                |
     * | -------- | ----- | ----------- | ----------------------------------------- |
     * | U+0218   | Ș     | S           | Latin capital letter S with comma below   |
     * | U+0219   | ș     | s           | Latin small letter s with comma below     |
     * | U+021A   | Ț     | T           | Latin capital letter T with comma below   |
     * | U+021B   | ț     | t           | Latin small letter t with comma below     |
     *
     * Vowels with diacritic (Chinese, Hanyu Pinyin):
     *
     * |   Code   | Glyph | Replacement |                      Description                      |
     * | -------- | ----- | ----------- | ----------------------------------------------------- |
     * | U+0251   | ɑ     | a           | Latin small letter alpha                              |
     * | U+1EA0   | Ạ     | A           | Latin capital letter A with dot below                 |
     * | U+1EA1   | ạ     | a           | Latin small letter a with dot below                   |
     * | U+1EA2   | Ả     | A           | Latin capital letter A with hook above                |
     * | U+1EA3   | ả     | a           | Latin small letter a with hook above                  |
     * | U+1EA4   | Ấ     | A           | Latin capital letter A with circumflex and acute      |
     * | U+1EA5   | ấ     | a           | Latin small letter a with circumflex and acute        |
     * | U+1EA6   | Ầ     | A           | Latin capital letter A with circumflex and grave      |
     * | U+1EA7   | ầ     | a           | Latin small letter a with circumflex and grave        |
     * | U+1EA8   | Ẩ     | A           | Latin capital letter A with circumflex and hook above |
     * | U+1EA9   | ẩ     | a           | Latin small letter a with circumflex and hook above   |
     * | U+1EAA   | Ẫ     | A           | Latin capital letter A with circumflex and tilde      |
     * | U+1EAB   | ẫ     | a           | Latin small letter a with circumflex and tilde        |
     * | U+1EA6   | Ậ     | A           | Latin capital letter A with circumflex and dot below  |
     * | U+1EAD   | ậ     | a           | Latin small letter a with circumflex and dot below    |
     * | U+1EAE   | Ắ     | A           | Latin capital letter A with breve and acute           |
     * | U+1EAF   | ắ     | a           | Latin small letter a with breve and acute             |
     * | U+1EB0   | Ằ     | A           | Latin capital letter A with breve and grave           |
     * | U+1EB1   | ằ     | a           | Latin small letter a with breve and grave             |
     * | U+1EB2   | Ẳ     | A           | Latin capital letter A with breve and hook above      |
     * | U+1EB3   | ẳ     | a           | Latin small letter a with breve and hook above        |
     * | U+1EB4   | Ẵ     | A           | Latin capital letter A with breve and tilde           |
     * | U+1EB5   | ẵ     | a           | Latin small letter a with breve and tilde             |
     * | U+1EB6   | Ặ     | A           | Latin capital letter A with breve and dot below       |
     * | U+1EB7   | ặ     | a           | Latin small letter a with breve and dot below         |
     * | U+1EB8   | Ẹ     | E           | Latin capital letter E with dot below                 |
     * | U+1EB9   | ẹ     | e           | Latin small letter e with dot below                   |
     * | U+1EBA   | Ẻ     | E           | Latin capital letter E with hook above                |
     * | U+1EBB   | ẻ     | e           | Latin small letter e with hook above                  |
     * | U+1EBC   | Ẽ     | E           | Latin capital letter E with tilde                     |
     * | U+1EBD   | ẽ     | e           | Latin small letter e with tilde                       |
     * | U+1EBE   | Ế     | E           | Latin capital letter E with circumflex and acute      |
     * | U+1EBF   | ế     | e           | Latin small letter e with circumflex and acute        |
     * | U+1EC0   | Ề     | E           | Latin capital letter E with circumflex and grave      |
     * | U+1EC1   | ề     | e           | Latin small letter e with circumflex and grave        |
     * | U+1EC2   | Ể     | E           | Latin capital letter E with circumflex and hook above |
     * | U+1EC3   | ể     | e           | Latin small letter e with circumflex and hook above   |
     * | U+1EC4   | Ễ     | E           | Latin capital letter E with circumflex and tilde      |
     * | U+1EC5   | ễ     | e           | Latin small letter e with circumflex and tilde        |
     * | U+1EC6   | Ệ     | E           | Latin capital letter E with circumflex and dot below  |
     * | U+1EC7   | ệ     | e           | Latin small letter e with circumflex and dot below    |
     * | U+1EC8   | Ỉ     | I           | Latin capital letter I with hook above                |
     * | U+1EC9   | ỉ     | i           | Latin small letter i with hook above                  |
     * | U+1ECA   | Ị     | I           | Latin capital letter I with dot below                 |
     * | U+1ECB   | ị     | i           | Latin small letter i with dot below                   |
     * | U+1ECC   | Ọ     | O           | Latin capital letter O with dot below                 |
     * | U+1ECD   | ọ     | o           | Latin small letter o with dot below                   |
     * | U+1ECE   | Ỏ     | O           | Latin capital letter O with hook above                |
     * | U+1ECF   | ỏ     | o           | Latin small letter o with hook above                  |
     * | U+1ED0   | Ố     | O           | Latin capital letter O with circumflex and acute      |
     * | U+1ED1   | ố     | o           | Latin small letter o with circumflex and acute        |
     * | U+1ED2   | Ồ     | O           | Latin capital letter O with circumflex and grave      |
     * | U+1ED3   | ồ     | o           | Latin small letter o with circumflex and grave        |
     * | U+1ED4   | Ổ     | O           | Latin capital letter O with circumflex and hook above |
     * | U+1ED5   | ổ     | o           | Latin small letter o with circumflex and hook above   |
     * | U+1ED6   | Ỗ     | O           | Latin capital letter O with circumflex and tilde      |
     * | U+1ED7   | ỗ     | o           | Latin small letter o with circumflex and tilde        |
     * | U+1ED8   | Ộ     | O           | Latin capital letter O with circumflex and dot below  |
     * | U+1ED9   | ộ     | o           | Latin small letter o with circumflex and dot below    |
     * | U+1EDA   | Ớ     | O           | Latin capital letter O with horn and acute            |
     * | U+1EDB   | ớ     | o           | Latin small letter o with horn and acute              |
     * | U+1EDC   | Ờ     | O           | Latin capital letter O with horn and grave            |
     * | U+1EDD   | ờ     | o           | Latin small letter o with horn and grave              |
     * | U+1EDE   | Ở     | O           | Latin capital letter O with horn and hook above       |
     * | U+1EDF   | ở     | o           | Latin small letter o with horn and hook above         |
     * | U+1EE0   | Ỡ     | O           | Latin capital letter O with horn and tilde            |
     * | U+1EE1   | ỡ     | o           | Latin small letter o with horn and tilde              |
     * | U+1EE2   | Ợ     | O           | Latin capital letter O with horn and dot below        |
     * | U+1EE3   | ợ     | o           | Latin small letter o with horn and dot below          |
     * | U+1EE4   | Ụ     | U           | Latin capital letter U with dot below                 |
     * | U+1EE5   | ụ     | u           | Latin small letter u with dot below                   |
     * | U+1EE6   | Ủ     | U           | Latin capital letter U with hook above                |
     * | U+1EE7   | ủ     | u           | Latin small letter u with hook above                  |
     * | U+1EE8   | Ứ     | U           | Latin capital letter U with horn and acute            |
     * | U+1EE9   | ứ     | u           | Latin small letter u with horn and acute              |
     * | U+1EEA   | Ừ     | U           | Latin capital letter U with horn and grave            |
     * | U+1EEB   | ừ     | u           | Latin small letter u with horn and grave              |
     * | U+1EEC   | Ử     | U           | Latin capital letter U with horn and hook above       |
     * | U+1EED   | ử     | u           | Latin small letter u with horn and hook above         |
     * | U+1EEE   | Ữ     | U           | Latin capital letter U with horn and tilde            |
     * | U+1EEF   | ữ     | u           | Latin small letter u with horn and tilde              |
     * | U+1EF0   | Ự     | U           | Latin capital letter U with horn and dot below        |
     * | U+1EF1   | ự     | u           | Latin small letter u with horn and dot below          |
     * | U+1EF2   | Ỳ     | Y           | Latin capital letter Y with grave                     |
     * | U+1EF3   | ỳ     | y           | Latin small letter y with grave                       |
     * | U+1EF4   | Ỵ     | Y           | Latin capital letter Y with dot below                 |
     * | U+1EF5   | ỵ     | y           | Latin small letter y with dot below                   |
     * | U+1EF6   | Ỷ     | Y           | Latin capital letter Y with hook above                |
     * | U+1EF7   | ỷ     | y           | Latin small letter y with hook above                  |
     * | U+1EF8   | Ỹ     | Y           | Latin capital letter Y with tilde                     |
     * | U+1EF9   | ỹ     | y           | Latin small letter y with tilde                       |
     *
     * German (`de_DE`), German formal (`de_DE_formal`), German (Switzerland) formal (`de_CH`),
     * and German (Switzerland) informal (`de_CH_informal`) locales:
     *
     * |   Code   | Glyph | Replacement |               Description               |
     * | -------- | ----- | ----------- | --------------------------------------- |
     * | U+00C4   | Ä     | Ae          | Latin capital letter A with diaeresis   |
     * | U+00E4   | ä     | ae          | Latin small letter a with diaeresis     |
     * | U+00D6   | Ö     | Oe          | Latin capital letter O with diaeresis   |
     * | U+00F6   | ö     | oe          | Latin small letter o with diaeresis     |
     * | U+00DC   | Ü     | Ue          | Latin capital letter U with diaeresis   |
     * | U+00FC   | ü     | ue          | Latin small letter u with diaeresis     |
     * | U+00DF   | ß     | ss          | Latin small letter sharp s              |
     *
     * Danish (`da_DK`) locale:
     *
     * |   Code   | Glyph | Replacement |               Description               |
     * | -------- | ----- | ----------- | --------------------------------------- |
     * | U+00C6   | Æ     | Ae          | Latin capital letter AE                 |
     * | U+00E6   | æ     | ae          | Latin small letter ae                   |
     * | U+00D8   | Ø     | Oe          | Latin capital letter O with stroke      |
     * | U+00F8   | ø     | oe          | Latin small letter o with stroke        |
     * | U+00C5   | Å     | Aa          | Latin capital letter A with ring above  |
     * | U+00E5   | å     | aa          | Latin small letter a with ring above    |
     *
     * Catalan (`ca`) locale:
     *
     * |   Code   | Glyph | Replacement |               Description               |
     * | -------- | ----- | ----------- | --------------------------------------- |
     * | U+00B7   | l·l   | ll          | Flown dot (between two Ls)              |
     *
     * Serbian (`sr_RS`) and Bosnian (`bs_BA`) locales:
     *
     * |   Code   | Glyph | Replacement |               Description               |
     * | -------- | ----- | ----------- | --------------------------------------- |
     * | U+0110   | Đ     | DJ          | Latin capital letter D with stroke      |
     * | U+0111   | đ     | dj          | Latin small letter d with stroke        |
     *
     * @since 1.2.1
     * @since 4.6.0 Added locale support for `de_CH`, `de_CH_informal`, and `ca`.
     * @since 4.7.0 Added locale support for `sr_RS`.
     * @since 4.8.0 Added locale support for `bs_BA`.
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */
    static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        if ((preg_match('~~u', $string) && !preg_match('~[\\0-\\x8\\xB\\xC\\xE-\\x1F]~', $string))) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                'ª' => 'a', 'º' => 'o',
                'À' => 'A', 'Á' => 'A',
                'Â' => 'A', 'Ã' => 'A',
                'Ä' => 'A', 'Å' => 'A',
                'Æ' => 'AE', 'Ç' => 'C',
                'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E',
                'Ì' => 'I', 'Í' => 'I',
                'Î' => 'I', 'Ï' => 'I',
                'Ð' => 'D', 'Ñ' => 'N',
                'Ò' => 'O', 'Ó' => 'O',
                'Ô' => 'O', 'Õ' => 'O',
                'Ö' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U',
                'Ü' => 'U', 'Ý' => 'Y',
                'Þ' => 'TH', 'ß' => 's',
                'à' => 'a', 'á' => 'a',
                'â' => 'a', 'ã' => 'a',
                'ä' => 'a', 'å' => 'a',
                'æ' => 'ae', 'ç' => 'c',
                'è' => 'e', 'é' => 'e',
                'ê' => 'e', 'ë' => 'e',
                'ì' => 'i', 'í' => 'i',
                'î' => 'i', 'ï' => 'i',
                'ð' => 'd', 'ñ' => 'n',
                'ò' => 'o', 'ó' => 'o',
                'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o',
                'ù' => 'u', 'ú' => 'u',
                'û' => 'u', 'ü' => 'u',
                'ý' => 'y', 'þ' => 'th',
                'ÿ' => 'y', 'Ø' => 'O',
                // Decompositions for Latin Extended-A
                'Ā' => 'A', 'ā' => 'a',
                'Ă' => 'A', 'ă' => 'a',
                'Ą' => 'A', 'ą' => 'a',
                'Ć' => 'C', 'ć' => 'c',
                'Ĉ' => 'C', 'ĉ' => 'c',
                'Ċ' => 'C', 'ċ' => 'c',
                'Č' => 'C', 'č' => 'c',
                'Ď' => 'D', 'ď' => 'd',
                'Đ' => 'D', 'đ' => 'd',
                'Ē' => 'E', 'ē' => 'e',
                'Ĕ' => 'E', 'ĕ' => 'e',
                'Ė' => 'E', 'ė' => 'e',
                'Ę' => 'E', 'ę' => 'e',
                'Ě' => 'E', 'ě' => 'e',
                'Ĝ' => 'G', 'ĝ' => 'g',
                'Ğ' => 'G', 'ğ' => 'g',
                'Ġ' => 'G', 'ġ' => 'g',
                'Ģ' => 'G', 'ģ' => 'g',
                'Ĥ' => 'H', 'ĥ' => 'h',
                'Ħ' => 'H', 'ħ' => 'h',
                'Ĩ' => 'I', 'ĩ' => 'i',
                'Ī' => 'I', 'ī' => 'i',
                'Ĭ' => 'I', 'ĭ' => 'i',
                'Į' => 'I', 'į' => 'i',
                'İ' => 'I', 'ı' => 'i',
                'Ĳ' => 'IJ', 'ĳ' => 'ij',
                'Ĵ' => 'J', 'ĵ' => 'j',
                'Ķ' => 'K', 'ķ' => 'k',
                'ĸ' => 'k', 'Ĺ' => 'L',
                'ĺ' => 'l', 'Ļ' => 'L',
                'ļ' => 'l', 'Ľ' => 'L',
                'ľ' => 'l', 'Ŀ' => 'L',
                'ŀ' => 'l', 'Ł' => 'L',
                'ł' => 'l', 'Ń' => 'N',
                'ń' => 'n', 'Ņ' => 'N',
                'ņ' => 'n', 'Ň' => 'N',
                'ň' => 'n', 'ŉ' => 'n',
                'Ŋ' => 'N', 'ŋ' => 'n',
                'Ō' => 'O', 'ō' => 'o',
                'Ŏ' => 'O', 'ŏ' => 'o',
                'Ő' => 'O', 'ő' => 'o',
                'Œ' => 'OE', 'œ' => 'oe',
                'Ŕ' => 'R', 'ŕ' => 'r',
                'Ŗ' => 'R', 'ŗ' => 'r',
                'Ř' => 'R', 'ř' => 'r',
                'Ś' => 'S', 'ś' => 's',
                'Ŝ' => 'S', 'ŝ' => 's',
                'Ş' => 'S', 'ş' => 's',
                'Š' => 'S', 'š' => 's',
                'Ţ' => 'T', 'ţ' => 't',
                'Ť' => 'T', 'ť' => 't',
                'Ŧ' => 'T', 'ŧ' => 't',
                'Ũ' => 'U', 'ũ' => 'u',
                'Ū' => 'U', 'ū' => 'u',
                'Ŭ' => 'U', 'ŭ' => 'u',
                'Ů' => 'U', 'ů' => 'u',
                'Ű' => 'U', 'ű' => 'u',
                'Ų' => 'U', 'ų' => 'u',
                'Ŵ' => 'W', 'ŵ' => 'w',
                'Ŷ' => 'Y', 'ŷ' => 'y',
                'Ÿ' => 'Y', 'Ź' => 'Z',
                'ź' => 'z', 'Ż' => 'Z',
                'ż' => 'z', 'Ž' => 'Z',
                'ž' => 'z', 'ſ' => 's',
                // Decompositions for Latin Extended-B
                'Ș' => 'S', 'ș' => 's',
                'Ț' => 'T', 'ț' => 't',
                // Euro Sign
                '€' => 'E',
                // GBP (Pound) Sign
                '£' => '',
                // Vowels with diacritic (Vietnamese)
                // unmarked
                'Ơ' => 'O', 'ơ' => 'o',
                'Ư' => 'U', 'ư' => 'u',
                // grave accent
                'Ầ' => 'A', 'ầ' => 'a',
                'Ằ' => 'A', 'ằ' => 'a',
                'Ề' => 'E', 'ề' => 'e',
                'Ồ' => 'O', 'ồ' => 'o',
                'Ờ' => 'O', 'ờ' => 'o',
                'Ừ' => 'U', 'ừ' => 'u',
                'Ỳ' => 'Y', 'ỳ' => 'y',
                // hook
                'Ả' => 'A', 'ả' => 'a',
                'Ẩ' => 'A', 'ẩ' => 'a',
                'Ẳ' => 'A', 'ẳ' => 'a',
                'Ẻ' => 'E', 'ẻ' => 'e',
                'Ể' => 'E', 'ể' => 'e',
                'Ỉ' => 'I', 'ỉ' => 'i',
                'Ỏ' => 'O', 'ỏ' => 'o',
                'Ổ' => 'O', 'ổ' => 'o',
                'Ở' => 'O', 'ở' => 'o',
                'Ủ' => 'U', 'ủ' => 'u',
                'Ử' => 'U', 'ử' => 'u',
                'Ỷ' => 'Y', 'ỷ' => 'y',
                // tilde
                'Ẫ' => 'A', 'ẫ' => 'a',
                'Ẵ' => 'A', 'ẵ' => 'a',
                'Ẽ' => 'E', 'ẽ' => 'e',
                'Ễ' => 'E', 'ễ' => 'e',
                'Ỗ' => 'O', 'ỗ' => 'o',
                'Ỡ' => 'O', 'ỡ' => 'o',
                'Ữ' => 'U', 'ữ' => 'u',
                'Ỹ' => 'Y', 'ỹ' => 'y',
                // acute accent
                'Ấ' => 'A', 'ấ' => 'a',
                'Ắ' => 'A', 'ắ' => 'a',
                'Ế' => 'E', 'ế' => 'e',
                'Ố' => 'O', 'ố' => 'o',
                'Ớ' => 'O', 'ớ' => 'o',
                'Ứ' => 'U', 'ứ' => 'u',
                // dot below
                'Ạ' => 'A', 'ạ' => 'a',
                'Ậ' => 'A', 'ậ' => 'a',
                'Ặ' => 'A', 'ặ' => 'a',
                'Ẹ' => 'E', 'ẹ' => 'e',
                'Ệ' => 'E', 'ệ' => 'e',
                'Ị' => 'I', 'ị' => 'i',
                'Ọ' => 'O', 'ọ' => 'o',
                'Ộ' => 'O', 'ộ' => 'o',
                'Ợ' => 'O', 'ợ' => 'o',
                'Ụ' => 'U', 'ụ' => 'u',
                'Ự' => 'U', 'ự' => 'u',
                'Ỵ' => 'Y', 'ỵ' => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin)
                'ɑ' => 'a',
                // macron
                'Ǖ' => 'U', 'ǖ' => 'u',
                // acute accent
                'Ǘ' => 'U', 'ǘ' => 'u',
                // caron
                'Ǎ' => 'A', 'ǎ' => 'a',
                'Ǐ' => 'I', 'ǐ' => 'i',
                'Ǒ' => 'O', 'ǒ' => 'o',
                'Ǔ' => 'U', 'ǔ' => 'u',
                'Ǚ' => 'U', 'ǚ' => 'u',
                // grave accent
                'Ǜ' => 'U', 'ǜ' => 'u',
            );

            // Used for locale-specific rules
            /*$locale = get_locale();

            if ('de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale) {
                $chars['Ä'] = 'Ae';
                $chars['ä'] = 'ae';
                $chars['Ö'] = 'Oe';
                $chars['ö'] = 'oe';
                $chars['Ü'] = 'Ue';
                $chars['ü'] = 'ue';
                $chars['ß'] = 'ss';
            } elseif ('da_DK' === $locale) {
                $chars['Æ'] = 'Ae';
                $chars['æ'] = 'ae';
                $chars['Ø'] = 'Oe';
                $chars['ø'] = 'oe';
                $chars['Å'] = 'Aa';
                $chars['å'] = 'aa';
            } elseif ('ca' === $locale) {
                $chars['l·l'] = 'll';
            } elseif ('sr_RS' === $locale || 'bs_BA' === $locale) {
                $chars['Đ'] = 'DJ';
                $chars['đ'] = 'dj';
            }*/

            $string = strtr($string, $chars);
        } else {
            $chars = array();
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
                . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
                . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
                . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
                . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
                . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
                . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
                . "\xec\xed\xee\xef\xf1\xf2\xf3"
                . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
                . "\xfc\xfd\xff";

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars = array();
            $double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }
}

/**
 * Class DictionaryLine : For extended class, it must be contain the same name as the extended Dictionary with 'Line' at the end of the class name (ex: CountryDictionary and CountryDictionaryLine)
 */
class DictionaryLine extends CommonObjectLine
{
    /**
     * @var Dictionary      Dictionary handler
     */
    public $dictionary;

    /**
     * @var int             Id of the line
     */
    public $id;

    /**
     * @var int             Entity of the line
     */
    public $entity;

    /**
     * @var int             If the line is active (=1, if not = 0)
     */
    public $active;

    /**
     * @var array           List of value of the fields: array(fieldName => value, ...)
     */
    public $fields = array();

    /**
     * @var array           List of object cached
     */
    static protected $objects_cached = array();

    /**
     *  Constructor
     *
     * @param   DoliDB      $db             Database handler
     * @param   Dictionary  $dictionary     Dictionary handler
     */
    public function __construct($db, &$dictionary=null)
    {
        $this->db = $db;

        if ($dictionary === null) {
            $dictionaryClassName = substr(get_class($this), 0, -4);
            if (class_exists($dictionaryClassName, false)) {
                $this->dictionary = new $dictionaryClassName($this->db);
            } else {
                $this->dictionary = new Dictionary($this->db);
            }
        } else {
            $this->dictionary = $dictionary;
        }
    }

    /**
     *  Check values fields
     *
     * @param   array   $fieldsValue    Values of the fields array(name => value, ...)
     * @return  int                     <0 if not ok, >0 if ok
     */
	public function checkFieldsValues($fieldsValue)
	{
		global $langs;

		$langs->loadLangs($this->dictionary->langs);

		$check = true;
		foreach ($this->dictionary->fields as $fieldName => $field) {
			if (!empty($field['is_require'])) {
				$checkField = true;
				if (isset($fieldsValue[$fieldName])) {
					$value = $fieldsValue[$fieldName];
					switch ($field['type']) {
						case 'varchar':
						case 'text':
						case 'phone':
						case 'mail':
						case 'url':
						case 'password':
							if (empty($value))
								$checkField = false;
							break;
						case 'checkbox':
						case 'chkbxlst':
                        case 'chkbxlstwithorder':
							if (trim($value) === '')
								$checkField = false;
							break;
						case 'int':
						case 'float':
						case 'double':
						case 'price':
							if ($value === '')
								$checkField = false;
							break;
						case 'date':
						case 'datetime':
							if (empty($value))
								$checkField = false;
							break;
						case 'link':
						case 'radio':
						case 'select':
						case 'sellist':
						case 'boolean':
							if (trim($value) === '' || $value == -1)
								$checkField = false;
							break;
						default: // unknown
							break;
					}
				} else {
					$checkField = false;
				}

				if (!$checkField) {
					$check = false;
					$this->errors[] = $langs->trans("ErrorFieldRequired", $langs->transnoentities($field['label']));
				}
			}

			if (isset($fieldsValue[$fieldName])) {
				$value = $fieldsValue[$fieldName];
				switch ($field['type']) {
					case 'int':
					case 'float':
					case 'double':
					case 'price':
						if (isset($field['min']) && $value < $field['min']) {
							$check = false;
							$this->errors[] = $langs->trans("AdvanceDictionariesErrorValueMustBeGreaterOrEqualThan", $field['min']);
						}
						if (isset($field['max']) && $value > $field['max']) {
							$check = false;
							$this->errors[] = $langs->trans("AdvanceDictionariesErrorValueMustBeLesserOrEqualThan", $field['max']);
						}
						break;
					case 'custom':
						if ($this->checkCustomFieldValue($field, $value) < 0)
							$check = false;
						break;
					default: // unknown
						break;
				}
			}
		}

		if ($check)
			return 1;
		else
			return -1;
	}

    /**
     *  Check value of the custom field
     *
     * @param   array   $field          Information of the field
     * @param   mixed   $value          Values of the field
     * @return  int                     <0 if not ok, >0 if ok
     */
    protected function checkCustomFieldValue($field, $value)
    {
        return 1;
    }

    /**
     *  Insert line
     *
     * @param   array   $fieldsValue   Values of the fields array(name => value, ...)
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
     * @return  int                     <0 if not ok, >0 if ok
     */
	public function insert($fieldsValue, $user, $noTrigger = 0)
    {
        global $conf;
        dol_syslog(__METHOD__ . " fieldsValues: " . http_build_query($fieldsValue));

        if ($this->checkFieldsValues($fieldsValue) > 0) {
            $this->db->begin();
            $error = 0;
            $errors = array();
            $this->fields = $fieldsValue;

			$cq = $this->db->type == 'pgsql' ? '"' : '`';

			// Insert line into dictionary table
            $insert_field = array();
            $insert_statement = array();
            foreach ($this->dictionary->fields as $fieldName => $field) {
                if (($formattedValue = $this->formatFieldValueForSQL($fieldName, $this->fields[$fieldName])) !== null) {
                    $insert_field[] = $fieldName;
                    $insert_statement[] = $formattedValue;
                }
            }
            $rowid = !$this->dictionary->is_rowid_auto_increment ? ($this->dictionary->is_rowid_defined_by_code ? $this->id : $this->dictionary->getNextRowID()) : 0;
            $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->dictionary->table_name . ' (' . $cq .
                (!$this->dictionary->is_rowid_auto_increment ? $this->dictionary->rowid_field . $cq . ', ' . $cq : '') .
                implode($cq . ', ' . $cq, $insert_field) .
				$cq . ', ' . $cq . $this->dictionary->active_field . ($this->dictionary->has_entity ? $cq . ', ' . $cq . $this->dictionary->entity_field : '') .
				$cq . ') VALUES (' .
                (!$this->dictionary->is_rowid_auto_increment ? $rowid . ', ' : '') .
                implode(', ', $insert_statement) . ', 1' . ($this->dictionary->has_entity ? ', ' . $conf->entity : '') . ')';

            dol_syslog(__METHOD__, LOG_DEBUG);
            $resql = $this->db->query($sql);
            if (!$resql) {
                dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
                $error++;
                $errors[] = $this->db->lasterror();
            } else {
                $this->id = $this->dictionary->is_rowid_auto_increment ? $this->db->last_insert_id(MAIN_DB_PREFIX . $this->dictionary->table_name) : $rowid;
            }

            // Insert post line of dictionary table
            if (!$error) {
                foreach ($this->fields as $fieldName => $value) {
                    $field = $this->dictionary->fields[$fieldName];
                    switch ($field['type']) {
                        case 'chkbxlst':
                        case 'chkbxlstwithorder':
                            // Delete association line for the multi-select list
                            $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' WHERE ' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ' = ' . $this->id;
                            $resql = $this->db->query($sql);
                            if (!$resql) {
                                dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
                                $error++;
                                $errors[] = $this->db->lasterror();
                            } else {
                                // Insert association line for the multi-select list
                                $insert_values = array();
                                $value_arr = array();
                                if (is_array($value)) {
                                    $value_arr = $value;
                                } elseif (!empty($value)) {
                                    $value_arr = array_filter(array_map('trim', explode(',', (string)$value)), 'strlen');
                                }
                                foreach ($value_arr as $value_id) {
                                    $insert_values[] = '(' . $this->id . ', ' . $value_id . ')';
                                }

                                if (count($insert_values) > 0) {
                                    $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . '(' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ', ' . $cq . $this->getDestinationColumnAssociationTableName($field) . $cq . ') VALUES' . implode(',', $insert_values);
                                    $resql = $this->db->query($sql);
                                    if (!$resql) {
                                        dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
                                        $error++;
                                        $errors[] = $this->db->lasterror();
                                    }
                                }
                            }
                            break;
                        case 'custom':
                            $res = $this->insertCustomFieldLine($field, $this->id, $value, $user);
                            if ($res < 0) {
                                $error++;
                            }
                            break;
                        default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                            break;
                    }
                    if ($error) break;
                }
            }

            if (!$error && !$noTrigger) {
                // Call trigger
                $result = $this->call_trigger('LINEDICTIONARY_CREATE', $user);
                if ($result < 0) $error++;
                // End call triggers
            }

			if (!$error) {
				$result = $this->insertLineSuccess($user);
				if ($result < 0) $error++;
			}

            if (!$error) {
                $this->db->commit();
                return 1;
            } else {
                foreach ($errors as $errmsg) {
                    $this->errors[] = ($this->errors ? ', ' : '') . $errmsg;
                }

                $this->fields = array();
                $this->db->rollback();
                return -1;
            }
        } else {
            return -1;
        }
    }

    /**
     *  Insert line for the custom field
     *
     * @param   string	$fieldName      Name of the field
     * @param   int     $lineId         Id of the line
     * @param   mixed   $value          Value of the field
     * @param   User    $user           User who add this line
     * @return  int                     <0 if not ok, >0 if ok
     */
    protected function insertCustomFieldLine($fieldName, $lineId, $value, $user)
    {
        return 1;
    }

    /**
     *  Update line
     *
     * @param   array   $fieldsValue    Values of the fields array(name => value, ...)
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
     * @return  int                     <0 if not ok, >0 if ok
     */
	public function update($fieldsValue, $user, $noTrigger = 0)
    {
        dol_syslog(__METHOD__ . " lineId: " . $this->id . "; fieldsValues: " . http_build_query($fieldsValue));

        if ($this->checkFieldsValues($fieldsValue) > 0) {
            $this->db->begin();
            $error = 0;
            $errors = array();
            $this->old = clone $this;
            $this->fields = $fieldsValue;

			$cq = $this->db->type == 'pgsql' ? '"' : '`';

			// Update line of dictionary table
            $sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->dictionary->table_name . ' SET ';
            $set_statement = array();
            foreach ($this->fields as $name => $value) {
                if (($formattedValue = $this->formatFieldValueForSQL($name, $value)) !== null) {
                    $set_statement[] = $cq . $name . $cq . ' = ' . $formattedValue;
                }
            }
            $sql .= implode(', ', $set_statement);
            $sql .= ' WHERE ' . $cq . $this->dictionary->rowid_field . $cq . ' = ' . $this->id;

            dol_syslog(__METHOD__, LOG_DEBUG);
            if (!empty($set_statement)) {
				$resql = $this->db->query($sql);
				if (!$resql) {
					dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
					$error++;
					$errors[] = $this->db->lasterror();
				}
			}

            // Update post line of dictionary table
            if (!$error) {
                foreach ($this->fields as $fieldName => $value) {
                    $field = $this->dictionary->fields[$fieldName];
                    switch ($field['type']) {
                        case 'chkbxlst':
                        case 'chkbxlstwithorder':
                            // Delete association line for the multi-select list
                            $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' WHERE ' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ' = ' . $this->id;
                            $resql = $this->db->query($sql);
                            if (!$resql) {
                                dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
                                $error++;
                                $errors[] = $this->db->lasterror();
                            } elseif(!empty($value)) {
                                // Insert association line for the multi-select list
                                $insert_values = array();
                                $value_arr = array();
                                if (is_array($value)) {
                                    $value_arr = $value;
                                } elseif (!empty($value)) {
                                    $value_arr = array_filter(array_map('trim', explode(',', (string)$value)), 'strlen');
                                }
                                foreach ($value_arr as $value_id) {
                                    $insert_values[] = '(' . $this->id . ', ' . $value_id . ')';
                                }
                                if (count($insert_values) > 0) {
                                    $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . '(' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ', ' . $cq . $this->getDestinationColumnAssociationTableName($field) . $cq . ') VALUES' . implode(',', $insert_values);
                                    $resql = $this->db->query($sql);
                                    if (!$resql) {
                                        dol_syslog(__METHOD__ . ' SQL: ' . $sql . '; Errors: ' . $this->db->lasterror(), LOG_ERR);
                                        $error++;
                                        $errors[] = $this->db->lasterror();
                                    }
                                }
                            }
                            break;
                        case 'custom':
                            $res = $this->updateCustomFieldLine($field, $this->id, $value, $user);
                            if ($res < 0) {
                                $error++;
                            }
                            break;
                        default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                            break;
                    }
                    if ($error) break;
                }
            }

            if (!$error && !$noTrigger) {
                // Call trigger
                $result = $this->call_trigger('LINEDICTIONARY_UPDATE', $user);
                if ($result < 0) $error++;
                // End call triggers
            }

			if (!$error) {
				$result = $this->updateLineSuccess($user);
				if ($result < 0) $error++;
			}

            if (!$error) {
                $this->db->commit();
                return 1;
            } else {
                foreach ($errors as $errmsg) {
                    $this->errors[] = ($this->errors ? ', ' : '') . $errmsg;
                }

                $this->fields = $this->old->fields;
                $this->db->rollback();
                return -1;
            }
        } else {
            return -1;
        }
    }

    /**
     *  Update line for the custom field
     *
     * @param   string	$fieldName      Name of the field
     * @param   int     $lineId         Id of the line
     * @param   mixed   $value          Value of the field
     * @param   User    $user           User who add this line
     * @return  int                     <0 if not ok, >0 if ok
     */
    protected function updateCustomFieldLine($fieldName, $lineId, $value, $user)
    {
        return 1;
    }

    /**
     *  Delete line
     *
     * @param   User    $user           User who add this line
     * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
     * @return  int                     <0 if not ok, >0 if ok
     */
	public function delete($user, $noTrigger = 0)
    {
        global $langs;
        dol_syslog(__METHOD__ . " lineId: " . $this->id);

        $langs->load('advancedictionaries@advancedictionaries');
        $this->db->begin();
        $error = 0;
        $errors = array();

		$cq = $this->db->type == 'pgsql' ? '"' : '`';

        if (!$noTrigger) {
            // Call trigger
            $result = $this->call_trigger('LINEDICTIONARY_DELETE', $user);
            if ($result < 0) $error++;
            // End call triggers
        }

        foreach ($this->dictionary->fields as $fieldName => $field) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    // Delete association line for the multi-select list
                    $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' WHERE ' . $cq . $this->getCurrentColumnAssociationTableName($field) . $cq . ' = ' . $this->id;
                    $resql = $this->db->query($sql);
                    if (!$resql) {
                        $error++;
                        if ($this->db->lasterrno() == 'DB_ERROR_CHILD_EXISTS') {
                            $errors[] = $langs->trans('AdvanceDictionariesErrorValueUsedInDolibarr', $langs->transnoentitiesnoconv($field['label'])) . ' : ' . $this->db->lasterror();
                        } else {
                            $errors[] = $this->db->lasterror();
                        }
                    }
                    break;
                case 'custom':
                    $res = $this->deleteCustomFieldLine($field, $this->id, $user);
                    if ($res < 0) {
                        $error++;
                    }
                    break;
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    break;
            }
            if ($error) break;
        }

        if (!$error) {
			$cq = $this->db->type == 'pgsql' ? '"' : '`';

			// Delete line of dictionary table
            $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->dictionary->table_name . ' WHERE ' . $cq . $this->dictionary->rowid_field . $cq . ' = ' . $this->id;

            dol_syslog(__METHOD__, LOG_DEBUG);
            $resql = $this->db->query($sql);
            if (!$resql) {
                $error++;
                if ($this->db->lasterrno() == 'DB_ERROR_CHILD_EXISTS') {
                    $errors[] = $langs->trans('AdvanceDictionariesErrorUsedInDolibarr') . ' : ' . $this->db->lasterror();
                } else {
                    $errors[] = $this->db->lasterror();
                }
            }
        }

		if (!$error) {
			$result = $this->deleteLineSuccess($user);
			if ($result < 0) $error++;
		}

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            foreach ($errors as $errmsg) {
                dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
                $this->errors[] = ($this->errors ? ', ' : '') . $errmsg;
            }

            $this->db->rollback();
            return -1;
        }
    }

    /**
     *  Delete line for the custom field
     *
     * @param   string	$fieldName      Name of the field
     * @param   int     $lineId         Id of the line
     * @param   User    $user           User who add this line
     * @return  int                     <0 if not ok, >0 if ok
     */
    protected function deleteCustomFieldLine($fieldName, $lineId, $user)
    {
        return 1;
    }

    /**
    *  Active line
    *
    * @param   int     $status         Status of the line, 0: desactived, 1: actived
    * @param   User    $user           User who add this line
    * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
    * @return  int                     <0 if not ok, >0 if ok
    */
	public function active($status, $user, $noTrigger = 0)
    {
        dol_syslog(__METHOD__ . " lineId: " . $this->id . "; status: " . $status);

        $this->db->begin();
        $error = 0;
        $errors = array();
        $this->old = clone $this;
        $this->active = $status;

		$cq = $this->db->type == 'pgsql' ? '"' : '`';

		// Update line of dictionary table
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->dictionary->table_name .
            ' SET ' . $cq . $this->dictionary->active_field . $cq . ' = ' . $this->active .
            ' WHERE ' . $cq . $this->dictionary->rowid_field . $cq . ' = ' . $this->id;

        dol_syslog(__METHOD__, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $errors[] = $this->db->lasterror();
        }

        if (!$error && !$noTrigger) {
            // Call trigger
            $result = $this->call_trigger('LINEDICTIONARY_ACTIVE', $user);
            if ($result < 0) $error++;
            // End call triggers
        }

		if (!$error) {
			$result = $this->activeLineSuccess($user);
			if ($result < 0) $error++;
		}

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            foreach ($errors as $errmsg) {
                dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
                $this->errors[] = ($this->errors ? ', ' : '') . $errmsg;
            }

            $this->active = $this->old->active;
            $this->db->rollback();
            return -1;
        }
    }

	/**
	 *  Set entities
	 *
	 * @param   User    $user           User who add this line
	 * @param   int  	$entity       	Entity to update
	 * @param   int     $noTrigger      1 = Does not execute triggers, 0 = execute triggers
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	public function setEntity($user, $entity, $noTrigger = 0)
	{
		dol_syslog(__METHOD__ . " lineId: " . $this->id . "; entity: " . $entity);

		$this->db->begin();
		$error = 0;
		$errors = array();
		$this->old = clone $this;
		$this->entity = $entity;

		$cq = $this->db->type == 'pgsql' ? '"' : '`';

		// Update line of dictionary table
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->dictionary->table_name .
			' SET ' . $cq . $this->dictionary->entity_field . $cq . ' = ' . $this->entity .
			' WHERE ' . $cq . $this->dictionary->rowid_field . $cq . ' = ' . $this->id;

		dol_syslog(__METHOD__, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$errors[] = $this->db->lasterror();
		}

		if (!$error && !$noTrigger) {
			// Call trigger
			$result = $this->call_trigger('LINEDICTIONARY_ENTITY', $user);
			if ($result < 0) $error++;
			// End call triggers
		}

		if (!$error) {
			$result = $this->entityLineSuccess($user);
			if ($result < 0) $error++;
		}

		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			foreach ($errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->errors[] = ($this->errors ? ', ' : '') . $errmsg;
			}

			$this->entity = $this->old->entity;
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Execute this function if the insertion of the line in the dictionary is successful
	 *
	 * @param   User    $user           User who make this action
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function insertLineSuccess(User $user)
	{
		return 1;
	}

	/**
	 * Execute this function if the modification of the line in the dictionary is successful
	 *
	 * @param   User    $user           User who make this action
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function updateLineSuccess(User $user)
	{
		return 1;
	}

	/**
	 * Execute this function if the deletion of the line in the dictionary is successful
	 *
	 * @param   User    $user           User who make this action
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function deleteLineSuccess(User $user)
	{
		return 1;
	}

	/**
	 * Execute this function if the modification of the active status of the line in the dictionary is successful
	 *
	 * @param   User    $user           User who make this action
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function activeLineSuccess(User $user)
	{
		return 1;
	}

	/**
	 * Execute this function if the modification of the entity of the line in the dictionary is successful
	 *
	 * @param   User    $user           User who make this action
	 * @return  int                     <0 if not ok, >0 if ok
	 */
	protected function entityLineSuccess(User $user)
	{
		return 1;
	}

    /**
     *  Load a line of the dictionary
     *
     * @param   int     $rowid      id of line to load
     * @return  int                 >0 if OK, <0 if KO
     */
	public function fetch($rowid)
    {
    	global $conf;
		$select = array();
		$group_by = array();
        $from = "";
        foreach ($this->dictionary->fields as $field) {
            // Select clause
            $sqlStatement = $this->selectFieldSqlStatement($field);
            if (!empty($sqlStatement)) {
                $select[] = $sqlStatement . (!empty($sqlStatement) ? ' AS ' . $field['name'] : '');
				if ($field['type'] != 'chkbxlst' && $field['type'] != 'chkbxlstwithorder') $group_by[] = $sqlStatement;
            }
            // from clause
            $from .= $this->fromFieldSqlStatement($field);
        }

		$filter_entity = array();
		if (!in_array(0, $filter_entity)) $filter_entity[] = 0;
		if (!in_array(1, $filter_entity)) $filter_entity[] = 1;
		if (!in_array($conf->entity, $filter_entity)) $filter_entity[] = $conf->entity;

        $sql = 'SELECT d.' . $this->dictionary->rowid_field . ', ' . implode(', ', $select) .
            ', d.' . $this->dictionary->active_field . ($this->dictionary->has_entity ? ', d.' . $this->dictionary->entity_field : '') .
            ' FROM ' . MAIN_DB_PREFIX . $this->dictionary->table_name . ' as d ' . $from .
            ' WHERE d.' . $this->dictionary->rowid_field . ' = ' . $rowid .
            ($this->dictionary->is_multi_entity && $this->dictionary->has_entity ? ' AND d.' . $this->dictionary->entity_field . ' IN (' . implode(',', $filter_entity) . ')' : '') .
            ' GROUP BY d.' . $this->dictionary->rowid_field . ', ' . implode(', ', $group_by) .
			', d.' . $this->dictionary->active_field . ($this->dictionary->has_entity ? ', d.' . $this->dictionary->entity_field : '');

        dol_syslog(__METHOD__, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($obj = $this->db->fetch_array($resql)) {
                $this->id = $obj[$this->dictionary->rowid_field];
                $this->active = $obj[$this->dictionary->active_field];
                if ($this->dictionary->has_entity) {
                    $this->entity = $obj[$this->dictionary->entity_field];
                }
                $this->fields = array();
                foreach ($this->dictionary->fields as $k => $v) {
                    $this->fields[$k] = $this->formatFieldValueFromSQL($k, $obj[$k]);
                }

                $this->db->free($resql);

                return 1;
            } else {
                return 0;
            }
        } else {
            $this->errors[] = ($this->errors ? ', ' : '') . $this->db->lasterror();
            return -1;
        }
    }

    /**
   	 * Return the sql statement for the field in the select clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the field in the select clause
   	 */
   	protected function selectFieldSqlStatement($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    return 'GROUP_CONCAT(DISTINCT cbl_' . $field['name'] . '.' . $this->getDestinationColumnAssociationTableName($field) .
						($field['type'] == 'chkbxlstwithorder' ? ' ORDER BY cbl_' . $field['name'] . '.rowid ASC' : '') . ' SEPARATOR \',\')';
                case 'custom':
                    return $this->selectCustomFieldSqlStatement($field);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    return 'd.' . $field['name'];
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the select clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the custom field in the select clause
   	 */
    protected function selectCustomFieldSqlStatement($field) {
   	    return '';
    }

    /**
   	 * Return the sql statement for the field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the field in the from clause
   	 */
   	protected function fromFieldSqlStatement($field)
    {
        if (!empty($field)) {
            switch ($field['type']) {
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    return ' LEFT JOIN ' . MAIN_DB_PREFIX . $this->getAssociationTableName($field) . ' AS cbl_' . $field['name'] .
                        ' ON (cbl_' . $field['name'] . '.' . $this->getCurrentColumnAssociationTableName($field) . ' = d.' . $this->dictionary->rowid_field . ')';
                case 'custom':
                    return $this->fromCustomFieldSqlStatement($field);
                default: // varchar, text, int, float, double, date, datetime, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, link, unknown
                    return '';
            }
        }

        return '';
    }

    /**
   	 * Return the sql statement for the custom field in the from clause
   	 *
     * @param   array       $field      Description of the field
     * @return  string                  Return the sql statement for the custom field in the from clause
   	 */
    protected function fromCustomFieldSqlStatement($field) {
   	    return '';
    }

    /**
     *  Format the value of the field to the table
     *
     * @param   string      $name       Name of the field
     * @param   mixed       $value      Value of the field
     * @return  string                  Value of the custom field formatted for the table
     */
	public function formatFieldValueForSQL($name, $value)
    {
        if (isset($this->dictionary->fields[$name])) {
            $field = $this->dictionary->fields[$name];
            $unselect_values = (isset($field['unselected_values']) && is_array($field['unselected_values'])) ? $field['unselected_values'] : array(-1);

            switch ($field['type']) {
                case 'varchar':
                case 'text':
                case 'phone':
                case 'mail':
                case 'url':
                case 'password':
                case 'radio':
                case 'checkbox':
                case 'int':
                case 'float':
                case 'double':
                case 'price':
                case 'link':
                    return !empty($field['is_require']) || (isset($value) && $value !== '') ? "'" . $this->db->escape($value) . "'" : 'NULL';
                case 'select':
                case 'sellist':
                    return !empty($field['is_require']) || (isset($value) && $value !== '' && !in_array($value, $unselect_values)) ? "'" . $this->db->escape($value) . "'" : 'NULL';
                case 'chkbxlst':
				case 'chkbxlstwithorder':
                    return null;
                case 'date':
                case 'datetime':
                    return !empty($field['is_require']) || (isset($value) && $value !== '') ? "'" . $this->db->idate($value) . "'" : 'NULL';
                case 'boolean':
                    return !empty($field['is_require']) || (isset($value) && $value !== '') ? (!empty($value) ? 1 : 0) : 'NULL';
                case 'custom':
                    return $this->formatCustomFieldValueForSQL($name, $value);
                default: // unknown
                    return '';
            }
        }

        return '';
    }

    /**
     *  Format the value of the custom field to the table
     *
     * @param   string      $name       Name of the field
     * @param   mixed       $value      Value of the field
     * @return  string                  Value of the custom field formatted for the table
     */
    protected function formatCustomFieldValueForSQL($name, $value) {
        return '';
    }

    /**
     *  Format the value of the field from the table (for export ?)
     *
     * @param   string      $name       Name of the field
     * @param   mixed       $value      Value of the field
     * @return  string                  Formatted value of the field from the table
     */
	public function formatFieldValueFromSQL($name, $value)
    {
        if (isset($this->dictionary->fields[$name])) {
            $field = $this->dictionary->fields[$name];

            switch ($field['type']) {
                case 'date':
                case 'datetime':
                    return $this->db->jdate($value);
                case 'boolean':
                    return (!empty($value) ? 1 : 0);
                case 'custom':
                    return $this->formatCustomFieldValueFromSQL($name, $value);
                default: // varchar, text, int, float, double, boolean, price, phone, mail, url, password, select, sellist, radio, checkbox, chkbxlst, chkbxlstwithorder, link, unknown
                    return $value;
            }
        }

        return '';
    }

    /**
     *  Format the value of the custom field from the table
     *
     * @param   string      $name       Name of the field
     * @param   mixed       $value      Value of the field
     * @return  string                  Formatted value of the custom field from the table
     */
    protected function formatCustomFieldValueFromSQL($name, $value) {
        return '';
    }

    /**
     * Return HTML string to put an output field into a page
     *
     * @param   string	$fieldName      Name of the field
     * @param   string	$value          Value to show
     * @return	string					Formatted value
     */
	public function showOutputFieldAD($fieldName, $value = null)
    {
        global $langs, $conf;

        if (isset($this->dictionary->fields[$fieldName])) {
            $field = $this->dictionary->fields[$fieldName];

            // set some default values
            $field['translate_prefix'] = $field['translate_prefix'] ?? '';
            $field['translate_suffix'] = $field['translate_suffix'] ?? '';

            if ($value === null) $value = $this->fields[$fieldName];

            if (isset($field['show_output']['moreAttributes'])) $moreAttributes = trim($field['show_output']['moreAttributes']);
            $moreAttributes = !empty($moreAttributes) ? ' ' . $moreAttributes : '';

            switch ($field['type']) {
                case 'varchar':
                    $value = $langs->trans($field['translate_prefix'] . $value . $field['translate_suffix']);
                    break;
                case 'text':
                    $value = dol_htmlentitiesbr($value);
                    break;
                case 'phone':
                    $value = dol_print_phone($value, '', 0, 0, '', '&nbsp;', 1);
                    break;
                case 'mail':
                    $value = dol_print_email($value, 0, 0, 0, 64, 1, 1);
                    break;
                case 'url':
                    $value = dol_print_url($value, '_blank', 32, 1);
                    break;
                case 'password':
                    $value = preg_replace('/./i', '*', $value);
                    break;
                case 'radio':
                case 'select':
                    $value = $langs->trans($field['translate_prefix'] . $field['options'][$value] . $field['translate_suffix']);
                    break;
                case 'sellist':
                    // 0 : tableName
                    // 1 : label field name
                    // 2 : key fields name (if differ of rowid)
                    // 3 : key field parent (for dependent lists)
                    // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                    // 5 : ObjectName
                    // 6 : classPath
					// 7 : lang
					$InfoFieldList = explode(":", (string)$field['options']);

                    if (empty($InfoFieldList[5]) && empty($InfoFieldList[6])) {
                        $selectkey = "rowid";
                        $keyList = 'rowid';

                        if (count($InfoFieldList) >= 3) {
                            $selectkey = $InfoFieldList[2];
                            $keyList = $InfoFieldList[2] . ' as rowid';
                        }

						$fields_label = !empty($InfoFieldList[1]) ? explode('|', $InfoFieldList[1]) : null;
						$fieldList = array();
						if (is_array($fields_label)) {
							$keyList .= ', ' . implode(', ', $fields_label);
							foreach ($fields_label as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldList[] = $matches[1];
								} else {
									$fieldList[] = $l;
								}
							}
						}

						$fields_lang = !empty($InfoFieldList[7]) ? explode('|', $InfoFieldList[7]) : null;
						$fieldLangList = array();
						if (is_array($fields_lang)) {
							$keyList .= ', ' . implode(', ', $fields_lang);
							foreach ($fields_lang as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldLangList[] = $matches[1];
								} else {
									$fieldLangList[] = $l;
								}
							}
						}

                        $sql = 'SELECT ' . $keyList;
                        $sql .= ' FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]);
                        if (strpos($InfoFieldList[4], 'extra') !== false) {
                            $sql .= ' as main';
                        }
                        if ($selectkey == 'rowid' && empty($value)) {
                            $sql .= " WHERE " . $selectkey . "=0";
                        } elseif ($selectkey == 'rowid') {
                            $sql .= " WHERE " . $selectkey . "=" . $this->db->escape($value);
                        } else {
                            $sql .= " WHERE " . $selectkey . "='" . $this->db->escape($value) . "'";
                        }

                        dol_syslog(__METHOD__ . ' type=sellist', LOG_DEBUG);
                        $resql = $this->db->query($sql);
                        if ($resql) {
                            $value = '';    // value was used, so now we reste it to use it to build final output

                            $obj = $this->db->fetch_object($resql);

							if (!empty($fieldLangList)) {
								foreach ($fieldLangList as $lang) {
									if (!empty($obj->$lang)) $langs->load($obj->$lang);
								}
							}
							if (is_array($fieldList) && count($fieldList) > 1) {
								// Several field into label (eq table:code|libelle:rowid)
								$label_separator = isset($field['label_separator']) ? $field['label_separator'] : ' ';
                                $labelstoshow = array();
                                foreach ($fieldList as $field_toshow) {
                                    $translabel = '';
                                    if (!empty($obj->$field_toshow)) {
                                        $translabel = $langs->trans($field['translate_prefix'] . $obj->$field_toshow . $field['translate_suffix']);
                                    }
                                    if ($translabel != $field_toshow) {
                                        $labelstoshow[] = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
                                    } else {
                                        $labelstoshow[] = $obj->$field_toshow;
                                    }
                                }
                                $value .= implode($label_separator, $labelstoshow);
                            } else {
                                $translabel = '';
                                if (!empty($obj->{$fieldList[0]})) {
                                    $translabel = $langs->trans($field['translate_prefix'] . $obj->{$fieldList[0]} . $field['translate_suffix']);
                                }
                                if ($translabel != $obj->{$fieldList[0]}) {
                                    $value = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
                                } else {
                                    $value = $obj->{$fieldList[0]};
                                }
                            }
                        } else dol_syslog(__METHOD__ . ' Error ' . $this->db->lasterror(), LOG_WARNING);
                    } else {
                        $value = $this->getObjectNomUrl($fieldName, $InfoFieldList[5], $InfoFieldList[6], $value);
                    }
                    break;
                case 'checkbox':
                    if (is_array($value)) {
                        $value_arr = $value;
                    } else {
                        $value_arr = array_filter(explode(',', (string)$value), 'strlen');
                    }
                    $toprint = array();
                    if (is_array($value_arr)) {
                        foreach ($value_arr as $keyval => $valueval) {
                            $toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #aaa">' . $langs->trans($field['translate_prefix'] . $field['options'][$valueval] . $field['translate_suffix']) . '</li>';
                        }
                    }
                    $value = '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">' . implode(' ', $toprint) . '</ul></div>';
                    break;
                case 'chkbxlst':
                case 'chkbxlstwithorder':
                    if (is_array($value)) {
                        $value_arr = $value;
                    } else {
                        if ($value === NULL) {
                            $value_arr = array('NULL');
                        } else {
                            $value_arr = array_filter(explode(',', (string)$value), 'strlen');
                        }
                    }

                    // 0 : tableName
                    // 1 : label field name
                    // 2 : key fields name (if differ of rowid)
                    // 3 : key field parent (for dependent lists)
                    // 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
                    // 5 : ObjectName
					// 6 : classPath
					// 7 : lang
                    $InfoFieldList = explode(":", (string)$field['options']);

                    if (empty($InfoFieldList[5]) && empty($InfoFieldList[6])) {
                        $selectkey = "rowid";
                        $keyList = 'rowid';

                        if (count($InfoFieldList) >= 3) {
                            $selectkey = $InfoFieldList[2];
                            $keyList = $InfoFieldList[2] . ' as rowid';
                        }

						$fields_label = !empty($InfoFieldList[1]) ? explode('|', $InfoFieldList[1]) : null;
						$fieldList = array();
						if (is_array($fields_label)) {
							$keyList .= ', ' . implode(', ', $fields_label);
							foreach ($fields_label as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldList[] = $matches[1];
								} else {
									$fieldList[] = $l;
								}
							}
						}

						$fields_lang = !empty($InfoFieldList[7]) ? explode('|', $InfoFieldList[7]) : null;
						$fieldLangList = array();
						if (is_array($fields_lang)) {
							$keyList .= ', ' . implode(', ', $fields_lang);
							foreach ($fields_lang as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldLangList[] = $matches[1];
								} else {
									$fieldLangList[] = $l;
								}
							}
						}

                        $sql = 'SELECT ' . $keyList;
                        $sql .= ' FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]);
                        if (strpos($InfoFieldList[4], 'extra') !== false) {
                            $sql .= ' as main';
                        }
                        $sql .= " WHERE " . $selectkey . " IN (" . implode(',', $value_arr) . ")";

						$value = ''; // value was used, so now we reste it to use it to build final output
                        dol_syslog(__METHOD__ . ' type=' . $field['type'], LOG_DEBUG);
                        $resql = $this->db->query($sql);
                        if ($resql) {
                            $toprint = array();
							// Get all infos of selected values
							$elementToDisplay = array();
							while ($obj = $this->db->fetch_object($resql)) {
								$elementToDisplay[$obj->rowid] = $obj;
							}
							if (is_array($value_arr)) {
								// Show in selected values order
								foreach($value_arr as $id){
									if(!isset($elementToDisplay[$id])) continue;

									$obj = $elementToDisplay[$id];
									if (!empty($fieldLangList)) {
										foreach ($fieldLangList as $lang) {
											if (!empty($obj->$lang)) $langs->load($obj->$lang);
										}
									}
                                    if (is_array($fieldList) && count($fieldList) > 1) {
										// Several field into label (eq table:code|libelle:rowid)
                                        $label_separator = isset($field['label_separator']) ? $field['label_separator'] : ' ';
                                        $labelstoshow = array();
                                        foreach ($fieldList as $field_toshow) {
                                            $translabel = $langs->trans($field['translate_prefix']. $obj->$field_toshow . $field['translate_suffix']);
                                            if ($translabel != $obj->$field_toshow) {
                                                $labelstoshow[] = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
                                            } else {
                                                $labelstoshow[] = dol_trunc($obj->$field_toshow, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
                                            }
                                        }
                                        $toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #aaa">' . implode($label_separator, $labelstoshow) . '</li>';
                                    } else {
                                        $translabel = '';
                                        if (!empty($obj->{$fieldList[0]})) {
                                            $translabel = $langs->trans($field['translate_prefix'] . $obj->{$fieldList[0]} . $field['translate_suffix']);
                                        }
                                        if ($translabel != $obj->{$fieldList[0]}) {
                                            $toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #aaa">' . dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0) . '</li>';
                                        } else {
                                            $toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #aaa">' . $obj->{$fieldList[0]} . '</li>';
                                        }
                                    }
                                }
                            }
                            $value = '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">' . implode(' ', $toprint) . '</ul></div>';
                        } else {
                            dol_syslog(__METHOD__ . ' Error ' . $this->db->lasterror(), LOG_WARNING);
                        }
                    } else {
                        $toprint = array();
                        foreach ($value_arr as $val) {
                            if ($val === 'NULL') continue;
                            $toprint[] = $this->getObjectNomUrl($fieldName, $InfoFieldList[5], $InfoFieldList[6], $val);
                        }
                        $value = implode(', ', $toprint);
                    }
                    break;
                case 'int':
                    break;
                case 'float':
                case 'double':
                    if (!empty($value)) $value = price($value);
                    break;
                case 'price':
                    $value = price($value, 0, $langs, 0, 0, -1, $conf->currency);
                    break;
                case 'link':
                    // only if something to display (perf)
                    if ($value) {
                        $out = '';
                        // 0 : ObjectName
                        // 1 : classPath
                        $InfoFieldList = explode(":", (string)$field['options']);
                        $value = $this->getObjectNomUrl($fieldName, $InfoFieldList[0], $InfoFieldList[1], $value);
                    }
                    break;
                case 'date':
                    $value = dol_print_date($value, 'day');
                    break;
                case 'datetime':
                    $value = dol_print_date($value, 'dayhour');
                    break;
                case 'boolean':
                    $value = '<input type="checkbox" ' . (!empty($value) ? ' checked ' : '') . $moreAttributes . ' readonly disabled>';
                    break;
                case 'custom':
                    return $this->showOutputCustomFieldAD($fieldName, $value);
                default: // unknown
                    return '';
            }

            return $value;
        }

        return '';
    }

    /**
     * Return HTML string to put an output custom field into a page
     *
     * @param   string	$fieldName      Name of the field
     * @param   string	$value          Value to show
     * @return	string					Formatted value
     */
    protected function showOutputCustomFieldAD($fieldName, $value)
    {
        return '';
    }

    /**
   	 * Return HTML string to put an input field into a page
   	 *
   	 * @param  string  $fieldName      		Name of the field
   	 * @param  string  $value          		Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
   	 * @param  string  $keyprefix     	 	Prefix string to add into name and id of field (can be used to avoid duplicate names)
   	 * @param  string  $keysuffix      		Suffix string to add into name and id of field (can be used to avoid duplicate names)
   	 * @param  int     $objectid       		Current object id
   	 * @param  int     $options_only   		1: Return only the html output of the options of the select input
	 * @return string
   	 */
	public function showInputFieldAD($fieldName, $value=null, $keyprefix='', $keysuffix='', $objectid=0, $options_only=0)
    {
        global $conf, $langs;

        if (isset($this->dictionary->fields[$fieldName])) {
            $field = $this->dictionary->fields[$fieldName];

            // set some default values
            $field['translate_prefix'] = $field['translate_prefix'] ?? '';
            $field['translate_suffix'] = $field['translate_suffix'] ?? '';

            if ($value === null) $value = $this->fields[$fieldName] ?? '';

            $type = $field['type'];
            $size = empty($field['database']['length']) ? '' : $field['database']['length'];
            $required = !empty($field['is_require']);

            $fieldHtmlName = $keyprefix . $fieldName . $keysuffix;

            $moreClasses = empty($field['show_input']['moreClasses']) ? '' : trim($field['show_input']['moreClasses']);
            if (empty($moreClasses)) {
                if ($type == 'date') {
                    $moreClasses = ' minwidth100imp';
                } elseif ($type == 'datetime') {
                    $moreClasses = ' minwidth200imp';
                } elseif (in_array($type, array('int', 'float', 'double', 'price'))) {
                    $moreClasses = ' maxwidth75';
                } elseif (in_array($type, array('varchar', 'phone', 'mail', 'url', 'password', 'link', 'select', 'sellist'))) {
                    $moreClasses = ' minwidth200';
                } elseif (in_array($type, array('boolean', 'radio', 'checkbox', 'chkbxlst', 'chkbxlstwithorder'))) {
                    $moreClasses = '';
                } else {
                    $moreClasses = ' minwidth100';
                }
            } else {
                $moreClasses = ' ' . $moreClasses;
            }

            $moreAttributes = empty($field['show_input']['moreAttributes']) ? '' : trim($field['show_input']['moreAttributes']);
	        if (empty($moreAttributes)) {
		        if (in_array($type, array('checkbox', 'chkbxlst', 'chkbxlstwithorder'))) {
			        $moreAttributes = ' style="width:100%;"';
		        }
	        } else {
		        $moreAttributes = ' ' . $moreAttributes;
	        }
            $moreAttributes = !empty($moreAttributes) ? ' ' . $moreAttributes : '';

            if (!empty($hidden)) {
                $out = '<input type="hidden" value="' . $value . '" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '"/>';
            } else {
				switch ($field['type']) {
					case 'varchar':
						$out = '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" maxlength="' . $size . '" value="' . dol_escape_htmltag($value) . '"' . $moreAttributes . '>';
						break;
					case 'text':
						if (empty($field['no_wysiwyg'])) {
							require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
							$doleditor = new DolEditor($fieldHtmlName, $value, '', 200, 'dolibarr_notes', 'In', false,
								true, !empty($conf->fckeditor->enabled), ROWS_5, '90%');
							$out = $doleditor->Create(1);
						} else {
							$out = '<textarea id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" rows="' . ROWS_5 . '" style="margin-top: 5px; width: 90%;" class="flat">' . $value . '</textarea>';
						}
						break;
					case 'phone':
					case 'mail':
					case 'url':
						$out = '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="' . $value . '"' . $moreAttributes . '>';
						break;
					case 'password':
						$out = '<input type="password" class="flat' . $moreClasses . '" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="' . $value . '"' . $moreAttributes . '>';
						break;
					case 'select':
						$out = '';
						if (!empty($conf->use_javascript_ajax) && !empty($conf->global->MAIN_DICTIONARY_USE_SELECT2)) {
							include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
							$out .= ajax_combobox($fieldHtmlName, array(), 0);
						}

						if (empty($options_only)) $out .= '<select class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '"' . $moreAttributes . '>';
						$out .= '<option value="">&nbsp;</option>';
						if (is_array($field['options'])) {
							foreach ($field['options'] as $key => $val) {
								if ((string)$key == '') continue;
                                if(substr_count($val, '|')) list($val, $parent) = explode('|', $val);
								$out .= '<option value="' . $key . '"';
								$out .= (((string)$value == (string)$key) ? ' selected' : '');
								$out .= (!empty($parent) ? ' parent="' . $parent . '"' : '');
                                $field['translate_prefix'] = $field['translate_prefix'] ?? '';
                                $field['translate_suffix'] = $field['translate_suffix'] ?? '';
								$out .= '>' . $langs->trans($field['translate_prefix'] . $val . $field['translate_suffix']) . '</option>';
							}
						}
						if (empty($options_only)) $out .= '</select>';
						break;
					case 'sellist':
						$out = '';
						if (!empty($conf->use_javascript_ajax) && !empty($conf->global->MAIN_DICTIONARY_USE_SELECT2)) {
							include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
							$out .= ajax_combobox($fieldHtmlName, array(), 0);
						}

						if (empty($options_only)) $out .= '<select class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '"' . $moreAttributes . '>';
						$InfoFieldList = explode(":", (string)$field['options']);
						// 0 : tableName
						// 1 : label field name
						// 2 : key fields name (if differ of rowid)
						// 3 : key field parent (for dependent lists)
						// 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
						// 7 : lang
						$keyList = (empty($InfoFieldList[2]) ? 'rowid' : $InfoFieldList[2] . ' as rowid');

						if (count($InfoFieldList) > 4 && !empty($InfoFieldList[4])) {
							if (strpos($InfoFieldList[4], 'extra.') !== false) {
								$keyList = 'main.' . $InfoFieldList[2] . ' as rowid';
							} else {
								$keyList = $InfoFieldList[2] . ' as rowid';
							}
						}
						$parentName = "";
						if (count($InfoFieldList) > 3 && !empty($InfoFieldList[3])) {
							list($parentName, $parentField) = explode('|', $InfoFieldList[3]);
							$keyList .= ', ' . $parentField;
						}

						$fields_label = !empty($InfoFieldList[1]) ? explode('|', $InfoFieldList[1]) : null;
						$fieldList = array();
						if (is_array($fields_label)) {
							$keyList .= ', ' . implode(', ', $fields_label);
							foreach ($fields_label as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldList[] = $matches[1];
								} else {
									$fieldList[] = $l;
								}
							}
						}

						$fields_lang = !empty($InfoFieldList[7]) ? explode('|', $InfoFieldList[7]) : null;
						$fieldLangList = array();
						if (is_array($fields_lang)) {
							$keyList .= ', ' . implode(', ', $fields_lang);
							foreach ($fields_lang as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldLangList[] = $matches[1];
								} else {
									$fieldLangList[] = $l;
								}
							}
						}

						$sql = 'SELECT ' . $keyList;
						$sql .= ' FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]);
						$sqlwhere = array();
						if (!empty($InfoFieldList[4])) {
							// can use SELECT request
							if (strpos($InfoFieldList[4], '$SEL$') !== false) {
								$InfoFieldList[4] = str_replace('$SEL$', 'SELECT', $InfoFieldList[4]);
							}

							// current object id can be use into filter
							if (strpos($InfoFieldList[4], '$ID$') !== false && !empty($objectid)) {
								$InfoFieldList[4] = str_replace('$ID$', $objectid, $InfoFieldList[4]);
							} else {
								$InfoFieldList[4] = str_replace('$ID$', '0', $InfoFieldList[4]);
							}
							//We have to join on extrafield table
							if (strpos($InfoFieldList[4], 'extra') !== false) {
								$sql .= ' as main, ' . MAIN_DB_PREFIX . $InfoFieldList[0] . ' as extra';
								$sqlwhere[] = 'extra.fk_object=main.' . $InfoFieldList[2] . ' AND ' . $InfoFieldList[4];
							} else {
								$sqlwhere[] = $InfoFieldList[4];
							}
						}
						// Some tables may have field, some other not. For the moment we disable it.
						if (in_array($InfoFieldList[0], array('tablewithentity'))) {
							$sqlwhere[] = 'entity = ' . $conf->entity;
						}
						if (!empty($sqlwhere)) $sql .= ' WHERE ' . implode('AND', $sqlwhere);
						$sql .= ' ORDER BY ' . implode(', ', $fieldList);

						dol_syslog(get_class($this) . ' type=sellist', LOG_DEBUG);
						$resql = $this->db->query($sql);
						if ($resql) {
							$out .= '<option value="">&nbsp;</option>';
							$num = $this->db->num_rows($resql);
							$i = 0;
							while ($i < $num) {
								$obj = $this->db->fetch_object($resql);

								if (!empty($fieldLangList)) {
									foreach ($fieldLangList as $lang) {
										if (!empty($obj->$lang)) $langs->load($obj->$lang);
									}
								}
								$label_separator = isset($field['label_separator']) ? $field['label_separator'] : ' ';
								if (is_array($fieldList) && count($fieldList) > 1) {
									// Several field into label (eq table:code|libelle:rowid)
									$labelstoshow = array();
									foreach ($fieldList as $field_toshow) {
										$translabel = $langs->trans($field['translate_prefix'] . $obj->$field_toshow . $field['translate_suffix']);
										if ($translabel != $obj->$field_toshow) {
											$labelstoshow[] = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
										} else {
											$labelstoshow[] = dol_trunc($obj->$field_toshow, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
										}
									}
									$labeltoshow = implode($label_separator, $labelstoshow);
								} else {
									$translabel = $langs->trans($field['translate_prefix'] . $obj->{$fieldList[0]} . $field['translate_suffix']);
									if ($translabel != $obj->{$fieldList[0]}) {
										$labeltoshow = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
									} else {
										$labeltoshow = dol_trunc($obj->{$fieldList[0]}, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
									}
								}
								if (empty($labeltoshow)) $labeltoshow = '(not defined)';

								if (!empty($InfoFieldList[3])) {
									$parent = $parentName . ':' . $obj->{$parentField};
								}

								$out .= '<option value="' . $obj->rowid . '"';
								$out .= ($value == $obj->rowid ? ' selected' : '');
								$out .= (!empty($parent) ? ' parent="' . $parent . '"' : '');
								$out .= '>' . $labeltoshow . '</option>';

								$i++;
							}
							$this->db->free($resql);
						} else {
							print 'Error in request ' . $sql . ' ' . $this->db->lasterror() . '. Check setup of extra parameters.<br>';
						}
						if (empty($options_only)) $out .= '</select>';
						break;
					case 'radio':
						$out = '';
						if (is_array($field['options'])) {
							foreach ($field['options'] as $keyopt => $val) {
								$out .= '<input class="flat' . $moreClasses . '" type="radio" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '"' . $moreAttributes;
								$out .= ' value="' . $keyopt . '"';
								$out .= ' id="' . $fieldHtmlName . '_' . $keyopt . '"';
								$out .= ($value == $keyopt ? 'checked' : '');
								$out .= '/><label for="' . $fieldHtmlName . '_' . $keyopt . '">' . $langs->trans($field['translate_prefix'] . $val . $field['translate_suffix']) . '</label><br>';
							}
						}
						break;
					case 'checkbox':
						require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
						global $form;
						if (!is_object($form)) $form = new Form($this->db);

						if (is_array($value)) {
							$value_arr = $value;
						} else {
							$value_arr = array_filter(explode(',', (string)$value), 'strlen');
						}
						$options = array();
						if (is_array($field['options'])) {
							foreach ($field['options'] as $option_id => $option) {
								$options[$option_id] = $langs->trans($field['translate_prefix'] . $option . $field['translate_suffix']);
							}
						}
						if (empty($options_only)) {
							$out = $form->multiselectarray($fieldHtmlName, (empty($options) ? null : $options), $value_arr, '', 0, $moreClasses, 0, '', $moreAttributes);
						} else {
							$out = '';
							if (is_array($options) && !empty($options)) {
								foreach ($options as $key => $value) {
									$out .= '<option value="' . $key . '"';
									if (is_array($value_arr) && !empty($value_arr) && in_array((string)$key, $value_arr) && ((string)$key != '')) {
										$out .= ' selected';
									}
									$out .= '>';
									$out .= dol_htmlentitiesbr($value);
									$out .= '</option>' . "\n";
								}
							}
						}
						break;
					case 'chkbxlst':
                    case 'chkbxlstwithorder':
						if (is_array($value)) {
							$value_arr = $value;
						} else {
							$value_arr = array_filter(explode(',', (string)$value), 'strlen');
						}

						$InfoFieldList = explode(":", (string)$field['options']);
						// 0 : tableName
						// 1 : label field name
						// 2 : key fields name (if differ of rowid)
						// 3 : key field parent (for dependent lists)
						// 4 : where clause filter on column or table extrafield, syntax field='value' or extra.field=value
						// 7 : lang
						$keyList = (empty($InfoFieldList[2]) ? 'rowid' : $InfoFieldList[2] . ' as rowid');

						if (count($InfoFieldList) > 3 && !empty($InfoFieldList[3])) {
							list ($parentName, $parentField) = explode('|', $InfoFieldList[3]);
							$keyList .= ', ' . $parentField;
						}
						if (count($InfoFieldList) > 4 && !empty($InfoFieldList[4])) {
							if (strpos($InfoFieldList[4], 'extra.') !== false) {
								$keyList = 'main.' . $InfoFieldList[2] . ' as rowid';
							} else {
								$keyList = $InfoFieldList[2] . ' as rowid';
							}
						}

						$fields_label = !empty($InfoFieldList[1]) ? explode('|', $InfoFieldList[1]) : null;
						$fieldList = array();
						if (is_array($fields_label)) {
							$keyList .= ', ' . implode(', ', $fields_label);
							foreach ($fields_label as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldList[] = $matches[1];
								} else {
									$fieldList[] = $l;
								}
							}
						}

						$fields_lang = !empty($InfoFieldList[7]) ? explode('|', $InfoFieldList[7]) : null;
						$fieldLangList = array();
						if (is_array($fields_lang)) {
							$keyList .= ', ' . implode(', ', $fields_lang);
							foreach ($fields_lang as $l) {
								if (preg_match('/\s+AS\s+(\S+)\s*$/i', $l, $matches)) {
									$fieldLangList[] = $matches[1];
								} else {
									$fieldLangList[] = $l;
								}
							}
						}

						$sqlwhere = array();
						$sql = 'SELECT ' . $keyList;
						$sql .= ' FROM ' . MAIN_DB_PREFIX . str_replace('{{DB_PREFIX}}', MAIN_DB_PREFIX, $InfoFieldList[0]);
						if (!empty($InfoFieldList[4])) {

							// can use SELECT request
							if (strpos($InfoFieldList[4], '$SEL$') !== false) {
								$InfoFieldList[4] = str_replace('$SEL$', 'SELECT', $InfoFieldList[4]);
							}

							// current object id can be use into filter
							if (strpos($InfoFieldList[4], '$ID$') !== false && !empty($objectid)) {
								$InfoFieldList[4] = str_replace('$ID$', $objectid, $InfoFieldList[4]);
							} else {
								$InfoFieldList[4] = str_replace('$ID$', '0', $InfoFieldList[4]);
							}

							// We have to join on extrafield table
							if (strpos($InfoFieldList[4], 'extra') !== false) {
								$sql .= ' as main, ' . MAIN_DB_PREFIX . $InfoFieldList[0] . '_extrafields as extra';
								$sqlwhere[] = 'extra.fk_object=main.' . $InfoFieldList[2] . ' AND ' . $InfoFieldList[4];
							} else {
								$sqlwhere[] = $InfoFieldList[4];
							}
						}
						// Some tables may have field, some other not. For the moment we disable it.
						if (in_array($InfoFieldList[0], array('tablewithentity'))) {
							$sqlwhere[] = 'entity = ' . $conf->entity;
						}
						if (!empty($sqlwhere)) $sql .= ' WHERE ' . implode('AND', $sqlwhere);
						$sql .= ' ORDER BY ' . implode(', ', $fieldList);

						dol_syslog(get_class($this) . ' type=' . $field['type'], LOG_DEBUG);
						$resql = $this->db->query($sql);
						if ($resql) {
							$num = $this->db->num_rows($resql);
							$i = 0;

							$data = array();

							while ($i < $num) {
								$obj = $this->db->fetch_object($resql);

								if (!empty($fieldLangList)) {
									foreach ($fieldLangList as $lang) {
										if (!empty($obj->$lang)) $langs->load($obj->$lang);
									}
								}
								$label_separator = isset($field['label_separator']) ? $field['label_separator'] : ' ';
								if (is_array($fields_label) && count($fields_label) > 1) {
									// Several field into label (eq table:code|libelle:rowid)
									$labelstoshow = array();
									foreach ($fields_label as $field_toshow) {
										$translabel = $langs->trans($field['translate_prefix'] . $obj->$field_toshow . $field['translate_suffix']);
										if ($translabel != $obj->$field_toshow) {
											$labelstoshow[] = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
										} else {
											$labelstoshow[] = dol_trunc($obj->$field_toshow, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
										}
									}
									$labeltoshow = implode($label_separator, $labelstoshow);
								} else {
									$translabel = $langs->trans($field['translate_prefix'] . $obj->{$InfoFieldList[1]} . $field['translate_suffix']);
									if ($translabel != $obj->{$InfoFieldList[1]}) {
										$labeltoshow = dol_trunc($translabel, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
									} else {
										$labeltoshow = dol_trunc($obj->{$InfoFieldList[1]}, isset($field['truncate']) && $field['truncate'] > 0 ? $field['truncate'] : 0);
									}
								}
								if (empty($labeltoshow)) $labeltoshow = '(not defined)';

								if (!empty($InfoFieldList[3])) {
									$parent = $parentName . ':' . $obj->{$parentField};
								}

								$data[$obj->rowid] = $labeltoshow;

								$i++;
							}
							$this->db->free($resql);

							if (empty($options_only)) {
								if ($field['type'] == 'chkbxlst') {
									require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
									global $form;
									if (!is_object($form)) $form = new Form($this->db);
									$out = $form->multiselectarray($fieldHtmlName, $data, $value_arr, '', 0, $moreClasses, 0, '', $moreAttributes);
								} else { // $field['type'] == 'chkbxlstwithorder'
									dol_include_once('/advancedictionaries/class/html.formdictionary.class.php');
									global $formdictionary;
									if (!is_object($formdictionary)) $formdictionary = new FormDictionary($this->db);
									$out = $formdictionary->multiselectarrayWithOrder($fieldHtmlName, $data, $value_arr, '', 0, $moreClasses, 0, '', $moreAttributes);
									$out .= '<link href="' . dol_buildpath('/advancedictionaries/css/adSelect2Sortable.css.php', 1) . '" rel="stylesheet" type="text/css" />';
									$out .= '<script type="text/javascript" src="' . dol_buildpath('/advancedictionaries/js/ad-select2Sortable.min.js', 1) . '"></script>';
								}
							} else {
								$out = '';
								if (is_array($data) && !empty($data)) {
									foreach ($data as $key => $value) {
										$out .= '<option value="' . $key . '"';
										if (is_array($value_arr) && !empty($value_arr) && in_array((string)$key, $value_arr) && ((string)$key != '')) {
											$out .= ' selected';
										}
										$out .= '>';
										$out .= dol_htmlentitiesbr($value);
										$out .= '</option>' . "\n";
									}
								}
							}
						} else {
							$out = 'Error in request ' . $sql . ' ' . $this->db->lasterror() . '. Check setup of field parameters.';
						}
						break;
					case 'int':
						$tmp = explode(',', $size);
						$newsize = $tmp[0] + 1 + ($tmp[1] ?? 0);
						$out = '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" maxlength="' . $newsize . '" value="' . $value . '"' . $moreAttributes . '>';
						break;
					case 'float':
					case 'double':
						if (!empty($value)) {        // $value in memory is a php numeric, we format it into user number format.
							$value = price($value);
						}
						$out = '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="' . $value . '"' . $moreAttributes . '> ';
						break;
					case 'price':
						if (!empty($value)) {        // $value in memory is a php numeric, we format it into user number format.
							$value = price($value);
						}
						$out = '<input type="text" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="' . $value . '"' . $moreAttributes . '> ' . $langs->getCurrencySymbol($conf->currency);
						break;
					case 'link':
						// 0 : ObjectName
						// 1 : classPath
						$InfoFieldList = explode(":", (string)$field['options']);
						dol_include_once($InfoFieldList[1]);
						if ($InfoFieldList[0] && class_exists($InfoFieldList[0], false)) {
							$valuetoshow = $value;
							if (!empty($value)) {
								$object = new $InfoFieldList[0]($this->db);
								$resfetch = $object->fetch($value);
								if ($resfetch > 0) {
									$valuetoshow = $object->ref;
									if ($object->element == 'societe') $valuetoshow = $object->name;  // Special case for thirdparty because ->ref is not name but id (because name is not unique)
								}
							}
							$out = '<input type="text" class="flat' . $moreClasses . '" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="' . $valuetoshow . '"' . $moreAttributes . '>';
						} else {
							dol_syslog('Error bad setup of extrafield', LOG_WARNING);
							$out = 'Error bad setup of extrafield';
						}
						break;
					case 'date':
					case 'datetime':
						$showtime = $type == 'datetime' ? 1 : 0;

						// Do not show current date when field not required (see select_date() method)
						if (!$required && $value == '') $value = '-1';

						require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
						global $form;
						if (!is_object($form)) $form = new Form($this->db);

						// TODO Must also support $moreparam
						$out = $form->select_date($value, $fieldHtmlName, $showtime, $showtime, $required, '', 1, 1, 1, 0, 1);
						break;
					case 'boolean':
						$out = '<input type="checkbox" class="flat' . $moreClasses . ' maxwidthonsmartphone" id="' . $fieldHtmlName . '" name="' . $fieldHtmlName . '" value="1" ' . (!empty($value) ? 'checked' : '') . $moreAttributes . '>';
						break;
					case 'custom':
						$out = $this->showInputCustomFieldAD($fieldName, $value, $keyprefix, $keysuffix, $objectid);
						break;
					default: // unknown
						$out = '';
						break;
				}
			}

            return $out;
        }

        return '';
    }

	/**
	 * Return HTML string to put an input custom field into a page
	 *
	 * @param  string  $fieldName      Name of the field
	 * @param  string  $value          Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
	 * @param  string  $keyprefix      Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  $keysuffix      Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  int     $objectid       Current object idF
	 * @return string
	 */
	protected function showInputCustomFieldAD($fieldName, $value, $keyprefix='', $keysuffix='', $objectid=0)
	{
		return '';
	}

    /**
   	 * Return label define by the pattern
   	 *
     * @param   string  $label          Label pattern for the label of the line (replace {{FieldName}} by this value)
   	 * @return  string
   	 */
    public function getLabel($label)
    {
        $l = $label;
        foreach ($this->fields as $fieldName => $fieldValue) {
            $l = str_replace('{{' . $fieldName . '}}', $fieldValue, $l);
        }
        $l = str_replace('{{' . $this->dictionary->rowid_field . '}}', $this->id, $l);
        $l = str_replace('{{' . $this->dictionary->active_field . '}}', $this->active, $l);
        $l = str_replace('{{' . $this->dictionary->entity_field . '}}', $this->entity, $l);

        return $l;
    }

    /**
     * Return Nom url of the field value
     *
     * @param   string      $fieldName      Field name
     * @param   string      $className      Class name of the object
     * @param   string      $classPath      Filepath of the class object
     * @param   int         $id             Id of the object
     * @return  string
     */
    public function getObjectNomUrl($fieldName, $className, $classPath, $id)
    {
        $result = dol_include_once($classPath);
        if (!$result || empty($className) || !class_exists($className, false)) {
            dol_syslog('Error bad setup for the field: ' . $fieldName . ' onto the dictionary: ' . $this->dictionary->name, LOG_WARNING);
            return 'Error bad setup for the field';
        }

        if (!isset(self::$objects_cached[$className][$id])) {
            $object = new $className($this->db);
            if (!method_exists($object, 'getNomUrl')) {
                dol_syslog('Error getNomUrl method not found in the class for the field: ' . $fieldName . ' onto the dictionary: ' . $this->dictionary->name, LOG_WARNING);
                return 'Error getNomUrl method not found in the class for the field';
            }

            if (!method_exists($object, 'fetch')) {
                dol_syslog('Error fetch method not found in the class for the field: ' . $fieldName . ' onto the dictionary: ' . $this->dictionary->name, LOG_WARNING);
                return 'Error fetch method not found in the class for the field';
            }

            if ($id > 0) {
				$object->fetch($id);
				self::$objects_cached[$className][$id] = $object->getNomUrl(3);
			} else {
				self::$objects_cached[$className][$id] = "";
			}
        }

        return self::$objects_cached[$className][$id];
    }

    /**
     * Function to get association table for chkbxlst or chkbxlstwithorder field
     * @param   array       $field      Description of the field
     * @return string
     */
    public function getAssociationTableName($field)
    {
        return $this->dictionary->getAssociationTableName($field);
    }

    /**
     * Function to get current object column name for chkbxlst or chkbxlstwithorder relation
     * @param   array       $field      Description of the field
     * @return string
     */
    public function getCurrentColumnAssociationTableName($field)
    {
        return $this->dictionary->getCurrentColumnAssociationTableName($field);
    }

    /**
     * Function to get destination object column name for chkbxlst or chkbxlstwithorder relation
     * @param   array       $field      Description of the field
     * @return string
     */
    public function getDestinationColumnAssociationTableName($field)
    {
        return $this->dictionary->getDestinationColumnAssociationTableName($field);
    }

    /**
     * Set values to default
     */
    // public function setDefaultFieldValues(&$field) {
    //     $strings = array(
    //         'translateprefix',
    //         'translatesuffix',

    //     )

    //     // integers
    // }
}
