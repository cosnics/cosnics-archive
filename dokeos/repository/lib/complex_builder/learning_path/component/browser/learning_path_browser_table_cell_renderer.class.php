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
				$lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($lo->get_reference());
				$this->lpi_ref_object = $lo;
			}
			else
			{
				$lo = $this->lpi_ref_object;
			}
		}
		switch ($column->get_name())
		{
			case Translation :: get(DokeosUtilities :: underscores_to_camelcase(LearningObject :: PROPERTY_TITLE)) :
				$title = htmlspecialchars($lo->get_title());
				$title_short = $title;

                $title_short = DokeosUtilities::truncate_string($title_short,53,false);

				if($lo->get_type() == 'learning_path')
				{
					$title_short = '<a href="' . $this->browser->get_url(
						array(ComplexBuilder :: PARAM_ROOT_LO => $this->browser->get_root(),
							  ComplexBuilder :: PARAM_CLOI_ID => $cloi->get_id(), 'publish' => Request :: get('publish'))) . '">' . $title_short . '</a>';
				}

				return $title_short;
		}

		return parent :: render_cell($column, $cloi, $lo);
	}

}
?>