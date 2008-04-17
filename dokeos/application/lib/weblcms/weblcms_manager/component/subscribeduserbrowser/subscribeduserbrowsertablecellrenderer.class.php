<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/subscribeduserbrowsertablecolumnmodel.class.php';
require_once Path :: get_user_path(). 'lib/user_table/defaultusertablecellrenderer.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The weblcms browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param WeblcmsBrowserComponent $browser
	 */
	function SubscribedUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === SubscribedUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_user_property())
		{
			// Exceptions that need post-processing go here ...
			case User :: PROPERTY_STATUS :
				$course_user_relation = $this->browser->get_parent()->retrieve_course_user_relation($this->browser->get_course_id(), $user->get_user_id());
				if ($course_user_relation->get_status() == 1)
				{
					return Translation :: get('CourseAdmin');
				}
				else
				{
					return Translation :: get('Student');
				}
			case User :: PROPERTY_PLATFORMADMIN :
				if ($user->get_platformadmin() == '1')
				{
					return Translation :: get('PlatformAdmin');
				}
				else
				{
					return '';
				}
			case User :: PROPERTY_EMAIL:
				return '<a href="mailto:'.$user->get_email().'">'.$user->get_email().'</a>';
		}
		return parent :: render_cell($column, $user);
	}
	/**
	 * Gets the action links to display
	 * @param User $user The user for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		if($this->browser->get_parameter(Weblcms::PARAM_USER_ACTION) == Weblcms :: ACTION_SUBSCRIBE)
		{
			$parameters = array();
			$parameters[Weblcms::PARAM_ACTION] = Weblcms::ACTION_SUBSCRIBE;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_user_id();
			$subscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $subscribe_url,
				'label' => Translation :: get('Subscribe'),
				'img' => Theme :: get_common_img_path().'user-subscribe.gif'
			);
		}
		else
		{
			if($user->get_user_id() != $this->browser->get_user()->get_user_id())
			{
				$parameters = array();
				$parameters[Weblcms::PARAM_ACTION] = Weblcms::ACTION_UNSUBSCRIBE;
				$parameters[Weblcms :: PARAM_USERS] = $user->get_user_id();
				$unsubscribe_url = $this->browser->get_url($parameters);
				$toolbar_data[] = array(
					'href' => $unsubscribe_url,
					'label' => Translation :: get('Unsubscribe'),
					'img' => Theme :: get_common_img_path().'user-unsubscribe.gif'
				);
			}
			$parameters = array();
			$parameters[Weblcms::PARAM_USER_ACTION] = UserTool::USER_DETAILS;
			$parameters[Weblcms :: PARAM_USERS] = $user->get_user_id();
			$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Details'),
				'img' => Theme :: get_common_img_path().'profile.gif'
			);
		}
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>