<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
require_once dirname(__FILE__).'/portfolio_manager_component.class.php';
require_once dirname(__FILE__).'/../portfolio_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';

/**
 * A portfolio manager
 * @author Sven Vanpoucke
 */
 class PortfolioManager extends WebApplication
 {
 	const APPLICATION_NAME = 'portfolio';

	const PARAM_PORTFOLIO_PUBLICATION = 'portfolio_publication';
	const PARAM_USER_ID = 'user_id';
	const PARAM_DELETE_SELECTED_PORTFOLIO_PUBLICATIONS = 'delete_selected_portfolio_publications';

	const ACTION_DELETE_PORTFOLIO_PUBLICATION = 'delete_portfolio_publication';
	const ACTION_EDIT_PORTFOLIO_PUBLICATION = 'edit_portfolio_publication';
	const ACTION_CREATE_PORTFOLIO_PUBLICATION = 'create_portfolio_publication';
	const ACTION_VIEW_PORTFOLIO = 'view_portfolio';
	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function PortfolioManager($user = null)
    {
    	parent :: __construct($user);
    }

    /**
	 * Run this portfolio manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_VIEW_PORTFOLIO :
				$component = PortfolioManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_DELETE_PORTFOLIO_PUBLICATION :
				$component = PortfolioManagerComponent :: factory('PortfolioPublicationDeleter', $this);
				break;
			case self :: ACTION_EDIT_PORTFOLIO_PUBLICATION :
				$component = PortfolioManagerComponent :: factory('PortfolioPublicationUpdater', $this);
				break;
			case self :: ACTION_CREATE_PORTFOLIO_PUBLICATION :
				$component = PortfolioManagerComponent :: factory('PortfolioPublicationCreator', $this);
				break;
			case self :: ACTION_BROWSE:
				$component = PortfolioManagerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = PortfolioManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_portfolio_publications($condition)
	{
		return PortfolioDataManager :: get_instance()->count_portfolio_publications($condition);
	}

	function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publications($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_portfolio_publication($id)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($id);
	}

	function count_portfolio_publication_groups($condition)
	{
		return PortfolioDataManager :: get_instance()->count_portfolio_publication_groups($condition);
	}

	function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_groups($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_portfolio_publication_group($id)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_group($id);
	}

	function count_portfolio_publication_users($condition)
	{
		return PortfolioDataManager :: get_instance()->count_portfolio_publication_users($condition);
	}

	function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_users($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_portfolio_publication_user($id)
	{
		return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_user($id);
	}

	// Url Creation

	function get_create_portfolio_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PORTFOLIO_PUBLICATION));
	}

	function get_update_portfolio_publication_url($portfolio_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PORTFOLIO_PUBLICATION,
								    self :: PARAM_PORTFOLIO_PUBLICATION => $portfolio_publication->get_id()));
	}

 	function get_delete_portfolio_publication_url($portfolio_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PORTFOLIO_PUBLICATION,
								    self :: PARAM_PORTFOLIO_PUBLICATION => $portfolio_publication->get_id()));
	}

	function get_view_portfolio_url($user)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PORTFOLIO,
									self :: PARAM_USER_ID => $user));
	}
	
	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function learning_object_is_published($object_id)
	{
	}

	function any_learning_object_is_published($object_ids)
	{
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
	}

	function get_learning_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_learning_object_publications($object_id)
	{

	}

	function update_learning_object_publication_id($publication_attr)
	{

	}

	function get_learning_object_publication_locations($learning_object)
	{

	}

	function publish_learning_object($learning_object, $location)
	{

	}
}
?>