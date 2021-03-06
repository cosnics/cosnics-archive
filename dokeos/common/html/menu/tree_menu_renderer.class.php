<?php
/**
 * $Id$
 * @package repository
 */
require_once 'HTML/Menu/DirectTreeRenderer.php';
require_once Path :: get_library_path() . 'resource_manager.class.php';

/**
 * Renderer which can be used to include a tree menu on your page.
 * @author Bart Mollet
 * @author Tim De Pauw
 */
class TreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
    /**
     * Boolean to check if this tree menu is allready initialized
     */
    private static $initialized;

    /**
     * Constructor.
     */
    function TreeMenuRenderer()
    {
        //$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVE => '<!--A--><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>', HTML_MENU_ENTRY_ACTIVEPATH => '<!--P--><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a>');
        $entryTemplates = array();
        $entryTemplates[HTML_MENU_ENTRY_INACTIVE] = '<div class="{children}"><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></div>';
        $entryTemplates[HTML_MENU_ENTRY_ACTIVE] = '<!--A--><div><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></div>';
        $entryTemplates[HTML_MENU_ENTRY_ACTIVEPATH] = '<!--P--><div><a href="{url}" onclick="{onclick}" id="{id}" class="{class}">{title}</a></div>';
        $this->setEntryTemplate($entryTemplates);
        $this->setItemTemplate('<li>', '</li>' . "\n");
    }

    /**
     * Finishes rendering a level in the tree menu
     * @see HTML_Menu_DirectTreeRenderer::finishLevel
     */
    function finishLevel($level)
    {
        $root = ($level == 0);
        if ($root)
        {
            $this->setLevelTemplate('<ul class="tree-menu">' . "\n", '</ul>' . "\n");
        }
        parent :: finishLevel($level);
        if ($root)
        {
            $this->setLevelTemplate('<ul>' . "\n", '</ul>' . "\n");
        }
    }

    /**
     * Renders an entry in the tree menu
     * @see HTML_Menu_DirectTreeRenderer::renderEntry
     */
    function renderEntry($node, $level, $type)
    {
        // Add some extra keys, so they always get replaced in the template.
        foreach (array('children', 'class', 'onclick', 'id') as $key)
        {
            if (! array_key_exists($key, $node))
            {
                $node[$key] = '';
            }
        }

        parent :: renderEntry($node, $level, $type);
    }

    /**
     * Gets a HTML representation of the tree menu
     * @return string
     */
    function toHtml()
    {
        $html = parent :: toHtml();
        $class = array('A' => 'current', 'P' => 'current_path');
        $html = preg_replace('/(?<=<li)><!--([AP])-->/e', '\' class="\'.$class[\1].\'">\'', $html);
        $html = preg_replace('/\s*\b(onclick|id)="\s*"\s*/', ' ', $html);

        if (self :: $initialized)
        {
            return $html;
        }
        self :: $initialized = true;
        //return ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH).'javascript/treemenu.js') . $html;
        return ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/tree_menu.js') . $html;
    }
}
?>