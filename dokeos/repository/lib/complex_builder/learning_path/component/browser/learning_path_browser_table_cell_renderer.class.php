<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LearningPathBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function LearningPathBrowserTableCellRenderer($browser, $condition)
	{
		parent :: __construct($browser, $condition);
	}
	
	private $lpi_ref_object;
	
	// Inherited
	function render_cell($column, $cloi)
	{
		$lo = $this->retrieve_learning_object($cloi->get_ref());
		if($lo->get_type() == 'learning_path_item')
		{
			if(!$this->lpi_ref_object || $this->lpi_ref_object->get_id() != $lo->get_reference())
			{
				$ref_lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($lo->get_reference());
				$this->lpi_ref_object = $ref_lo;	
			}
			else
			{
				$ref_lo = $this->lpi_ref_object;
			}
		}
		
		return parent :: render_cell($column, $cloi, $ref_lo);
	}

}
?>