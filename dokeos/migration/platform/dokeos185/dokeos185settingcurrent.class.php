<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importsettingcurrent.class.php';
require_once dirname(__FILE__).'/../../../admin/lib/setting.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185SettingCurrent extends Import
{
	private $convert = array
					   (
							'siteName'=> 'site_name', 
							'server_type' => 'server_type', 
							'Institution' => 'institution',
							'InstitutionUrl' => 'institution_url',
							'show_administrator_data' => 'show_administrator_data',
							'administratorName' => 'administrator_firstname',
							'administratorSurname' => 'administrator_surname',
							'emailAdministrator' => 'administrator_email',
							'administratorTelephone' => 'administrator_telephone',
							'allow_lostpassword' => 'allow_password_retrieval',
							'allow_registration' => 'allow_registration'
					   );	

	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * current setting properties
	 */
	 
	const PROPERTY_ID = 'id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_SUBKEY = 'subkey';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_SELECTED_VALUE = 'selected_value';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_SCOPE = 'scope';
	const PROPERTY_SUBKEYTEXT = 'subkeytext';
	
	/**
	 * Alfanumeric identifier of the current setting object.
	 */
	private $code;
	
	/**
	 * Default properties of the current setting object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new current setting object.
	 * @param array $defaultProperties The default properties of the current setting
	 *                                 object. Associative array.
	 */
	function Dokeos185SettingCurrent($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this current setting object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this current setting.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all current setting.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self::PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_SUBKEY,
		self :: PROPERTY_TYPE, self::PROPERTY_CATEGORY, self::PROPERTY_SELECTED_VALUE, self :: PROPERTY_TITLE,
		self :: PROPERTY_COMMENT, self :: PROPERTY_SCOPE, self :: PROPERTY_SUBKEYTEXT);
	}
	
	/**
	 * Sets a default property of this current setting by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default current setting
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this current setting.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the variable of this current setting.
	 * @return String The variable.
	 */
	function get_variable()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE);
	}
	
	/**
	 * Returns the subkey of this current setting.
	 * @return String The subkey.
	 */
	function get_subkey()
	{
		return $this->get_default_property(self :: PROPERTY_SUBKEY);
	}
	
	/**
	 * Returns the type of this current setting.
	 * @return String The type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}
	
	/**
	 * Returns the category of this current setting.
	 * @return String The category.
	 */
	function get_category()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY);
	}
	
	/**
	 * Returns the selected_value of this current setting.
	 * @return String The selected_value.
	 */
	function get_selected_value()
	{
		return $this->get_default_property(self :: PROPERTY_SELECTED_VALUE);
	}
	
	/**
	 * Returns the title of this current setting.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the comment of this current setting.
	 * @return String The comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}
	
	/**
	 * Returns the scope of this current setting.
	 * @return String The scope.
	 */
	function get_scope()
	{
		return $this->get_default_property(self :: PROPERTY_SCOPE);
	}
	
	/**
	 * Returns the subkeytext of this current setting.
	 * @return String The subkeytext.
	 */
	function get_subkey_text()
	{
		return $this->get_default_property(self :: PROPERTY_SUBKEYTEXT);
	}
	
	/**
	 * Sets the id of this current setting.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the variable of this current setting.
	 * @param String $variable The variable.
	 */
	function set_variable($variable)
	{
		$this->set_default_property(self :: PROPERTY_ID, $variable);
	}
	
	/**
	 * Sets the subkey of this current setting.
	 * @param String $subkey The subkey.
	 */
	function set_subkey($subkey)
	{
		$this->set_default_property(self :: PROPERTY_ID, $subkey);
	}
	
	/**
	 * Sets the type of this current setting.
	 * @param String $type The type.
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_ID, $type);
	}
	
	/**
	 * Sets the category of this current setting.
	 * @param String $category The category.
	 */
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_ID, $category);
	}
	
	/**
	 * Sets the selected_value of this current setting.
	 * @param String $selected_value The selected_value.
	 */
	function set_selected_value($selected_value)
	{
		$this->set_default_property(self :: PROPERTY_ID, $selected_value);
	}
	
	/**
	 * Sets the title of this current setting.
	 * @param String $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_ID, $title);
	}
	
	/**
	 * Sets the comment of this current setting.
	 * @param String $comment The comment.
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_ID, $comment);
	}
	
	/**
	 * Sets the scope of this current setting.
	 * @param String $scope The scope.
	 */
	function set_scope($scope)
	{
		$this->set_default_property(self :: PROPERTY_ID, $scope);
	}
	
	/**
	 * Sets the subkeytext of this current setting.
	 * @param String $subkeytext The subkeytext.
	 */
	function set_subkey_text($subkeytext)
	{
		$this->set_default_property(self :: PROPERTY_ID, $subkeytext);
	}
	
	function is_valid_current_setting()
	{
		return isset($this->convert[$this->get_variable()]);
	}
	
	/**
	 * Migration course user relation
	 */
	function convert_to_new_admin_setting()
	{
		//course_rel_user parameters
		
		$value = $this->convert[$this->get_variable()];
		if ($value)
		{
			$lcms_admin_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name($value);
			
			if($this->get_variable() == 'allow_lostpassword')
			{
				if ($this->get_selected_value() == 'true')
					$this->set_selected_value(1);
				else
					$this->set_selected_value(0);
			}
			
			if($this->get_variable() == 'allow_registration')
			{
				if ($this->get_selected_value() == 'true')
					$this->set_selected_value(1);
				else
					$this->set_selected_value(0);
			}
			
			$lcms_admin_setting->set_value($this->get_selected_value());
		
			// Update setting in database
			//$lcms_admin_setting->update();
		
			//return $lcms_admin_setting;
			
			return null;
		}
		
		return null;
	}
	
	/** 
	 * Get all current settings from database
	 * @param Migration Data Manager $mgdm the datamanager from where the settings should be retrieved;
	 */
	static function get_all_current_settings($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_current_settings();	
	}
}
?>
