<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/portfolio_builder_component.class.php';

class PortfolioBuilder extends ComplexBuilder
{
	const ACTION_CREATE_LP_ITEM = 'create_item';
	
	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = PortfolioBuilderComponent :: factory('Browser', $this); 
				break;
			case PortfolioBuilder :: ACTION_CREATE_LP_ITEM :
				$component = PortfolioBuilderComponent :: factory('ItemCreator', $this); 
				break;	
			case self :: ACTION_DELETE_CLOI :
				$component = PortfolioBuilderComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_UPDATE_CLOI :
				$component = PortfolioBuilderComponent :: factory('Updater', $this);
				break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>