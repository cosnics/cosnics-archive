<?php
/**
 * $Id: learning_object_publication_form.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/content_object_publication.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path().'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class ContentObjectPublicationForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/
	const TYPE_SINGLE = 1;
	const TYPE_MULTI = 2;

	// XXX: Some of these constants heavily depend on FormValidator.
	const PARAM_CATEGORY_ID = 'category';
	const PARAM_TARGET = 'target_users_and_course_groups';
	const PARAM_TARGET_ELEMENTS = 'target_users_and_course_groups_elements';
	const PARAM_TARGET_OPTION = 'target_users_and_course_groups_option';
	const PARAM_FOREVER = 'forever';
	const PARAM_FROM_DATE = 'from_date';
	const PARAM_TO_DATE = 'to_date';
	const PARAM_HIDDEN = 'hidden';
	const PARAM_EMAIL = 'email';
	/**#@-*/
	/**
	 * The tool in which the publication will be made
	 */
	private $tool;
	/**
	 * The learning object that will be published
	 */
	private $content_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $publication;
	/**
	 * Is a 'send by email' option available?
	 */
	private $email_option;
	/**
	 * The course we're publishing in
	 */
	private $course;

	private $user;

	private $repo_viewer;

	private $form_type;
	/**
	 * Creates a new learning object publication form.
	 * @param ContentObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function ContentObjectPublicationForm($form_type, $content_object, $repo_viewer, $email_option = false, $course, $in_repo_viewer = true, $extra_parameters = array())
    {
    	if($repo_viewer)
    	{
    		$pub_param = $repo_viewer->get_parameters();
    	}

    	$this->form_type = $form_type;
		switch($this->form_type)
		{
			case self :: TYPE_SINGLE:
		    	if(get_class($content_object) == 'Introduction')
		    	{
		    		$parameters = array_merge($pub_param, array (ContentObjectRepoViewer :: PARAM_ID => $content_object->get_id(), Tool :: PARAM_ACTION => $in_repo_viewer?Tool :: ACTION_PUBLISH_INTRODUCTION:null));
		    	}
		    	else
		    	{
		    		$parameters = array_merge($pub_param, array (ContentObjectRepoViewer :: PARAM_ID => $content_object->get_id(), Tool :: PARAM_ACTION => $in_repo_viewer?Tool :: ACTION_PUBLISH:null));
		    	}
				break;
			case self :: TYPE_MULTI:
				$parameters = array_merge($pub_param, array (Tool :: PARAM_ACTION => $in_repo_viewer?Tool :: ACTION_PUBLISH:null, ContentObjectRepoViewer :: PARAM_ID => $content_object));
				break;
		}

    	$parameters = array_merge($parameters, $extra_parameters);

    	$url = $repo_viewer->get_url($parameters);
		parent :: __construct('publish', 'post', $url);

		$this->repo_viewer = $repo_viewer;

		if($in_repo_viewer)
		{
			$this->tool = $repo_viewer->get_parent()->get_parent();
		}
		else
		{
			$this->tool = $repo_viewer->get_parent();
		}

		$this->content_object = $content_object;
		$this->email_option = $email_option;
		$this->course = $course;
		$this->user = $repo_viewer->get_user();

		switch($this->form_type)
		{
			case self :: TYPE_SINGLE:
				$this->build_single_form();
				break;
			case self :: TYPE_MULTI:
				$this->build_multi_form();
				break;
		}
		$this->add_footer();
		$this->setDefaults();
    }
    /**
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param ContentObjectPublication $publication
     */
    function set_publication($publication)
    {
    	$this->publication = $publication;
		$this->addElement('hidden','pid');
		$this->addElement('hidden','action');
		$defaults['action'] = 'edit';
		$defaults['pid'] = $publication->get_id();
		$defaults[ContentObjectPublication :: PROPERTY_FROM_DATE] = $publication->get_from_date();
		$defaults[ContentObjectPublication :: PROPERTY_TO_DATE] = $publication->get_to_date();
		if($defaults[ContentObjectPublication :: PROPERTY_FROM_DATE] != 0)
		{
			$defaults[self :: PARAM_FOREVER] = 0;
		}
		$defaults[ContentObjectPublication :: PROPERTY_HIDDEN] = $publication->is_hidden();
		$defaults[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] = $publication->get_show_on_homepage();

		$udm = UserDataManager :: get_instance();
		$wdm = WeblcmsDataManager :: get_instance();

		$target_groups = $this->publication->get_target_course_groups();
		$target_users = $this->publication->get_target_users();

		$defaults[self :: PARAM_TARGET_ELEMENTS] = array();
		foreach($target_groups as $target_group)
		{
		    $group = $wdm->retrieve_course_group($target_group);

		    $selected_group = array ();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_description();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
		}
    	foreach($target_users as $target_user)
		{
		    $user = $udm->retrieve_user($target_user);

		    $selected_user = array ();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
		}

		if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
		{
            $defaults[self :: PARAM_TARGET_OPTION] = '1';
		}

		$active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
		$active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));

		parent::setDefaults($defaults);
    }
	/**
	 * Sets the default values of the form.
	 *
	 * By default the publication is for everybody who has access to the tool
	 * and the publication will be available forever.
	 */
    function setDefaults()
    {
    	$defaults = array();
    	$defaults[self :: PARAM_TARGET_OPTION] = 0;
		$defaults[self :: PARAM_FOREVER] = 1;
		$defaults[self :: PARAM_CATEGORY_ID] = Request :: get('pcattree');
		parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
    	$this->build_form();
    }

    function build_multi_form()
    {
    	$this->build_form();
    	$this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    private $categories;
    private $level = 1;

    function get_categories($parent_id)
    {
    	$conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, Request :: get('course'));
		$conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, Request :: get('tool'));
		$conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
		$condition = new AndCondition($conditions);

		$cats = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_categories($condition);
		while($cat = $cats->next_result())
		{
			$this->categories[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
			$this->level++;
			$this->get_categories($cat->get_id());
			$this->level--;
		}
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
		$this->categories[0] = Translation :: get('Root');
		$this->get_categories(0);

		//$categories = $this->repo_viewer->get_categories(true);
		if(count($this->categories) > 1)
		{
			// More than one category -> let user select one
			$this->addElement('select', self :: PARAM_CATEGORY_ID, Translation :: get('Category'), $this->categories);
		}
		else
		{
			// Only root category -> store object in root category
			$this->addElement('hidden', ContentObjectPublication :: PROPERTY_CATEGORY_ID, 0);
		}

		$attributes = array();
		$attributes['search_url'] = Path :: get(WEB_PATH).'application/lib/weblcms/xml_feeds/xml_course_user_group_feed.php?course=' . $this->course->get_id();
		$locale = array ();
		$locale['Display'] = Translation :: get('SelectRecipients');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
		$attributes['exclude'] = array('user_' . $this->tool->get_user_id());
		$attributes['defaults'] = array();

		$this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);

		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
		if($this->email_option)
		{
			$this->addElement('checkbox', self::PARAM_EMAIL, Translation :: get('SendByEMail'));
		}
		$this->addElement('checkbox', ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, Translation :: get('ShowOnHomepage'));
    }

    function add_footer()
    {
    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    	//$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }
    /**
     * Updates a learning object publication using the values from the form.
     * @return ContentObjectPublication The updated publication
     * @todo This function shares some code with function
     * create_content_object_publication. This code duplication should be
     * resolved.
     */
    function update_content_object_publication()
    {
		$values = $this->exportValues();
		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		$hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$category = $values[self :: PARAM_CATEGORY_ID];

		$users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
		$course_groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];

		$pub = $this->publication;
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_hidden($hidden);
		$modifiedDate = time();
		$pub->set_modified_date($modifiedDate);
		$pub->set_target_users($users);
		$pub->set_target_course_groups($course_groups);
		$show_on_homepage = ($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
		$pub->set_show_on_homepage($show_on_homepage);
		$pub->set_category_id($category);
		$pub->update();
		return $pub;
    }
	/**
	 * Creates a learning object publication using the values from the form.
	 * @return ContentObjectPublication The new publication
	 */
    function create_content_object_publication()
    {
    	// TODO: Seems like the modified date isn't being written to the DB
    	// TODO: Hidden is not being used correctly
		$values = $this->exportValues();

		if ($values[self :: PARAM_FOREVER] != 0)
		{
			$from = $to = 0;
		}
		else
		{
			$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
			$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
		}
		
		$course = $this->course->get_id();
		$tool = $this->repo_viewer->get_tool()->get_tool_id();
		$tool = (is_null($tool) ? 'introduction' : $tool);
		$category = $values[self :: PARAM_CATEGORY_ID];

		$wdm = WeblcmsDataManager :: get_instance();
		$index = $wdm->get_next_content_object_publication_display_order_index($course, $tool, $category);
		
		$pub = new ContentObjectPublication();
		$pub->set_content_object_id($this->content_object->get_id());
		$pub->set_course_id($course);
		$pub->set_tool($tool);
		$pub->set_category_id($category);
		$pub->set_target_users($values[self :: PARAM_TARGET_ELEMENTS]['user']);
		$pub->set_target_course_groups($values[self :: PARAM_TARGET_ELEMENTS]['group']);
		$pub->set_from_date($from);
		$pub->set_to_date($to);
		$pub->set_publisher_id($this->user->get_id());
		$pub->set_publication_date(time());
		$pub->set_modified_date(time());
		$pub->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
		$pub->set_display_order_index($index);
		$pub->set_email_sent(false);
		$pub->set_show_on_homepage($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
		
		if (!$pub->create())
		{
			return false;
		}
		
		if($this->email_option && $values[self::PARAM_EMAIL])
		{
			$content_object = $this->content_object;
			$display = ContentObjectDisplay::factory($content_object);

			$adm = AdminDataManager :: get_instance();
			$site_name_setting = PlatformSetting :: get('site_name');

			$subject = '['.$site_name_setting.'] '.$content_object->get_title();
			$body = new html2text($display->get_full_html());
			// TODO: send email to correct users/course_groups. For testing, the email is sent now to the repo_viewer.
			$user = $this->user;
			$mail = Mail :: factory($content_object->get_title(), $body->get_text(), $user->get_email());

			if($mail->send())
			{
				$pub->set_email_sent(true);
			}

			if (!$pub->update())
			{
				return false;
			}
		}
		return $pub;
    }

    function create_content_object_publications()
    {
    	$values = $this->exportValues();

    	$ids = unserialize($values['ids']);

    	foreach($ids as $id)
    	{
    		$content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($id);

			if ($values[self :: PARAM_FOREVER] != 0)
			{
				$from = $to = 0;
			}
			else
			{
				$from = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
				$to = DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
			}
			
			$course = $this->course->get_id();
			$tool = $this->repo_viewer->get_tool()->get_tool_id();
			$tool = (is_null($tool) ? 'introduction' : $tool);
			$category = $values[self :: PARAM_CATEGORY_ID];
			if(!$category)
				$category = 0;

			$wdm = WeblcmsDataManager :: get_instance();
			$index = $wdm->get_next_content_object_publication_display_order_index($course, $tool, $category);
			
			$pub = new ContentObjectPublication();
			$pub->set_content_object_id($id);
			$pub->set_course_id($course);
			$pub->set_tool($tool);
			$pub->set_category_id($category);
			$pub->set_target_users($values[self :: PARAM_TARGET_ELEMENTS]['user']);
			$pub->set_target_course_groups($values[self :: PARAM_TARGET_ELEMENTS]['group']);
			$pub->set_from_date($from);
			$pub->set_to_date($to);
			$pub->set_publisher_id($this->user->get_id());
			$pub->set_publication_date(time());
			$pub->set_modified_date(time());
			$pub->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
			$pub->set_display_order_index($index);
			$pub->set_email_sent(false);
			$pub->set_show_on_homepage($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
			
			if (!$pub->create())
			{
				return false;
			}
			
			if($this->email_option && $values[self :: PARAM_EMAIL])
			{
				$display = ContentObjectDisplay::factory($content_object);

				$adm = AdminDataManager :: get_instance();
				$site_name_setting = PlatformSetting :: get('site_name');

				$subject = '['.$site_name_setting.'] '.$content_object->get_title();
				$body = new html2text($display->get_full_html());
				// TODO: send email to correct users/course_groups. For testing, the email is sent now to the repo_viewer.
				$user = $this->user;
				$mail = Mail :: factory($content_object->get_title(), $body->get_text(), $user->get_email());

				if($mail->send())
				{
					$pub->set_email_sent(true);
				}

				if (!$pub->update())
				{
					return false;
				}
			}
    	}
		return true;
    }
}
?>