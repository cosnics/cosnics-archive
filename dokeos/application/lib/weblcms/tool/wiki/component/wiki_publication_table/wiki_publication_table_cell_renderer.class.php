<?php
/**
 * @package application.weblcms.tool.exercise.component.exercise_publication_table
 */
require_once Path :: get_repository_path(). 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/wiki_publication_table_column_model.class.php';
require_once Path :: get_repository_path().'lib/complex_display/wiki/wiki_display.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class WikiPublicationTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	private $table_actions;
	private $browser;
	/**
	 * Constructor.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 */
	function WikiPublicationTableCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		if ($column === WikiPublicationTableColumnModel :: get_action_column())
		{
			return $this->get_actions($publication);
		}
		$content_object = $publication->get_content_object();

        
		
		if ($property = $column->get_name())
            {
                switch ($property)
                {
                    //hier link maken naar externe pagina voor de wiki
                    case ContentObject :: PROPERTY_TITLE :                       
                        $homepage = WikiTool :: get_wiki_homepage($content_object->get_id());
                        if(empty($homepage))
                            $url = $this->browser->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));
                        else
                            $url = $this->browser->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, 'cid' => $homepage->get_id(), 'pid' => $publication->get_id()));
                        if($publication->is_hidden())
                        return '<a class="invisible" href="'.$url.'">' . htmlspecialchars($content_object->get_title()) . '</a>';
                        else
                        return '<a href="'.$url.'">' . htmlspecialchars($content_object->get_title()) . '</a>';
                    case ContentObject ::PROPERTY_DESCRIPTION :
                        if($publication->is_hidden())
                        return '<span class = "invisible">'.DokeosUtilities::truncate_string($content_object->get_description(),50).'</span>';
                }
            }
			return parent :: render_cell($column, $publication->get_content_object());
        
	}
	
	function get_actions($publication) 
	{
        if($this->browser->is_allowed(EDIT_RIGHT))
        {
			$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
				'label' => Translation :: get('Delete'), 
				'img' => Theme :: get_common_image_path().'action_delete.png',
	            'confirm' => true
				);
	
	        $actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
				'label' => Translation :: get('Edit'), 
				'img' => Theme :: get_common_image_path().'action_edit.png'
				);
	
	            $actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
				'label' => Translation :: get('Visible'),
				'img' => $publication->is_hidden()? Theme :: get_common_image_path().'action_visible_na.png' : Theme :: get_common_image_path().'action_visible.png'
				);
        }
		
        /*if(!WikiTool :: is_wiki_locked($publication->get_content_object()->get_id()))
        {
            $actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_LOCK, Tool :: PARAM_PUBLICATION_ID => $publication->get_content_object()->get_id())),
			'label' => Translation :: get('Lock'),
			'img' => Theme :: get_common_image_path().'action_lock.png'
			);
        }
        else
        {
            $actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_LOCK, Tool :: PARAM_PUBLICATION_ID => $publication->get_content_object()->get_id())),
			'label' => Translation :: get('Lock'),
			'img' => Theme :: get_common_image_path().'action_unlock.png'
			);
        }*/
		
        if(count($actions) > 0)
			return DokeosUtilities :: build_toolbar($actions);
	}
	
	/**
	 * Gets the links to publish or edit and publish a learning object.
	 * @param ContentObject $content_object The learning object for which the
	 * links should be returned.
	 * @return string A HTML-representation of the links.
	 */
	private function get_publish_links($content_object)
	{
		$toolbar_data = array();
		$table_actions = $this->table_actions;
		
		foreach($table_actions as $table_action)
		{
			$table_action['href'] = sprintf($table_action['href'], $content_object->get_id());
			$toolbar_data[] = $table_action;
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>
