<?php
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../announcement_distribution.class.php';

/**
 * This class describes the form for a AnnouncementPublication object.
 * @author Hans De Bisschop
 **/
class AnnouncementDistributionForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/

	const TYPE_SINGLE = 1;
	const TYPE_MULTI = 2;

	/**#@-*/
	/**
	 * The learning object that will be published
	 */
	private $learning_object;
	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $form_user;

	private $form_type;

	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function AnnouncementDistributionForm($form_type, $learning_object, $form_user, $action)
    {
		parent :: __construct('publish', 'post', $action);
		$this->form_type = $form_type;
		$this->learning_object = $learning_object;
		$this->form_user = $form_user;

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
	 * Sets the default values of the form.
	 *
	 * By default the publication is for everybody who has access to the tool
	 * and the publication will be available forever.
	 */
    function setDefaults()
    {
    	$defaults = array();
		parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
    	$this->build_form();
    }

    function build_multi_form()
    {
    	$this->build_form();
    	$this->addElement('hidden', 'ids', serialize($this->learning_object));
    }

	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
    	$shares = array ();
//    	if ($publication)
//    	{
//			$publication = $this->publication;
//			$recip = $publication->get_publication_sender();
//			$recipient = array ();
//			$recipient['id'] = $recip->get_id();
//			$recipient['class'] = 'type type_user';
//			$recipient['title'] = $recip->get_username();
//			$recipient['description'] = $recip->get_lastname() . ' ' . $recip->get_firstname();
//			$recipients[$recipient['id']] = $recipient;
//    	}

		$url = Path :: get(WEB_PATH).'application/lib/distribute/xml_user_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get('ShareWith');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = false;
		$elem = $this->addElement('user_group_finder', 'recipients', Translation :: get('SendTo'), $url, $locale, $shares);
		$elem->excludeElements(array($this->form_user->get_id()));
		$elem->setDefaultCollapsed(false);
    }

    function add_footer()
    {
    	$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    	//$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

	/**
	 * Creates a learning object publication using the values from the form.
	 * @return LearningObjectPublication The new publication
	 */
    function create_announcement_distribution()
    {
		$values = $this->exportValues();
		$recipients = $values['recipients'];

		$pub = new AnnouncementDistribution();
		$pub->set_announcement($this->learning_object->get_id());
		$pub->set_publisher($this->form_user->get_id());
		$pub->set_published(time());
		$pub->set_target_users($recipients['user']);
		$pub->set_target_groups($recipients['group']);

		if ($pub->create())
		{
			return true;
		}
		else
		{
			return false;
		}
    }

    function create_announcement_distributions()
    {
		$values = $this->exportValues();
    	$ids = unserialize($values['ids']);
    	$recipients = $values['recipients'];

    	foreach($ids as $id)
    	{
			$pub = new AnnouncementDistribution();
			$pub->set_announcement($id);
			$pub->set_publisher($this->form_user->get_id());
			$pub->set_published(time());
			$pub->set_target_users($recipients['user']);
			$pub->set_target_groups($recipients['group']);

			if (!$pub->create())
			{
				return false;
			}
    	}
    	return true;
    }
}
?>