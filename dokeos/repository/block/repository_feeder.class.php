<?php
require_once dirname(__FILE__).'/../lib/repository_block.class.php';
require_once dirname(__FILE__).'/../lib/content_object_display.class.php';

class RepositoryFeeder extends RepositoryBlock
{
	/**
	 * Runs this component and displays its output.
	 * This component is only meant for use within the home-component and not as a standalone item.
	 */
	function run()
	{
		return $this->as_html();
	}

	function as_html()
	{
		$configuration = $this->get_configuration();
		$object_id = $configuration['use_object'];

		$html = array();

		if (!isset($object_id) || $object_id == 0)
		{
		    $html[] = $this->display_header();
			$html[] = Translation :: get('ConfigureBlockFirst');
		}
		else
		{
			$content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($configuration['use_object']);
			$display = ContentObjectDisplay :: factory($content_object);

			$feed = $display->parse_file($content_object->get_url());
			$html[] = $this->display_header($content_object);
			if($feed)
			{
			    $html[] = '<div class="tool_menu">';
				$html[] = '<ul>';
				foreach ($feed['items'] as $item)
				{
					$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'treemenu_types/rss_feed_item.png)"><a href="'.htmlentities($item['link']).'">'. $item['title'] .'</a></li>';
				}
				$html[] = '</ul>';
        		$html[] = '<div class="clear"></div>';
        		$html[] = '</div>';
			}
			else
			{
				$html[] = Translation :: get('CanNotConnectToFeed');
			}
		}
		$html[] = $this->display_footer();

		return implode("\n", $html);
	}

    function display_header($content_object = null)
    {
        $html = array();

        $html[] = '<div class="block" id="block_' . $this->get_block_info()->get_id() . '" style="background-image: url(' . Theme :: get_image_path() . 'block_rss_feed.png);">';
        $html[] = $this->display_title($content_object);
        $html[] = '<div class="description"' . ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') . '>';

        return implode("\n", $html);
    }

    function display_title($content_object = null)
    {
        if (!$content_object)
        {
            $title = Translation :: get('Newsfeed');
        }
        else
        {
            $title = $content_object->get_title();
        }

        $html = array();

        $html[] = '<div class="title"><div style="float: left;">' . $title . '</div>';
        $html[] = $this->display_actions();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>