<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../buddy_list.class.php';

class UserManagerBuddyListViewerComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Header :: set_section('my_account');

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BuddyList')));
		$trail->add_help('user general');

		$this->display_header($trail);
		echo "<br />";

		$buddylist = new BuddyList($this->get_user(), $this);
		echo $buddylist->to_html();

		$this->display_footer();
	}
}
?>