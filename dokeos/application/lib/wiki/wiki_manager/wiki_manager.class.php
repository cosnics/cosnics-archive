<?php
/**
 * @package application.lib.wiki.wiki_manager
 */
require_once dirname(__FILE__).'/wiki_manager_component.class.php';
require_once dirname(__FILE__).'/../wiki_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once dirname(__FILE__).'/component/wiki_publication_browser/wiki_publication_browser_table.class.php';

/**
 * A wiki manager
 * @author Sven Vanpoucke & Stefan Billiet
 */
 class WikiManager extends WebApplication
 {
 	const APPLICATION_NAME = 'wiki';

	const PARAM_WIKI_PUBLICATION = 'wiki_publication';
	const PARAM_DELETE_SELECTED_WIKI_PUBLICATIONS = 'delete_selected_wiki_publications';

	const ACTION_DELETE_WIKI_PUBLICATION = 'delete_wiki_publication';
	const ACTION_EDIT_WIKI_PUBLICATION = 'edit_wiki_publication';
	const ACTION_CREATE_WIKI_PUBLICATION = 'create_wiki_publication';
	const ACTION_BROWSE_WIKI_PUBLICATIONS = 'browse_wiki_publications';
    const ACTION_VIEW_WIKI = 'view';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function WikiManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this wiki manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_WIKI_PUBLICATIONS :
				$component = WikiManagerComponent :: factory('WikiPublicationsBrowser', $this);
				break;
			case self :: ACTION_DELETE_WIKI_PUBLICATION :
				$component = WikiManagerComponent :: factory('WikiPublicationDeleter', $this);
				break;
			case self :: ACTION_EDIT_WIKI_PUBLICATION :
				$component = WikiManagerComponent :: factory('WikiPublicationUpdater', $this);
				break;
			case self :: ACTION_CREATE_WIKI_PUBLICATION :
				$component = WikiManagerComponent :: factory('WikiPublicationCreator', $this);
				break;
            case self :: ACTION_VIEW_WIKI:
				$component = WikiManagerComponent :: factory('WikiViewer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_WIKI_PUBLICATIONS);
				$component = WikiManagerComponent :: factory('WikiPublicationsBrowser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_WIKI_PUBLICATIONS :

					$selected_ids = $_POST[WikiPublicationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_WIKI_PUBLICATION);
					$_GET[self :: PARAM_WIKI_PUBLICATION] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_wiki_publications($condition)
	{
		return WikiDataManager :: get_instance()->count_wiki_publications($condition);
	}

	function retrieve_wiki_publications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WikiDataManager :: get_instance()->retrieve_wiki_publications($condition, $offset, $count, $order_property);
	}

 	function retrieve_wiki_publication($id)
	{
		return WikiDataManager :: get_instance()->retrieve_wiki_publication($id);
	}

	// Url Creation

	function get_create_wiki_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_WIKI_PUBLICATION));
	}

	function get_update_wiki_publication_url($wiki_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_WIKI_PUBLICATION,
								    self :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
	}

 	function get_delete_wiki_publication_url($wiki_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_WIKI_PUBLICATION,
								    self :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
	}

	function get_browse_wiki_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_WIKI_PUBLICATIONS));
	}

	function is_allowed()
	{
		return true;
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function content_object_is_published($object_id)
	{
	}

	function any_content_object_is_published($object_ids)
	{
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
	}

	function get_content_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
	{

	}

	function update_content_object_publication_id($publication_attr)
	{

	}

	function get_content_object_publication_locations($content_object)
	{

	}

	function publish_content_object($content_object, $location)
	{

	}
}
?>