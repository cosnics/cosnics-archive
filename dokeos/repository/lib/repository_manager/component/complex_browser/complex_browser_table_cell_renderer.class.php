<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/complex_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../content_object.class.php';
require_once dirname(__FILE__).'/../../../content_object.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ComplexBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	protected $content_object;
	protected $rdm;
	protected $condition;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ComplexBrowserTableCellRenderer($browser, $condition)
	{
		parent :: __construct();
		$this->browser = $browser;
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->condition = $condition;
	}

	function retrieve_content_object($lo_id)
	{
		if(!$this->content_object || $this->content_object->get_id() != $lo_id)
		{
			$content_object = $this->rdm->retrieve_content_object($lo_id);
			$this->content_object = $content_object;
		}
		else
		{
			$content_object = $this->content_object;
		}

		return $content_object;
	}

	// Inherited
	function render_cell($column, $cloi, $content_object = null)
	{
		if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($cloi);
		}

		if(!$content_object)
			$content_object = $this->retrieve_content_object($cloi->get_ref());

		switch ($column->get_name())
		{
			case Translation :: get(DokeosUtilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TYPE)) :
				$type = $content_object->get_type();
				$icon = $content_object->get_icon_name();
				$url = '<img src="'.Theme :: get_common_image_path() . 'content_object/' .$icon.'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class($type).'TypeName')).'"/>';
				return $url;//'<a href="'.htmlentities($this->browser->get_type_filter_url($content_object->get_type())).'">'.$url.'</a>';
			case Translation :: get(DokeosUtilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
				$title = htmlspecialchars($content_object->get_title());
				$title_short = $title;
                $title_short = DokeosUtilities::truncate_string($title_short,53,false);

				if($content_object->is_complex_content_object())
				{
					$title_short = '<a href="' . $this->browser->get_url(
						array(ComplexBuilder :: PARAM_ROOT_LO => $this->browser->get_root(),
							  ComplexBuilder :: PARAM_CLOI_ID => $cloi->get_id(), 'publish' => Request :: get('publish'))) . '">' . $title_short . '</a>';
				}

				return $title_short; //'<a href="'.htmlentities($this->browser->get_content_object_viewing_url($content_object)).'" title="'.$title.'">'.$title_short.'</a>';
			case Translation :: get(DokeosUtilities :: underscores_to_camelcase(ContentObject :: PROPERTY_DESCRIPTION)) :
				$description = strip_tags($content_object->get_description());
				if(strlen($description) > 75)
				{
					mb_internal_encoding("UTF-8");
					$description = mb_substr(strip_tags($content_object->get_description()),0,200).'&hellip;';
				}
                 return DokeosUtilities::truncate_string($description,75);
			case Translation :: get('Subitems'):
				if($cloi->is_complex())
				{
					$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $cloi->get_ref(), ComplexContentObjectItem :: get_table_name());
					return $this->rdm->count_complex_content_object_items($condition);
				}
				return 0;
		}
	}
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	protected function get_modification_links($cloi, $additional_toolbar_items = array(), $no_move = false)
	{
		$toolbar_data = array();

		$edit_url = $this->browser->get_complex_content_object_item_edit_url($cloi, $this->browser->get_root());
		if($cloi->is_extended() || get_parent_class($this->browser) == 'ComplexBuilder')
		{
			$toolbar_data[] = array(
				'href' => $edit_url,
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('EditNA'),
				'img' => Theme :: get_common_image_path().'action_edit_na.png'
			);
		}

		$delete_url = $this->browser->get_complex_content_object_item_delete_url($cloi, $this->browser->get_root());
		$moveup_url = $this->browser->get_complex_content_object_item_move_url($cloi, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_UP);
		$movedown_url = $this->browser->get_complex_content_object_item_move_url($cloi, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_DOWN);

		$toolbar_data[] = array(
			'href' => $delete_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
			'confirm' => true
		);

		$allowed = $this->check_move_allowed($cloi);

		if(!$no_move)
		{
			if($allowed["moveup"])
			{
				$toolbar_data[] = array(
					'href' => $moveup_url,
					'label' => Translation :: get('MoveUp'),
					'img' => Theme :: get_common_image_path().'action_up.png',
				);
			}
			else
			{
				$toolbar_data[] = array(
					'label' => Translation :: get('MoveUpNA'),
					'img' => Theme :: get_common_image_path().'action_up_na.png',
				);

			}

			if($allowed["movedown"])
			{
				$toolbar_data[] = array(
					'href' => $movedown_url,
					'label' => Translation :: get('MoveDown'),
					'img' => Theme :: get_common_image_path().'action_down.png',
				);
			}
			else
			{
				$toolbar_data[] = array(
					'label' => Translation :: get('MoveDownNA'),
					'img' => Theme :: get_common_image_path().'action_down_na.png',
				);
			}
		}

		$toolbar_data = array_merge($toolbar_data, $additional_toolbar_items);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}

	protected function check_move_allowed($cloi)
	{
		$moveup_allowed = true;
		$movedown_allowed = true;

		$count = $this->rdm->count_complex_content_object_items($this->condition);
		if($count == 1)
		{
			$moveup_allowed = false;
			$movedown_allowed = false;
		}
		else
		{
			if($cloi->get_display_order() == 1)
			{
				$moveup_allowed = false;
			}
			else
			{
				if($cloi->get_display_order() == $count)
				{
					$movedown_allowed = false;
				}
			}
		}

		return array('moveup' =>$moveup_allowed, 'movedown' => $movedown_allowed);
	}
}
?>