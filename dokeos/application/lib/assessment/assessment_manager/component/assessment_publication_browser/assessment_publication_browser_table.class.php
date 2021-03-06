<?php
/**
 * @package assessment.assessment_manager.component.assessment_publication_browser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/assessment_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/assessment_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../assessment_manager.class.php';

/**
 * Table to display a list of assessment_publications
 *
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentPublicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'assessment_publication_browser_table';

	/**
	 * Constructor
	 */
	function AssessmentPublicationBrowserTable($browser, $parameters, $condition)
	{
		$model = new AssessmentPublicationBrowserTableColumnModel();
		$renderer = new AssessmentPublicationBrowserTableCellRenderer($browser);
		$data_provider = new AssessmentPublicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		
		$actions[] = new ObjectTableFormAction(AssessmentManager :: PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATIONS, Translation :: get('RemoveSelected'));
		
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>