<?php
/**
 * $Id$
 * @package repository
 */

require_once dirname(__FILE__).'/../../common/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../common/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../common/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';

/**
 * This class provides some common methods that are used throughout the
 * repository and sometimes outside it.
 *
 *  @author Tim De Pauw
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
class RepositoryUtilities
{
	const TOOLBAR_DISPLAY_ICON = 1;
	const TOOLBAR_DISPLAY_LABEL = 2;
	const TOOLBAR_DISPLAY_ICON_AND_LABEL = 3;

	private static $us_camel_map = array ();
	private static $camel_us_map = array ();

	/**
	 * Splits a Google-style search query. For example, the query
	 * /"dokeos repository" utilities/ would be parsed into
	 * array('dokeos repository', 'utilities').
	 * @param $pattern The query.
	 * @return array The query's parts.
	 */
	static function split_query($pattern)
	{
		$matches = array();
		preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
		$parts = array ();
		for ($i = 1; $i <= 2; $i ++)
		{
			foreach ($matches[$i] as $m)
			{
				if (!is_null($m) && strlen($m) > 0)
					$parts[] = $m;
			}
		}
		return (count($parts) ? $parts : null);
	}

	/**
	 * Transforms a search string (given by an end user in a search form) to a
	 * Condition, which can be used to retrieve learning objects from the
	 * repository.
	 * @param string $query The query as given by the end user.
	 * @param mixed $properties The learning object properties which should be
	 *                          taken into account for the condition. For
	 *                          example, array('title','type') will yield a
	 *                          Condition which can be used to search for
	 *                          learning objects on the properties 'title' or
	 *                          'type'. By default the properties are 'title'
	 *                          and 'description'. If the condition should
	 *                          apply to a single property, you can pass a
	 *                          string instead of an array.
	 * @return Condition The condition.
	 */
	static function query_to_condition($query, $properties = array (LearningObject :: PROPERTY_TITLE, LearningObject :: PROPERTY_DESCRIPTION))
	{
		if (!is_array($properties))
		{
			$properties = array ($properties);
		}
		$queries = self :: split_query($query);
		if (is_null($queries))
		{
			return null;
		}
		$cond = array ();
		foreach ($queries as $q)
		{
			$q = '*'.$q.'*';
			$pattern_conditions = array ();
			foreach ($properties as $index => $property)
			{
				$pattern_conditions[] = new PatternMatchCondition($property, $q);
			}
			if (count($pattern_conditions) > 1)
			{
				$cond[] = new OrCondition($pattern_conditions);
			}
			else
			{
				$cond[] = $pattern_conditions[0];
			}
		}
		$result = new AndCondition($cond);
		return $result;
	}

	/**
	 * Converts a date/time value retrieved from a FormValidator datepicker
	 * element to the corresponding UNIX itmestamp.
	 * @param string $string The date/time value.
	 * @return int The UNIX timestamp.
	 */
	static function time_from_datepicker($string)
	{
		list ($date, $time) = split(' ', $string);
		list ($year, $month, $day) = split('-', $date);
		list ($hours, $minutes, $seconds) = split(':', $time);
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}

	/**
	 * Orders the given learning objects by their title. Note that the
	 * ordering happens in-place; there is no return value.
	 * @param array $objects The learning objects to order.
	 */
	static function order_learning_objects_by_title($objects)
	{
		usort($objects, array (get_class(), 'by_title'));
	}

	static function order_learning_objects_by_id_desc($objects)
	{
		usort($objects, array (get_class(), 'by_id_desc'));
	}

	/**
	 * Prepares the given learning objects for use as a value for the
	 * element_finder QuickForm element.
	 * @param array $objects The learning objects.
	 * @return array The value.
	 */
	static function learning_objects_for_element_finder($objects)
	{
		$return = array ();
		foreach ($objects as $object)
		{
			$id = $object->get_id();
			$return[$id] = self :: learning_object_for_element_finder($object);
		}
		return $return;
	}

	/**
	 * Prepares the given learning object for use as a value for the
	 * element_finder QuickForm element's value array.
	 * @param LearningObject $object The learning object.
	 * @return array The value.
	 */
	static function learning_object_for_element_finder($object)
	{
		$type = $object->get_type();
		// TODO: i18n
		$date = date('r', $object->get_modification_date());
		$return = array ();
		$return['class'] = 'type type_'.$type;
		$return['title'] = $object->get_title();
		$return['description'] = get_lang(LearningObject :: type_to_class($type).'TypeName').' ('.$date.')';
		return $return;
	}

	/**
	 * Converts the given under_score string to CamelCase notation.
	 * @param string $string The string in under_score notation.
	 * @return string The string in CamelCase notation.
	 */
	static function underscores_to_camelcase($string)
	{
		if (!isset (self :: $us_camel_map[$string]))
		{
			self :: $us_camel_map[$string] = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $string));
		}
		return self :: $us_camel_map[$string];
	}

	/**
	 * Converts the given CamelCase string to under_score notation.
	 * @param string $string The string in CamelCase notation.
	 * @return string The string in under_score notation.
	 */
	static function camelcase_to_underscores($string)
	{
		if (!isset (self :: $camel_us_map[$string]))
		{
			self :: $camel_us_map[$string] = preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $string);
		}
		return self :: $camel_us_map[$string];
	}

	/**
	 * Builds an HTML representation of a toolbar, i.e. a list of clickable
	 * icons. The icon data is passed as an array with the following structure:
	 *
	 *   array(
	 *     array(
	 *       'img'     => '/path/to/icon.gif', # preferably absolute
	 *       'label'   => 'The Label', # no HTML
	 *       'href'    => 'http://the.url.to.point.to/', # null for no link
	 *       'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON,
	 *                      # ... or another constant
	 *       'confirm' => true  # requests confirmation upon clicking
	 *     ),
	 *     # ... more arrays, one per icon
	 *   )
	 *
	 * For the purpose of semantics, the toolbar will be an unordered
	 * list (ul) element. You can pass extra element class names, which allows
	 * you to poke at that element a little, but not at individual icons. If
	 * you wish to style only the label in your stylesheet, you can, as it is
	 * enclosed in a span element. To overcome technical limitations, the icon
	 * gets the class name "labeled" if a label is present. Future versions
	 * may allow positioning the label on either side.
	 * @param array $toolbar_data An array of toolbar elements. See above.
	 * @param mixed $class_names An additional class name. All toolbars have
	 *                           the class name "toolbar", but you may add
	 *                           as much as you like by passing a string or
	 *                           an array of strings here.
	 * @param string $css If you must, you can pass extra CSS for the list
	 *                    element's "style" attribute, but please don't.
	 * @return string The HTML.
	 */
	function build_toolbar($toolbar_data, $class_names = array (), $css = null)
	{
		if (!is_array($class_names))
		{
			$class_names = array ($class_names);
		}
		$class_names[] = 'toolbar';
		$html = array ();
		$html[] = '<ul class="'.implode(' ', $class_names).'"'. (isset ($css) ? ' style="'.$css.'"' : '').'>';
		foreach ($toolbar_data as $index => $elmt)
		{
			$label = (isset ($elmt['label']) ? htmlentities($elmt['label']) : null);
			if (!array_key_exists('display', $elmt))
			{
				$elmt['display'] = self :: TOOLBAR_DISPLAY_ICON;
			}
			$display_label = ($elmt['display'] & self :: TOOLBAR_DISPLAY_LABEL) == self :: TOOLBAR_DISPLAY_LABEL && !empty ($label);
			$button = '';
			if (($elmt['display'] & self :: TOOLBAR_DISPLAY_ICON) == self :: TOOLBAR_DISPLAY_ICON && isset ($elmt['img']))
			{
				$button .= '<img src="'.htmlentities($elmt['img']).'" alt="'.$label.'" title="'.$label.'"'. ($display_label ? ' class="labeled"' : '').'/>';
			}
			if ($display_label)
			{
				$button .= '<span>'.$label.'</span>';
			}
			if (isset ($elmt['href']))
			{
				$button = '<a href="'.htmlentities($elmt['href']).'" title="'.$label.'"'. ($elmt['confirm'] ? ' onclick="return confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\');"' : '').'>'.$button.'</a>';
			}
			$classes = array();
			if ($index == 0)
			{
				$classes[] = 'first';
			}

			if ($index == count($toolbar_data) - 1)
			{
				$classes[] = 'last';
			}
			$html[] = '<li'.(count($classes) ? ' class="'.implode(' ', $classes).'"' : '').'>'.$button.'</li>';
		}
		$html[] = '</ul>';
		// Don't separate by linefeeds. It creates additional whitespace.
		return implode($html);
	}
	/**
	 * Compares learning objects by title.
	 * @param LearningObject $learning_object_1
	 * @param LearningObject $learning_object_2
	 * @return int
	 */
	private static function by_title($learning_object_1, $learning_object_2)
	{
		return strcasecmp($learning_object_1->get_title(), $learning_object_2->get_title());
	}

	private static function by_id_desc($learning_object_1, $learning_object_2)
	{
		return ($learning_object_1->get_id() < $learning_object_2->get_id() ? 1 : -1);
	}

	/**
	 * Checks if a file is an HTML document.
	 */
	// TODO: SCARA - MOVED / FROM: document_form_class / TO: RepositoryUtilities or some other relevant class.
	function is_html_document($path)
	{
		return (preg_match('/\.x?html?$/', $path) === 1);
	}

	function build_uses($publication_attr)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$udm = UsersDataManager :: get_instance();
		$html 	= array ();
		$html[] = '<div class="publications">';
		$html[] = '<div class="publications_title">'.htmlentities(get_lang('ThisObjectIsPublished')).'</div>';
		$html[] = '<ul class="publications_list">';
		foreach ($publication_attr as $info)
		{
			$publisher = $udm->retrieve_user($info->get_publisher_user_id());
			$object = $rdm->retrieve_learning_object($info->get_publication_object_id());
			$html[] = '<li>';
			// TODO: i18n
			// TODO: SCARA - Find cleaner solution to display Learning Object title + url
			if ($info->get_url())
			{
				$html[] = '<a href="'.$info->get_url(). '">' . $info->get_application().': '.$info->get_location().'</a>';
			}
			else
			{
				$html[] = $info->get_application().': '.$info->get_location();
			}
			$html[] = ' > <a href="'. $object->get_view_url() .'">'. $object->get_title() .'</a> ('.$publisher->get_firstname().' '.$publisher->get_lastname().', '.date('r', $info->get_publication_date()).')';
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';

		return implode($html);
	}

	function build_block_hider($type, $id = null, $message = null, $extra = null)
	{
		$html = array();

		if ($type == 'script')
		{
			$html[]   = '<script language="JavaScript" type="">';
			$html[]  .= 'function showElement(item)';
			$html[]  .= '{';
			$html[]  .= '	if (document.getElementById(item).style.display == \'block\')';
			$html[]  .= '  {';
			$html[]  .= '		document.getElementById(item).style.display = \'none\';';
			$html[]  .= '		document.getElementById(\'plus-\'+item).style.display = \'inline\';';
			$html[]  .= '		document.getElementById(\'minus-\'+item).style.display = \'none\';';
			$html[]  .= '  }';
			$html[]  .= '	else';
			$html[]  .= '  {';
			$html[]  .= '		document.getElementById(item).style.display = \'block\';';
			$html[]  .= '		document.getElementById(\'plus-\'+item).style.display = \'none\';';
			$html[]  .= '		document.getElementById(\'minus-\'+item).style.display = \'inline\';';
			$html[]  .= '		document.getElementById(item).value = \'Version comments here ...\';';
			$html[]  .= '	}';
			$html[]  .= '}';
			$html[]  .= '</script>';
		}
		elseif($type == 'begin')
		{
			$show_message = 'Show' . $message;
			$hide_message = 'Hide' . $message;

			$html[]    = '<div id="plus-'.$id.'"><a href="javascript:showElement(\''. $id .'\')">'. get_lang('Show' . $message) .'</a></div>';
			$html[]    = '<div id="minus-'.$id.'" style="display: none;"><a href="javascript:showElement(\''. $id .'\')">'. get_lang('Hide' . $message) .'</a></div>';
			$html[]   .= '<div id="'. $id .'" style="display: none; clear: both;">';
		}
		elseif($type == 'end')
		{
			$html[]   = '</div>';
		}

		return implode("\n", $html);
	}

	// 2 simple functions to display an array, a bit prettier as print_r
	// for testing purposes only!
	// @author Dieter De Neef
	function DisplayArray ($array)
		{
		$depth = 0;
		if (is_array($array))
		{
			echo "Array (<br />";
			for ($i = 0; $i < count($array); $i++)
			{
				if (is_array($array[$i]))
				{
					DisplayInlineArray($array[$i], $depth +1, $i);
				}
				else
				{
					echo "[" .$i. "] => " .$array[$i];
					echo "<br />";
					$depth = 0;
				}
			}
			echo ")<br />";
		}
		else
		{
			echo "Variabele is geen array";
		}
	}

	function DisplayInlineArray($inlinearray, $depth, $element)
	{
		$spaces = null;
		for ($j = 0; $j < $depth - 1; $j++)
		{
			$spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		echo $spaces. "[".$element. "]" . "Array (<br />";
		$spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		for ($i = 0; $i < count($inlinearray); $i++)
		{
			$key = key($inlinearray);
			if (is_array($inlinearray[$i]))
			{
				DisplayInlineArray($inlinearray[$i], $depth +1, $i);
			}
			else
			{
				echo $spaces ."[" .$key. "] => " .$inlinearray[$key];
				echo "<br />";
			}
			next($inlinearray);
		}
		echo $spaces .")<br />";
	}

}
?>