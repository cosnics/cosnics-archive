<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
require_once dirname(__FILE__).'/reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php';

require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class ReportingManagerBrowserComponent extends ReportingManagerComponent
{
    private $action_bar;
    private $application;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->application = Request :: get('app');

        if(!$application)
        	$application = $this->application = 'admin';
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application)), Translation :: get(Application :: application_to_class($application)) . '&nbsp;' . Translation :: get('Template')));
        $trail->add_help('reporting general');

        if (!$this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit;
        }

        $this->action_bar = $this->get_action_bar();
        //$output = $this->get_template_html();

        $this->display_header($trail);
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo '<div id="applications" class="applications">';
        echo $this->get_applications();
        if(isset ($application))
            echo $this->get_template_html();
        else
            echo $this->get_templates();
        unset($application);
        echo '</div>';
        //echo $output;
        $this->display_footer();
    }

    /**
     * Gets all the installed applications
     */
    function get_applications()
    {
        require_once Path :: get_admin_path().'lib/admin_manager/admin_manager.class.php';
        $application = $this->application;

        $html = array();

        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu.js' .'"></script>';
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu_interface.js' .'"></script>';
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_dock.js' .'"></script>';

        $html[] = '<div class="dock" id="dock">';
        $html[] = '<div class="dock-container"> ';
        $applications = WebApplication :: load_all();
        $admin_manager = CoreApplication :: factory('admin', $this->get_user());
        $links = $admin_manager->get_application_platform_admin_links();

        foreach ($links as $application_links)
        {
            if (isset($application) && $application == $application_links['application']['class'])
            {
            //$html[] = '<div class="application_current">';
            }
            else
            {
            //$html[] = '<div class="application">';
            }
            //$html[] = '<a id="'.$application_links['application']['class'].'" class="dock-item" href="'. $this->get_url(array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application_links['application']['class'])) .'">';
            //$html[] = '<a class="dock-item" href="#tabs-'.$index.'" />';
            $html[] = '<a id="'.$application_links['application']['class'].'" class="dock-item" href="core.php?application=reporting&go=browse_templates&app=' .$application_links['application']['class'] .'" />'; //. '#application-'.$application_links['application']['class']
            $html[] = '<img src="'. Theme :: get_image_path('admin') . 'place_' . $application_links['application']['class'] .'.png" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
            $html[] = '<span>'. $application_links['application']['name'].'</span>';
            $html[] = '</a>';
        }

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div><br /><br />';
        return implode("\n", $html);
    }

    function get_templates()
    {
        require_once Path :: get_admin_path().'lib/admin_manager/admin_manager.class.php';
        $html = array();

        $html[] = '<div id="applications-list" class="applications-list" >';

        $admin_manager = CoreApplication :: factory('admin', $this->get_user());
        $links = $admin_manager->get_application_platform_admin_links();

        foreach($links as $application_links)
        {
            $this->application = $application_links['application']['class'];
            $html[] = '<div id="application-'. $this->application .'">';
            $html[] = $this->get_template_html();
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
        }
        $html[] = '</div>';

        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_browser.js' .'"></script>';
        return implode("\n", $html);
    }

    /**
     * Converts an array of templates for this application to a table
     */
    function get_template_html()
    {
        $table = new ReportingTemplateRegistrationBrowserTable($this, array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $this->application), $this->get_condition());
        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if(isset($query) && $query != '')
        {
            $conditions[] = new LikeCondition(ReportingTemplateRegistration :: PROPERTY_TITLE, $query);
            $conditions[] = new LikeCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $query);
            $orcond = new OrCondition($conditions);
            $condition = new EqualityCondition('platform','1');
            $cond = new AndCondition($orcond,$condition);
        }else
        {
            $conditions[] = new EqualityCondition('application',$this->application);
            $conditions[] = new EqualityCondition('platform','1');
            $cond = new AndCondition($conditions);
        }
        return $cond;
    }

    function get_reporting_template()
    {
        return (Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) ? Request :: get(ReportingManager :: PARAM_TEMPLATE_ID) : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $this->get_reporting_template())));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>