<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../wiki_publication.class.php';

/**
 * This class describes the form for a WikiPublication object.
 * @author Sven Vanpoucke & Stefan Billiet
 **/
class WikiPublicationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

    const PARAM_CATEGORY_ID = 'category';
	const PARAM_TARGET = 'target_users_and_groups';
	const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
	const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
	const PARAM_FOREVER = 'forever';
	const PARAM_FROM_DATE = 'from_date';
	const PARAM_TO_DATE = 'to_date';
	const PARAM_HIDDEN = 'hidden';
	const PARAM_EMAIL = 'email';

	private $wiki_publication;
	private $user;

    function WikiPublicationForm($form_type, $wiki_publication, $action, $user)
    {
    	parent :: __construct('wiki_publication_settings', 'post', $action);

    	$this->wiki_publication = $wiki_publication;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
        $attributes = array();
		//$attributes['search_url'] = Path :: get(WEB_PATH).'application/lib/weblcms/xml_feeds/xml_course_user_group_feed.php?course=' . $this->course->get_id();
		$locale = array ();
		$locale['Display'] = Translation :: get('SelectRecipients');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$attributes['locale'] = $locale;
		$attributes['exclude'] = array('user_' . $this->user->get_id());
		$attributes['defaults'] = array();

		$this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);

		$this->add_forever_or_timewindow();
		$this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
		if($this->email_option)
		{
			$this->addElement('checkbox', self::PARAM_EMAIL, Translation :: get('SendByEMail'));
		}
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_wiki_publication()
    {
    	$wiki_publication = $this->wiki_publication;
        $wiki_publication->set_content_object($wiki_publication->get_content_object()->get_id());
        
    	$values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
		{
			$wiki_publication->set_from_date(0);
            $wiki_publication->set_to_date(0);
		}
		else
		{
			$wiki_publication->set_from_date(DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]));
			$wiki_publication->set_to_date(DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]));
		}
        $wiki_publication->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $wiki_publication->set_publisher($this->user->get_default_property('user_id'));
    	$wiki_publication->set_published(time());
    	$wiki_publication->set_modified(time());
    	$wiki_publication->set_display_order(0);
    	$wiki_publication->set_email_sent($values[WikiPublication :: PROPERTY_EMAIL_SENT]?$values[WikiPublication :: PROPERTY_EMAIL_SENT]:0);

        if($this->email_option && $values[self::PARAM_EMAIL])
		{
//			$content_object = $this->content_object;
//			$display = ContentObjectDisplay::factory($content_object);
//
//			$adm = AdminDataManager :: get_instance();
//			$site_name_setting = PlatformSetting :: get('site_name');
//
//			$subject = '['.$site_name_setting.'] '.$content_object->get_title();
//			$body = new html2text($display->get_full_html());
//			// TODO: send email to correct users/course_groups. For testing, the email is sent now to the repo_viewer.
//			$user = $this->user;
//			$mail = Mail :: factory($content_object->get_title(), $body->get_text(), $user->get_email());
//
//			if($mail->send())
//			{
//				$pub->set_email_sent(true);
//			}
//
//			if (!$pub->update())
//			{
//				return false;
//			}
        }

    	return $wiki_publication->update();
    }

    function create_wiki_publication()
    {
    	$wiki_publication = $this->wiki_publication;
    	$values = $this->exportValues();

        $wiki_publication->set_id(WikiDataManager :: get_instance()->get_next_wiki_publication_id());

        if ($values[self :: PARAM_FOREVER] != 0)
		{
			$wiki_publication->set_from_date(0);
            $wiki_publication->set_to_date(0);
		}
		else
		{
			$wiki_publication->set_from_date(DokeosUtilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]));
			$wiki_publication->set_to_date(DokeosUtilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]));
		}
        $wiki_publication->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $wiki_publication->set_publisher($this->user->get_default_property('user_id'));
    	$wiki_publication->set_published(time());
    	$wiki_publication->set_modified(time());
    	$wiki_publication->set_display_order(0);
    	$wiki_publication->set_email_sent($values[WikiPublication :: PROPERTY_EMAIL_SENT]?$values[WikiPublication :: PROPERTY_EMAIL_SENT]:0);

        if($this->email_option && $values[self::PARAM_EMAIL])
		{
//			$content_object = $this->content_object;
//			$display = ContentObjectDisplay::factory($content_object);
//
//			$adm = AdminDataManager :: get_instance();
//			$site_name_setting = PlatformSetting :: get('site_name');
//
//			$subject = '['.$site_name_setting.'] '.$content_object->get_title();
//			$body = new html2text($display->get_full_html());
//			// TODO: send email to correct users/course_groups. For testing, the email is sent now to the repo_viewer.
//			$user = $this->user;
//			$mail = Mail :: factory($content_object->get_title(), $body->get_text(), $user->get_email());
//
//			if($mail->send())
//			{
//				$pub->set_email_sent(true);
//			}
//
//			if (!$pub->update())
//			{
//				return false;
//			}
		}

   		return $wiki_publication->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults()
    {
    	$defaults = array();
    	$defaults[self :: PARAM_TARGET_OPTION] = 0;
		$defaults[self :: PARAM_FOREVER] = 1;
		parent :: setDefaults($defaults);
    }
}
?>