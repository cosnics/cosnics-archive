<?php
require_once Path :: get_library_path() . 'html/formvalidator/Element/element_finder.php';

/**
 * AJAX-based tree search and multiselect element. Use at your own risk.
 * @author Tim De Pauw
 */
class HTML_QuickForm_user_group_finder extends HTML_QuickForm_element_finder
{

    function HTML_QuickForm_user_group_finder($elementName, $elementLabel, $search_url, $locale = array ('Display' => 'Display'), $default_values = array ())
    {
        parent :: __construct($elementName, $elementLabel, $search_url, $locale, $default_values);
        $this->_type = 'user_group_finder';
    }

    function getValue()
    {
        $results = array();
        $results['user'] = array();
        $results['group'] = array();
        
        $values = $this->get_active_elements();
        
        // Process the array values so we end up with a 2-dimensional array (users and groups)
        

        foreach ($values as $value)
        {
            $value = explode('_', $value);
            $results[$value[0]][] = $value[1];
        }
        
        return $results;
    }
}
?>