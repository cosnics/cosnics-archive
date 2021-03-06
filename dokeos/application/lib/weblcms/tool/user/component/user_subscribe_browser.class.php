<?php

require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../../weblcms_manager/component/subscribed_user_browser/subscribed_user_browser_table.class.php';

class UserToolSubscribeBrowserComponent extends UserToolComponent
{
	private $action_bar;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$this->action_bar = $this->get_action_bar();
		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool ::ACTION_SUBSCRIBE_USERS)), Translation :: get('SubscribeUsers')));
        $trail->add_help('courses user');

		$this->display_header($trail, true);

		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->action_bar->as_html();
		echo $this->get_user_subscribe_html();

		$this->display_footer();
	}

	function get_user_subscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(), WeblcmsManager :: PARAM_TOOL => 'user', UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS, 'application' => 'weblcms'), $this->get_subscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)));

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewUsers'), Theme :: get_common_image_path().'place_users.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USERS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}

	function get_subscribe_condition()
	{
		$condition = null;

		$relation_conditions = array();
		$relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_course()->get_id());
		$relation_condition = new AndCondition($relation_conditions);

		$users = $this->get_parent()->retrieve_course_user_relations($relation_condition);

		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user()));
		}

		$condition = new AndCondition($conditions);

		if ($this->get_condition())
		{
			$condition = new AndCondition($condition, $this->get_condition());
		}
		return $condition;
	}


	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			return new OrCondition($conditions);
		}
	}
}
?>