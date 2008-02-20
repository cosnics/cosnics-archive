<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/repositorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function RepositoryBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $learning_object)
	{
		if ($column === RepositoryBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($learning_object);
		}
		switch ($column->get_learning_object_property())
		{
			case LearningObject :: PROPERTY_TYPE :
				return '<a href="'.htmlentities($this->browser->get_type_filter_url($learning_object->get_type())).'">'.parent :: render_cell($column, $learning_object).'</a>';
			case LearningObject :: PROPERTY_TITLE :
				$title = parent :: render_cell($column, $learning_object);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.$title.'">'.$title_short.'</a>';
			case LearningObject :: PROPERTY_MODIFICATION_DATE:
				return format_locale_date(get_lang('dateFormatShort').', '.get_lang('timeNoSecFormat'),$learning_object->get_modification_date());
		}
		return parent :: render_cell($column, $learning_object);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($learning_object)
	{
		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_editing_url($learning_object),
			'label' => get_lang('Edit'),
			'img' => $this->browser->get_path(WEB_IMG_PATH).'edit.gif'
		);
		$html = array ();
		if ($url = $this->browser->get_learning_object_recycling_url($learning_object))
		{
			$toolbar_data[] = array(
				'href' => $url,
				'label' => get_lang('Remove'),
				'img' => $this->browser->get_path(WEB_IMG_PATH).'recycle_bin.gif',
				'confirm' => true
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => get_lang('Remove'),
				'img' => $this->browser->get_path(WEB_IMG_PATH).'recycle_bin_na.gif'
			);
		}
		if($this->browser->get_number_of_categories() > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_learning_object_moving_url($learning_object),
				'label' => get_lang('Move'),
				'img' => $this->browser->get_path(WEB_IMG_PATH).'move.gif'
			);
		}
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_metadata_editing_url($learning_object),
			'label' => get_lang('Metadata'),
			'img' => $this->browser->get_path(WEB_IMG_PATH).'info_small.gif'
		);
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_rights_editing_url($learning_object),
			'label' => get_lang('Rights'),
			'img' => $this->browser->get_path(WEB_IMG_PATH).'group_small.gif'
		);
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>