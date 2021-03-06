<?php

class BreadcrumbTrail
{
    //
    private $breadcrumbtrail;
    
    private $help_items;

    function BreadcrumbTrail($include_main_index = true)
    {
        $this->breadcrumbtrail = array();
        $this->help_items = array();
        if ($include_main_index)
        {
            $this->add(new BreadCrumb($this->get_path(WEB_PATH) . 'index.php', $this->get_setting('site_name', 'admin')));
        }
    }

    function add($breadcrumb)
    {
        $this->breadcrumbtrail[] = $breadcrumb;
    }

    function add_help($help_item)
    {
        $this->help_items[] = $help_item;
    }

    function get_help_items()
    {
        return $this->help_items;
    }

    function set_help_items($help_items)
    {
        $this->help_items = $help_items;
    }

    function remove($breadcrumb_index)
    {
        unset($this->breadcrumbtrail[$breadcrumb_index]);
    }

    function get_first()
    {
        return $this->breadcrumbtrail[0];
    }

    function get_last()
    {
        $breadcrumbtrail = $this->breadcrumbtrail;
        $last_key = count($breadcrumbtrail) - 1;
        return $breadcrumbtrail[$last_key];
    }

    function truncate()
    {
        $this->breadcrumbtrail = array();
    }

    function render()
    {
        $html = array();
        
        $html[] = $this->render_breadcrumbs();
        $html[] = $this->render_help();
        
        return implode("\n", $html);
    }

    function render_breadcrumbs()
    {
        $html = array();
        $html[] = '<ul id="breadcrumbtrail">';
        
        $breadcrumbtrail = $this->breadcrumbtrail;
        if (is_array($breadcrumbtrail) && count($breadcrumbtrail) > 0)
        {
            foreach ($breadcrumbtrail as $breadcrumb)
            {
                $html[] = '<li><a href="' . $breadcrumb->get_url() . '" target="_self">' . DokeosUtilities :: truncate_string($breadcrumb->get_name(), 50, true) . '</a></li>';
            }
        }
        
        $html[] = '</ul>';
        
        return implode("\n", $html);
    }

    function render_help()
    {
        $html = array();
        $help_items = $this->help_items;
        
        if (is_array($help_items) && count($help_items) > 0)
        {
            $items = array();
            
            foreach ($help_items as $help_item)
            {
                $item = HelpManager :: get_tool_bar_help_item($help_item);
                if ($item)
                {
                    $items[] = $item;
                }
            }
            
            if (count($items) > 0)
            {
                $html[] = '<div id="helpitem">';
                $toolbar = new Toolbar();
                $toolbar->set_items($items);
                $toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
                $html[] = $toolbar->as_html();
                $html[] = '</div>';
            }
        }
        
        return implode("\n", $html);
    }

    function size()
    {
        return count($this->breadcrumbtrail);
    }

    function display()
    {
        $html = $this->render();
        echo $html;
    }

    function get_breadcrumbtrail()
    {
        return $this->breadcrumbtrail;
    }

    function set_breadcrumbtrail($breadcrumbtrail)
    {
        $this->breadcrumbtrail = $breadcrumbtrail;
    }

    function get_setting($variable, $application)
    {
        return PlatformSetting :: get($variable, $application);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function get_breadcrumbs()
    {
        return $this->breadcrumbtrail;
    }

    function merge($trail)
    {
        $this->breadcrumbtrail = array_merge($this->breadcrumbtrail, $trail->get_breadcrumbtrail());
    }
}
?>