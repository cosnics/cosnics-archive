<?php
/**
 * Extendable class for the reporting templates
 * This contains the general shared template properties such as
 *      Properties (name, description, etc)
 *      Layout (header,menu, footer)
 *
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_registration.class.php';
require_once dirname(__FILE__) . '/reporting.class.php';

require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

abstract class ReportingTemplate
{

    const PARAM_VISIBLE = 'visible';
    const PARAM_DIMENSIONS = 'dimensions';

    const REPORTING_BLOCK_VISIBLE = 1;
    const REPORTING_BLOCK_INVISIBLE = 0;
    const REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS = 1;
    const REPORTING_BLOCK_USE_BLOCK_DIMENSIONS = 0;
    protected $parent;
    protected $params;
    /*
     * array with all the reporting block and specific properties such as
     *  - visible
     *
     * @todo add 'zone'
     */
    protected $reporting_blocks = array();
    protected $id;
    protected $action_bar;

    function ReportingTemplate($parent=null,$id,$params)
    {
        $this->parent = $parent;
        $this->set_registration_id($id);
        $this->set_reporting_blocks_function_parameters($params);

        $this->action_bar = $this->get_action_bar();
    }//ReportingTemplateProperties

    /*
     * Layout
     */

    /**
     * The reporting template header
     * @return html representing the header
     */
    function get_header()
    {
        $html[] = '<br />' . $this->action_bar->as_html() . '<br />';
        return implode("\n", $html);
    }//get_header

    function get_action_bar()
    {
        $parameters[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $this->params;
        $parameters['s'] = Request :: get('s');
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        //$url = $this->parent->get_url(array (Tool :: PARAM_ACTION=>ReportingTool::ACTION_EXPORT_REPORT,ReportingManager::PARAM_TEMPLATE_ID => $this->id,ReportingManager::PARAM_EXPORT_TYPE=>'pdf',ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS => $this->params));
        $url = 'index_reporting.php?go=export&template='.$this->get_registration_id().'&export=pdf&'.http_build_query($parameters);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToPdf'), null, $url));

        return $action_bar;
    }

    /**
     * Generates a menu from the reporting blocks within the reporting template
     * @return html representing the menu
     */
    function get_menu($orientation)
    {
        if(!isset($orientation)) $orientation = Reporting::ORIENTATION_VERTICAL;
        $html[] = '<div class="reporting_template_menu">';
        if($orientation == Reporting::ORIENTATION_VERTICAL)
        {
            $html[] = '<ul id="nav">';
            $html[] = '<li><a href="#">'.Translation :: get('SelectReportingBlock').'</a>';
            $html[] = '<ul>';
            foreach($this->retrieve_reporting_blocks() as $key => $value)
            {
                $html[] = '<li>';
                $html[] = '<a href="' . $this->parent->get_url(array('s' => $value[0]->get_name(),'template' => $this->get_registration_id())) . '">'.Translation :: get($value[0]->get_name()).'</a>';
                $html[] = '</li>';
            }
            $html[] = '</ul></li>';
            $html[] = '</ul>';
        }else if($orientation == Reporting::ORIENTATION_HORIZONTAL)
            {
                foreach($this->retrieve_reporting_blocks() as $key => $value)
                {
                    $html[] = '<a href="' . $this->parent->get_url(array('s' => $value[0]->get_name(),'template' => $this->get_registration_id())) . '">'.Translation :: get($value[0]->get_name()).'</a> | ';
                }
            }
        $html[] = '</div>';
        $html[] = '<br /><br />';
        return implode("\n", $html);
    }

    /**
     * The reporting template footer
     * @return html representing the footer
     */
    function get_footer()
    {
        $parameters = array();
        $parameters[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = Request :: get(ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS);

        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_charttype.js' .'"></script>';
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_template_ajax.js' .'"></script>';
        return implode("\n", $html);
    }//get_footer

    /*
     * Properties
     */

    /**
     * Gets the properties for this template (name, description, platform)
     * @return an array of properties
     */
    abstract static function get_properties();

    /**
     * Sets the id under which this template is registered
     * @param int $value
     */
    function set_registration_id($value)
    {
        $this->id = $value;
    }

    /**
     * Gets the id under which this template is registered
     * @return the id under which this template is registered
     */
    function get_registration_id()
    {
        return $this->id;
    }

    /*
     * Reporting blocks
     */

    /**
     * Adds a reporting block to this template
     * @param ReportingBlock $reporting_block
     * @param int $visible
     */
    function add_reporting_block($reporting_block,$params)
    {
        array_push($this->reporting_blocks,array($reporting_block,$params));
    }

    /**
     * Sets the visible value to 1 for this reporting block & 0 for the rest
     * @param String $name
     */
    function show_reporting_block($name)
    {
        foreach($this->reporting_blocks as $key => $value)
        {
            if($value[0]->get_name() == $name)
            {
                $value[1][self :: PARAM_VISIBLE] = self :: REPORTING_BLOCK_VISIBLE;
            }else
            {
                $value[1][self :: PARAM_VISIBLE] = self :: REPORTING_BLOCK_INVISIBLE;
            }
            $this->reporting_blocks[$key] = $value;
        }
    }

    abstract function to_html();

    function to_html_export()
    {
        $html[] = '<div class="template-data">';
        $html[] = '<br /><br /><br />';
        $html[] = '<b><u>Template data</u></b><br />';
        $html[] = '<b>Template title: </b><i>'.Translation::get($properties[ReportingTemplateRegistration :: PROPERTY_TITLE]).'</i><br />';
        $html[] = '<b>Template description: </b><i>'.Translation :: get($properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION]).'</i><br />';
        if(isset($this->params['course_id']))
            $html[] = '<b>Course: </b><i>'.$this->params['course_id'].'</i>';
        $html[] = '</div><br /><br />';

        $html[] = $this->get_visible_reporting_blocks(true);
        return implode("\n", $html);
    }

    /**
     * Generates all the visible reporting blocks
     * @return html
     */
    function get_visible_reporting_blocks($export=false)
    {
        foreach($this->retrieve_reporting_blocks() as $key => $value)
        {
        // check if reporting block is visible
            if($value[1][self :: PARAM_VISIBLE] == self :: REPORTING_BLOCK_VISIBLE)
            {
                if($export)
                    $html[] = Reporting :: generate_block_export($value[0],$this->get_reporting_block_template_properties($value[0]->get_name()));
                else
                    $html[] = Reporting :: generate_block($value[0],$this->get_reporting_block_template_properties($value[0]->get_name()));
                $html[] = '<div class="clear">&nbsp;</div>';
            }
        }
        return implode("\n", $html);
    }

    function get_reporting_block_html($name)
    {
        $array = $this->retrieve_reporting_blocks();
        foreach($array as $key => $value)
        {
            if($value[0]->get_name() == $name)
            {
                return Reporting :: generate_block($value[0],$this->get_reporting_block_template_properties($name));
            }
        }
    }

    function set_reporting_block_template_properties($name,$params)
    {
        $array = $this->retrieve_reporting_blocks();
        foreach($array as $key => $value)
        {
            if($value[0]->get_name() == $name)
            {
                $value[1] = $params;
            }
        }
    }//set_reporting_block_template_properties

    function get_reporting_block_template_properties($name)
    {
        $array = $this->retrieve_reporting_blocks();
        foreach($array as $key => $value)
        {
            if($value[0]->get_name() == $name)
            {
                return $value[1];
            }
        }
    }

    /**
     * Returns all reporting blocks for this reporting template
     * @return an array of reporting blocks
     */
    function retrieve_reporting_blocks()
    {
        return $this->reporting_blocks;
    }

    function set_reporting_blocks_function_parameters($params)
    {
        $this->params = $params;
        foreach($this->retrieve_reporting_blocks() as $key => $value)
        {
            foreach ($params as $key2 => $value2)
            {
                $value[0]->add_function_parameter($key2,$value2);
            }
        }
    }//set_reporting_blocks_parameters

    function set_reporting_block_function_parameters($blockname,$params)
    {
        foreach($this->retrieve_reporting_blocks() as $key => $value)
        {
            if($value[0]->get_name() == $blockname)
            {
                $value[0]->set_function_parameters($params);
            }
        }
}//set_reporting_block_parameters
}//ReportingTemplateProperties
?>
