<?php
/**
 * @package repository.repositorymanager
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../content_object_difference_display.class.php';
/**
 * Repository manager component which can be used to compare a learning object.
 */
class RepositoryManagerComparerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail(false);

		$object_id = Request :: get(RepositoryManager :: PARAM_COMPARE_OBJECT);
		$version_id = Request :: get(RepositoryManager :: PARAM_COMPARE_VERSION);

		if ($object_id && $version_id)
		{
			$object = $this->retrieve_content_object($object_id);

			if ($object->get_state() == ContentObject :: STATE_RECYCLED)
			{
				$trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation :: get('RecycleBin')));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object_id)), $object->get_title()));
			$trail->add(new Breadcrumb(null, Translation :: get('DifferenceBetweenTwoVersions')));
			$trail->add_help('repository comparer');
			$this->display_header($trail, false, true);

			$diff = $object->get_difference($version_id);

			$display = ContentObjectDifferenceDisplay :: factory($diff);

			echo DokeosUtilities :: add_block_hider();
			echo DokeosUtilities :: build_block_hider('compare_legend');
			echo $display->get_legend();
			echo DokeosUtilities :: build_block_hider();
			echo $display->get_diff_as_html();

			$this->display_footer();
		}
		else
		{
			$this->display_warning_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>