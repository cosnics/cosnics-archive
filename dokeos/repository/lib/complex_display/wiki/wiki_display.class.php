<?php
/**
 * $Id: wiki_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Wiki tool
 * @package application.weblcms.tool
 * @subpackage wiki
 */

require_once dirname(__FILE__).'/wiki_display_component.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/tool/wiki/wiki_tool_component.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiDisplay extends ComplexDisplay
{
    const PARAM_WIKI_ID = 'wiki_id';
    const PARAM_WIKI_PAGE_ID = 'wiki_page_id';

	const ACTION_BROWSE_WIKIS = 'browse';
	const ACTION_VIEW_WIKI = 'view';
	const ACTION_VIEW_WIKI_PAGE = 'view_item';
    const ACTION_PUBLISH = 'publish';
    const ACTION_CREATE_PAGE = 'create_page';
    const ACTION_SET_AS_HOMEPAGE = 'set_as_homepage';
    const ACTION_DELETE_WIKI_CONTENTS = 'delete_wiki_contents';
    const ACTION_DISCUSS = 'discuss';
    const ACTION_HISTORY = 'history';
    const ACTION_PAGE_STATISTICS = 'page_statistics';
    const ACTION_COMPARE = 'compare';
    const ACTION_STATISTICS = 'statistics';
    const ACTION_LOCK = 'lock';
    const ACTION_ADD_LINK = 'add_wiki_link';




	/**
	 * Inherited.
	 */
	function run()
	{
        //wiki tool
        $action = Request :: get('tool_action');
		

		switch ($action)
		{
			case self :: ACTION_BROWSE_WIKIS :
				$component = WikiDisplayComponent :: factory('WikiBrowser', $this);
				break;
			case self :: ACTION_VIEW_WIKI :
				$component = WikiDisplayComponent :: factory('WikiViewer', $this);
				break;
			case self :: ACTION_VIEW_WIKI_PAGE :
				$component = WikiDisplayComponent :: factory('WikiItemViewer', $this);
				break;
            case self :: ACTION_PUBLISH :
				$component = WikiDisplayComponent :: factory('WikiPublisher', $this);
				break;
            case self :: ACTION_CREATE_PAGE :
                $component = WikiDisplayComponent :: factory('WikiPageCreator', $this);
                break;
            case self :: ACTION_SET_AS_HOMEPAGE :
                $component = WikiDisplayComponent :: factory('WikiHomepageSetter', $this);
                break;
            case self :: ACTION_LOCK :
                $component = WikiDisplayComponent :: factory('WikiLocker', $this);
                break;
            case self :: ACTION_DELETE_WIKI_CONTENTS :
                $component = WikiDisplayComponent :: factory('WikiContentsDeleter', $this);
                break;
            case self :: ACTION_DISCUSS :
                $component = WikiDisplayComponent :: factory('WikiDiscuss', $this);
                break;
            case self :: ACTION_HISTORY :
                $component = WikiDisplayComponent :: factory('WikiHistory', $this);
                break;
            case self :: ACTION_PAGE_STATISTICS :
                $component = WikiDisplayComponent :: factory('WikiPageStatisticsViewer', $this);
                break;
            case self :: ACTION_STATISTICS :
                $component = WikiDisplayComponent :: factory('WikiStatisticsViewer', $this);
                break;
            case self :: ACTION_ADD_LINK :
                $component = WikiDisplayComponent :: factory('', 'WikiLinkCreator', $this);
				break;
			default :
				$component = WikiDisplayComponent :: factory('WikiBrowser', $this);
		}
		$component->run();
	}

	static function get_allowed_types()
	{
		return array('wiki');
	}

    static function is_wiki_locked($wiki_id)
    {
        $wiki = RepositoryDataManager :: get_instance()->retrieve_learning_object($wiki_id);
        return $wiki->get_locked()==1;
    }

    static function get_wiki_homepage($wiki_id)
    {
        require_once Path :: get_repository_path() .'/lib/learning_object/wiki_page/complex_wiki_page.class.php';
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_PARENT,$wiki_id);
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE,1);
        $wiki_homepage = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new AndCondition($conditions),array (),array (), 0, -1, 'complex_wiki_page')->as_array();
        return $wiki_homepage[0];
    }

}
?>