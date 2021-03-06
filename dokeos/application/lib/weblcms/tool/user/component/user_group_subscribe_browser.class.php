<?php

require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../../weblcms_manager/component/subscribe_group_browser/subscribe_group_browser_table.class.php';
require_once Path :: get_group_path() . '/lib/group_menu.class.php';

class UserToolGroupSubscribeBrowserComponent extends UserToolComponent
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
		$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS)), Translation :: get('SubscribeGroups')));
        $trail->add(new BreadCrumb($this->get_url(), Translation :: get('SubscribeGroups')));
        $trail->add_help('courses user');

        $this->add_group_menu_breadcrumbs($trail);
		$this->display_header($trail, true);

		echo $this->action_bar->as_html();
		echo $this->get_group_menu();
		echo $this->get_group_subscribe_html();

		$this->display_footer();
	}

	function get_group_subscribe_html()
	{
		$table = new SubscribeGroupBrowserTable($this, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(), WeblcmsManager :: PARAM_TOOL => 'user', UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS, 'application' => 'weblcms'), $this->get_condition());

		$html = array();
		$html[] = '<div style="width: 75%; float: right;">';
		$html[] = $table->as_html();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_group_menu()
	{
		$groupmenu = new GroupMenu(Request :: get('group_id'), '?application=weblcms&go=courseviewer&course=' . $this->get_course()->get_id() . '&tool=user&tool_action=subscribe_groups&group_id=%s');
        return '<div style="overflow: auto; width: 20%; float: left;">' . $groupmenu->render_as_tree() . '<br /></div>';
	}

    private function add_group_menu_breadcrumbs(&$breadcrumb_trail)
    {
        $groupmenu = new GroupMenu(Request :: get('group_id'), '?application=weblcms&go=courseviewer&course=' . $this->get_course()->get_id() . '&tool=user&tool_action=subscribe_groups&group_id=%s');
        foreach($groupmenu->get_breadcrumbs() as $breadcrumb)
        {
            $breadcrumb_trail->add(new BreadCrumb($breadcrumb['url'], $breadcrumb['title']));
        }
    }

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)));

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewUsers'), Theme :: get_common_image_path().'place_users.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USERS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}


	function get_condition()
	{
		$conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, Request :: get('group_id')?Request :: get('group_id'):0);

		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions2[] = new LikeCondition(Group :: PROPERTY_NAME, $query);
			$conditions2[] = new LikeCondition(Group :: PROPERTY_DESCRIPTION, $query);
			$conditions[] = new OrCondition($conditions2);
		}

		return new AndCondition($conditions);
	}
}
?>