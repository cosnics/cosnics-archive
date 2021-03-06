<?php
/**
 * @package repository.learningobject
 * @subpackage youtube
 */
class YoutubeDisplay extends ContentObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$object = $this->get_content_object();
		
		$video_url = $object->get_url();
		$video_url_components = parse_url($video_url);
		$video_query_components = Text :: parse_query_string($video_url_components['query']);
		
		$video_element = '<embed style="margin-bottom: 1em;" height="'. $object->get_height() .'" width="'. $object->get_width() .'" type="application/x-shockwave-flash" src="http://www.youtube.com/v/'. $video_query_components['v'] .'"></embed>';
		
		return str_replace(self::DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">'. $video_element .'<br/><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_url()).'</a></div>' . self::DESCRIPTION_MARKER, $html);
	}
	function get_short_html()
	{
		$object = $this->get_content_object();
		return '<span class="content_object"><a target="about:blank" href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>