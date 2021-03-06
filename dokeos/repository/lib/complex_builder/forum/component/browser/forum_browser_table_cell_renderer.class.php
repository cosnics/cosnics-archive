<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ForumBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{
    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ForumBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }
    // Inherited
    function render_cell($column, $cloi)
    {
        if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($cloi);
        }

        switch($column->get_name())
        {
        	case Translation :: get('AddDate'):
        		return $cloi->get_add_date();
        }

        return parent :: render_cell($column, $cloi);
    }

    function get_modification_links($cloi)
    {
        $array = array();
        if($cloi->get_type() == 1)
        {
            $array[]= array(
                'href' => $this->browser->get_complex_content_object_item_sticky_url($cloi,$this->browser->get_root()),
                'label' => Translation :: get('UnSticky'),
                'img' => Theme :: get_common_image_path().'unsticky_read.png'
            );
            $array[]= array(
                'label' => Translation :: get('ImportantNa'),
                'img' => Theme :: get_common_image_path().'important_na.png'
            );
        }else if($cloi->get_type() == 2)
        {
            $array[]= array(
                'label' => Translation :: get('StickyNa'),
                'img' => Theme :: get_common_image_path().'sticky_na.png'
            );
            $array[]= array(
                'href' => $this->browser->get_complex_content_object_item_important_url($cloi,$this->browser->get_root()),
                'label' => Translation :: get('UnImportant'),
                'img' => Theme :: get_common_image_path().'unimportant_read.png'
            );
        }else
        {
            $array[]= array(
                'href' => $this->browser->get_complex_content_object_item_sticky_url($cloi,$this->browser->get_root()),
                'label' => Translation :: get('MakeSticky'),
                'img' => Theme :: get_common_image_path().'sticky_read.png'
            );
            $array[]= array(
                'href' => $this->browser->get_complex_content_object_item_important_url($cloi,$this->browser->get_root()),
                'label' => Translation :: get('MakeImportant'),
                'img' => Theme :: get_common_image_path().'important_read.png'
            );
        }
        return parent :: get_modification_links($cloi, $array, true);
    }
}
?>