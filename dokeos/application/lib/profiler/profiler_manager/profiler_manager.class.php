<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/profiler_manager_component.class.php';
require_once dirname(__FILE__).'/profiler_search_form.class.php';
require_once dirname(__FILE__).'/../profiler_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/component/profile_publication_browser/profile_publication_browser_table.class.php';
//require_once dirname(__FILE__).'/../profile_publisher.class.php';
require_once dirname(__FILE__).'/../profiler_menu.class.php';
require_once dirname(__FILE__).'/../profiler_block.class.php';

/**
 * A profiler manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class ProfilerManager extends WebApplication
 {
 	const APPLICATION_NAME = 'profiler';

 	const PARAM_ACTION = 'go';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_MARK_SELECTED_READ = 'mark_selected_read';
	const PARAM_MARK_SELECTED_UNREAD = 'mark_selected_unread';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_PROFILE_ID = 'profile';

	const ACTION_DELETE_PUBLICATION = 'delete';
	const ACTION_EDIT_PUBLICATION = 'edit';
	const ACTION_VIEW_PUBLICATION = 'view';
	const ACTION_CREATE_PUBLICATION = 'create';
	const ACTION_BROWSE_PROFILES = 'browse';
	const ACTION_MANAGE_CATEGORIES = 'manage_categories';

	private $parameters;
	private $search_parameters;
	private $user;
	private $search_form;
	private $breadcrumbs;
	private $firstletter;

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function ProfilerManager($user = null)
    {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();

		if (isset($_GET[ProfilerManager :: PARAM_FIRSTLETTER]))
		{
			$this->firstletter = $_GET[ProfilerManager :: PARAM_FIRSTLETTER];
		}
    }

    /**
	 * Run this profiler manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_PROFILES :
				$component = ProfilerManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_VIEW_PUBLICATION :
				$component = ProfilerManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_DELETE_PUBLICATION :
				$component = ProfilerManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_EDIT_PUBLICATION :
				$component = ProfilerManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_CREATE_PUBLICATION :
				$component = ProfilerManagerComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_MANAGE_CATEGORIES :
				$component = ProfilerManagerComponent :: factory('CategoryManager', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_PROFILES);
				$component = ProfilerManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}
	
    /**
	 * Renders the profiler block and returns it. 
	 */
	function render_block($block)
	{
		$block = ProfilerBlock :: factory($this, $block);
		return $block->run();
	}
	
	/**
	 * Gets the current action.
	 * @see get_parameter()
	 * @return string The current action.
	 */
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}
	/**
	 * Sets the current action.
	 * @param string $action The new action.
	 */
	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}
	/**
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$categories = $this->breadcrumbs;
		if (count($categories) > 0)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: header($breadcrumbtrail);

		//echo $this->get_menu_html();
		echo '<div style="float: right; width: 100%;">';
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}
		echo '<div class="clear">&nbsp;</div>';

		if ($msg = $_GET[self :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
		{
			$this->display_error_message($msg);
		}
	}

	/*
	 * Displays the menu html
	 *
	function get_menu_html()
	{
		$extra_items = array ();
		$create = array ();
		$create['title'] = Translation :: get('Publish');
		$create['url'] = $this->get_profile_creation_url();
		$create['class'] = 'create';
		$extra_items[] = $create;

		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}

		$temp_replacement = '__FIRSTLETTER__';
		$url_format = $this->get_url(array (ProfilerManager :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES, ProfilerManager :: PARAM_FIRSTLETTER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$user_menu = new ProfilerMenu($this->firstletter, $url_format, $extra_items);

		if ($this->get_action() == self :: ACTION_CREATE_PUBLICATION)
		{
			$user_menu->forceCurrentUrl($create['url'], true);
		}
		elseif(!isset($this->firstletter))
		{
			$user_menu->forceCurrentUrl($this->get_profile_home_url(), true);
		}

		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}

		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';

		return implode($html, "\n");
	}*/

	/**
	 * Displays the search form
	 */
	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}

	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: warning_message($message);
	}
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a warning page.
	 * @param string $message The message.
	 */
	function display_warning_page($message)
	{
		$this->display_header();
		$this->display_warning_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a popup form.
	 * @param string $message The message.
	 */
	function display_popup_form($form_html)
	{
		Display :: normal_message($form_html);
	}

	/**
	 * Gets the parameter list
	 * @param boolean $include_search Include the search parameters in the
	 * returned list?
	 * @return array The list of parameters.
	 */
	function get_parameters($include_search = false)
	{
		if ($include_search && isset ($this->search_parameters))
		{
			return array_merge($this->search_parameters, $this->parameters);
		}

		return $this->parameters;
	}
	/**
	 * Gets the value of a parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	/**
	 * Sets the value of a parameter.
	 * @param string $name The parameter name.
	 * @param mixed $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param int $new_category_id The category to show (default = root
	 * category).
	 * @param boolean $error_message Is the passed message an error message?
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return parent :: redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * Sets the active URL in the navigation menu.
	 * @param string $url The active URL.
	 */
	function force_menu_url($url)
	{
		//$this->get_category_menu()->forceCurrentUrl($url);
	}
	/**
	 * Gets an URL.
	 * @param array $additional_parameters Additional parameters to add in the
	 * query string (default = no additional parameters).
	 * @param boolean $include_search Include the search parameters in the
	 * query string of the URL? (default = false).
	 * @param boolean $encode_entities Apply php function htmlentities to the
	 * resulting URL ? (default = false).
	 * @return string The requested URL.
	 */
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	/**
	 * Gets the user id.
	 * @return int The requested user id.
	 */
	function get_user_id()
	{
		return $this->user->get_id();
	}

	/**
	 * Gets the user.
	 * @return int The requested user.
	 */
	function get_user()
	{
		return $this->user;
	}

	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	/**
	 * Wrapper for Display :: not_allowed();.
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}

	/**
	 * Returns a list of actions available to the admin.
	 * @return Array $info Contains all possible actions.
	 */
	public function get_application_platform_admin_links()
	{
		$links = array();
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links, 'search' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES)));
	}

	/**
	 * Return a link to a certain action of this application
	 * @param array $paramaters The parameters to be added to the url
	 * @param boolean $encode Should the url be encoded ?
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'run.php';
		$parameters['application'] = self::APPLICATION_NAME;
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}

	/**
	 * Returns whether a given object id is published in this application
	 * @param int $object_id
	 * @return boolean Is the object is published
	 */
	function learning_object_is_published($object_id)
	{
		return ProfilerDataManager :: get_instance()->learning_object_is_published($object_id);
	}

	/**
	 * Returns whether a given array of objects has been published
	 * @param array $object_ids An array of object id's
	 * @return boolean Was any learning object published
	 */
	function any_learning_object_is_published($object_ids)
	{
		return ProfilerDataManager :: get_instance()->any_learning_object_is_published($object_ids);
	}

	/**
	 * Gets the publication attributes of a given array of learning object id's
	 * @param array $object_id The array of object ids
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return array An array of Learing Object Publication Attributes
	 */
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return ProfilerDataManager :: get_instance()->get_learning_object_publication_attributes($this->get_user(), $object_id, $type, $offset, $count, $order_property, $order_direction);
	}

	/**
	 * Gets the publication attributes of a given learning object id
	 * @param int $object_id The object id
	 * @param string $type Type of retrieval
	 * @param int $offset
	 * @param int $count
	 * @param int $order_property
	 * @param int $order_direction
	 * @return LearningObjectPublicationAttribute
	 */
	function get_learning_object_publication_attribute($object_id)
	{
		return ProfilerDataManager :: get_instance()->get_learning_object_publication_attribute($object_id);
	}

	/**
	 * Counts the publication attributes
	 * @param string $type Type of retrieval
	 * @param Condition $conditions
	 * @return int
	 */
	function count_publication_attributes($type = null, $condition = null)
	{
		return ProfilerDataManager :: get_instance()->count_publication_attributes($this->get_user(), $type, $condition);
	}

	/**
	 * Counts the publication attributes
	 * @param string $type Type of retrieval
	 * @param Condition $conditions
	 * @return boolean
	 */
	function delete_learning_object_publications($object_id)
	{
		return ProfilerDataManager :: get_instance()->delete_profile_publications($object_id);
	}

	/**
	 * Update the publication id
	 * @param LearningObjectPublicationAttribure $publication_attr
	 * @return boolean
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		return ProfilerDataManager :: get_instance()->update_profile_publication_id($publication_attr);
	}

	/**
	 * Count the publications
	 * @param Condition $condition
	 * @return int
	 */
	function count_profile_publications($condition = null)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->count_profile_publications($condition);
	}

	/**
	 * Count the unread publications
	 * @return int
	 */
	function count_unread_profile_publications()
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->count_unread_profile_publications($this->user);
	}

	/**
	 * Retrieve a profile publication
	 * @param int $id
	 * @return PersonalMessagePublication
	 */
	function retrieve_profile_publication($id)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->retrieve_profile_publication($id);
	}

	/**
	 * Retrieve a series of profile publications
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return PersonalMessagePublicationResultSet
	 */
	function retrieve_profile_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$pmdm = ProfilerDataManager :: get_instance();
		return $pmdm->retrieve_profile_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
		
	/**
	 * Inherited
	 */
	function get_learning_object_publication_locations($learning_object)
	{
		$allowed_types = array('profile');
		
		$type = $learning_object->get_type();
		if(in_array($type, $allowed_types))
		{
			$locations = array(__CLASS__);
			return $locations;
		}
		
		return array();	
	}
	
	function publish_learning_object($learning_object, $location)
	{
		$publication = new ProfilePublication();
		$publication->set_profile($learning_object->get_id());
		$publication->set_publisher(Session :: get_user_id());
		$publication->set_published(time());
		$publication->set_category(0);
		$publication->create();
		return Translation :: get('PublicationCreated');
	}
	
	/**
	 * Gets the url for deleting a profile publication
	 * @param PersonalMessagePublication
	 * @return string The url
	 */
	function get_publication_deleting_url($profile)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
	}
	
	/**
	 * Gets the url for editing a profile publication
	 * @param PersonalMessagePublication
	 * @return string The url
	 */
	function get_publication_editing_url($profile)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
	}

	/**
	 * Gets the url for viewing a profile publication
	 * @param ProfilePublication
	 * @return string The url
	 */
	function get_publication_viewing_url($profile)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
	}

	/**
	 * Gets the url for replying to a profile publication
	 * @param ProfilePublication
	 * @return string The url
	 */
	function get_publication_reply_url($profile)
	{
		return $this->get_url(array (ProfilerManager :: PARAM_ACTION => ProfilerManager :: ACTION_CREATE_PUBLICATION, ProfilePublisher :: PARAM_ACTION => 'publicationcreator', ProfilePublisher :: PARAM_LEARNING_OBJECT_ID => $profile->get_profile(), self :: PARAM_PROFILE_ID => $profile->get_id(), ProfilePublisher :: PARAM_EDIT => 1));
	}

	/**
	 * Gets the url for creating a profile publication
	 * @param ProfilePublication
	 * @return string The url
	 */
	function get_profile_creation_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_PUBLICATION));
	}

	/**
	 * Gets the HOME URL for a profile publication
	 * @param ProfilePublication
	 * @return string The url
	 */
	function get_profile_home_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES));
	}
	
	function get_profiler_category_manager_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES));
	}

	/**
	 * Gets the search condition for a profile publication
	 */
	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}

	/**
	 * Gets the search form for a profile publication
	 */
	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new ProfilerSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	/**
	 * Gets the search form's validate
	 */
	function get_search_validate()
	{
		return $this->get_search_form()->validate();
	}

	/**
	 * Parse the input from the sortable tables and process input accordingly
	 */
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[ProfilePublicationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_MARK_SELECTED_READ :
					$this->set_action(self :: ACTION_MARK_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					$_GET[self :: PARAM_MARK_TYPE] = self :: PARAM_MARK_SELECTED_READ;
					break;
				case self :: PARAM_MARK_SELECTED_UNREAD :
					$this->set_action(self :: ACTION_MARK_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					$_GET[self :: PARAM_MARK_TYPE] = self :: PARAM_MARK_SELECTED_UNREAD;
					break;
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_PUBLICATION);
					$_GET[self :: PARAM_PROFILE_ID] = $selected_ids;
					break;
			}
		}
	}
	
	function get_platform_setting($variable, $application = self :: APPLICATION_NAME)
	{
		return PlatformSetting :: get($variable, $application = self :: APPLICATION_NAME);
	}
	
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}
}
?>