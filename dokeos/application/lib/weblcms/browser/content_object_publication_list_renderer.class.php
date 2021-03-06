<?php
/**
 * $Id: learning_object_publication_list_renderer.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package application.weblcms
 * @subpackage browser
 */
require_once Path :: get_repository_path(). 'lib/content_object_display.class.php';
/**
 * This is a generic renderer for a set of learning object publications.
 * @package application.weblcms.tool
 * @author Bart Mollet
 * @author Tim De Pauw
 */
abstract class ContentObjectPublicationListRenderer
{
    protected $browser;

    private $parameters;

    private $actions;

    /**
     * Constructor.
     * @param PublicationBrowser $browser The browser to associate this list
     *                                    renderer with.
     * @param array $parameters The parameters to pass to the renderer.
     */
    function ContentObjectPublicationListRenderer($browser, $parameters = array (), $actions)
    {
        $this->parameters = $parameters;
        $this->browser = $browser;
    }

    function get_actions()
    {
        return $this->actions;
    }

    function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * Renders the title of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_title($publication)
    {
        return htmlspecialchars($publication->get_content_object()->get_title());
    }

    /**
     * Renders the description of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_description($publication)
    {
        return $publication->get_content_object()->get_description();
    }

    /**
     * Renders information about the repo_viewer of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_repo_viewer($publication)
    {
        $user = $this->browser->get_user_info($publication->get_publisher_id());
        return $user->get_firstname().' '.$user->get_lastname();
    }

    /**
     * Renders the date when the given publication was published.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_date($publication)
    {
        return $this->format_date($publication->get_publication_date());
    }

    /**
     * Renders the users and course_groups the given publication was published for.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_targets($publication)
    {
        if($publication->is_email_sent())
        {
            $email_suffix = ' - <img src="'.Theme :: get_common_image_path().'action_email.png" alt="" style="vertical-align: middle;"/>';
        }
        if ($publication->is_for_everybody())
        {
            return htmlentities(Translation :: get('Everybody')).$email_suffix;
        }
        else
        {
            $users = $publication->get_target_users();            
            $course_groups = $publication->get_target_course_groups();
            if(count($users) + count($course_groups) == 1)
            {
                if(count($users) == 1)
                {
                    $user = $this->browser->get_user_info($users[0]);
                    return $user->get_firstname().' '.$user->get_lastname().$email_suffix;
                }
                else
                {
                    $wdm = WeblcmsDatamanager::get_instance();
                    $course_group = $wdm->retrieve_course_group($course_groups[0]);
                    return $course_group->get_name();
                }
            }
            $target_list = array ();
            $target_list[] = '<select>';
            foreach ($users as $index => $user_id)
            {
                $user = $this->browser->get_user_info($user_id);
                $target_list[] = '<option>'.$user->get_firstname().' '.$user->get_lastname().'</option>';
            }
            foreach ($course_groups as $index => $course_group_id)
            {
                $wdm = WeblcmsDatamanager::get_instance();
                //Todo: make this more efficient. Get all course_groups using a single query
                $course_group = $wdm->retrieve_course_group($course_group_id);
                $target_list[] = '<option>'.$course_group->get_name().'</option>';
            }
            $target_list[] = '</select>';
            return implode("\n", $target_list).$email_suffix;
        }
    }

    /**
     * Renders the time period in which the given publication is active.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_period($publication)
    {
        if ($publication->is_forever())
        {
            return htmlentities(Translation :: get('Forever'));
        }
        return htmlentities(Translation :: get('From').' '.$this->format_date($publication->get_from_date()).' '.Translation :: get('Until').' '.$this->format_date($publication->get_to_date()));
    }

    /**
     * Renders general publication information about the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_information($publication)
    {
        $repo_viewer = $this->browser->get_user_info($publication->get_publisher_id());
        $html = array ();
        $html[] = htmlentities(Translation :: get('PublishedOn')).' '.$this->render_publication_date($publication);
        $html[] = htmlentities(Translation :: get('By')).' '.$this->render_repo_viewer($publication);
        $html[] = htmlentities(Translation :: get('For')).' '.$this->render_publication_targets($publication);
        if (!$publication->is_forever())
        {
            $html[] = '('.$this->render_publication_period($publication).')';
        }
        return implode("\n", $html);
    }

    /**
     * Renders the means to move the given publication up one place.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $first True if the publication is the first in the list
     *                       it is a part of.
     * @return string The HTML rendering.
     */
    function render_up_action($publication, $first = false)
    {
        if (!$first)
        {
            $up_img = 'action_up.png';
            $up_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_UP, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
            $up_link = '<a href="'.$up_url.'"><img src="'.Theme :: get_common_image_path().$up_img.'" alt=""/></a>';
        }
        else
        {
            $up_link = '<img src="'.Theme :: get_common_image_path().'action_up_na.png"  alt=""/>';
        }
        return $up_link;
    }

    /**
     * Renders the means to move the given publication down one place.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $last True if the publication is the last in the list
     *                      it is a part of.
     * @return string The HTML rendering.
     */
    function render_down_action($publication, $last = false)
    {
        if (!$last)
        {
            $down_img = 'action_down.png';
            $down_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_DOWN, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
            $down_link = '<a href="'.$down_url.'"><img src="'.Theme :: get_common_image_path().$down_img.'"  alt=""/></a>';
        }
        else
        {
            $down_link = '<img src="'.Theme :: get_common_image_path().'action_down_na.png"  alt=""/>';
        }
        return $down_link;
    }

    /**
     * Renders the means to toggle visibility for the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_visibility_action($publication)
    {
        $visibility_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        if($publication->is_hidden())
        {
            $visibility_img = 'action_invisible.png';
        }
        elseif($publication->is_forever())
        {
            $visibility_img = 'action_visible.png';
        }
        else
        {
            $visibility_img = 'action_period.png';
            $visibility_url = 'javascript:void(0)';
        }
        $visibility_link = '<a href="'.$visibility_url.'"><img src="'.Theme :: get_common_image_path().$visibility_img.'"  alt=""/></a>';
        return $visibility_link;
    }

    /**
     * Renders the means to edit the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_edit_action($publication)
    {
        $edit_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        $edit_link = '<a href="'.$edit_url.'"><img src="'.Theme :: get_common_image_path().'action_edit.png"  alt=""/></a>';
        return $edit_link;
    }

    function render_top_action($publication)
    {
        return '<a href="#top"><img src="'.Theme :: get_common_image_path().'action_ajax_add.png"  alt=""/></a>';
    }

    /**
     * Renders the means to delete the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_delete_action($publication)
    {
        $delete_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        $delete_link = '<a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_image_path().'action_delete.png"  alt=""/></a>';
        return $delete_link;
    }

    /**
     * Renders the means to give feedback to the given publication
     * @param ContentObjectPublication $publication The publication
     *
     */
    function render_feedback_action($publication)
    {
        $feedback_url = $this->get_url(array (Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'), array(), true);
        $feedback_link = '<a href="'.$feedback_url.'"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt=""/></a>';
        return $feedback_link;
    }

    /**
     * Renders the means to move the given publication to another category.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_move_to_category_action($publication)
    {
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->browser->get_parent()->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->browser->get_parent()->get_tool_id());
        $count = WeblcmsDataManager :: get_instance()->count_content_object_publication_categories(new AndCondition($conditions));
        $count++;
        if($count > 1)
        {
            $url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
            $link = '<a href="'.$url.'"><img src="'.Theme :: get_common_image_path().'action_move.png"  alt=""/></a>';
        }
        else
        {
            $link = '<img src="'.Theme :: get_common_image_path().'action_move_na.png"  alt=""/>';
        }
        return $link;
    }

    /**
     * Renders the attachements of a publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The rendered HTML.
     */
    /*function render_attachments($publication)
    {
        $object = $publication->get_content_object();
        if ($object->supports_attachments())
        {
            $attachments = $object->get_attached_content_objects();
            if(count($attachments)>0)
            {
                $html[] = '<h4>Attachments</h4>';
                DokeosUtilities :: order_content_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $disp = ContentObjectDisplay :: factory($attachment);
                    $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path().'content_object/'.$attachment->get_icon_name().$icon_suffix.'.png);">';
                    $html[] = '<div class="title">';
                    $html[] = $attachment->get_title();
                    $html[] = '</div>';
                    $html[] = '<div class="description">';
                    $html[] = $attachment->get_description();
                    $html[] = '</div></div>';
                    //$html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
                }
                //$html[] = '</ul>';
                return implode("\n",$html);
            }
        }
        return '';
    }*/

    function render_attachments($publication)
    {
        $object = $publication->get_content_object();
        if ($object->supports_attachments())
        {
            $attachments = $object->get_attached_content_objects();
            if(count($attachments)>0)
            {
                $html[] = '<h4>Attachments</h4>';
                DokeosUtilities :: order_content_objects_by_title($attachments);
                $html[] = '<ul>';
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li><a href="' . $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_ATTACHMENT, Tool :: PARAM_OBJECT_ID => $attachment->get_id())) . '"><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$attachment->get_title().'</a></li>';
                }
                $html[] = '</ul>';
                return implode("\n",$html);
            }
        }
        return '';
    }

    /**
     * Renders publication actions for the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $first True if the publication is the first in the list
     *                       it is a part of.
     * @param boolean $last True if the publication is the last in the list
     *                      it is a part of.
     * @return string The rendered HTML.
     */
    function render_publication_actions($publication,$first,$last)
    {
        $html = array();
        $icons = array();

        $html[] = '<span style="white-space: nowrap;">';

        if ($this->is_allowed(DELETE_RIGHT))
        {
            $icons[] = $this->render_delete_action($publication);
        }
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $icons[] = $this->render_edit_action($publication);
            $icons[] = $this->render_visibility_action($publication);
            $icons[] = $this->render_up_action($publication,$first);
            $icons[] = $this->render_down_action($publication,$last);
            $icons[] = $this->render_move_to_category_action($publication,$last);
        }

        $icons[] = $this->render_feedback_action($publication);

        //dump($icons);
        $html[] = implode('&nbsp;', $icons);
        $html[] = '</span>';
        return implode($html);
    }

    /**
     * Renders the icon for the given publication
     * @param ContentObjectPublication $publication The publication.
     * @return string The rendered HTML.
     */
    function render_icon($publication)
    {
        $object = $publication->get_content_object();
        return '<img src="'.Theme :: get_common_image_path() . 'content_object/' .$object->get_icon_name().'.png" alt=""/>';
    }

    /**
     * Formats the given date in a human-readable format.
     * @param int $date A UNIX timestamp.
     * @return string The formatted date.
     */
    function format_date($date)
    {
        $date_format = Translation :: get('dateTimeFormatLong');
        return Text :: format_locale_date($date_format,$date);
    }

    /**
     * @see ContentObjectPublicationBrowser :: get_publications()
     */
    function get_publications()
    {
        return $this->browser->get_publications();
    }


    /**
     * @see ContentObjectPublicationBrowser :: get_publication_count()
     */
    function get_publication_count()
    {
        return $this->browser->get_publication_count();
    }



    /**
     * Returns the value of the given renderer parameter.
     * @param string $name The name of the parameter.
     * @return mixed The value of the parameter.
     */
    function get_parameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Sets the value of the given renderer parameter.
     * @param string $name The name of the parameter.
     * @param mixed $value The new value for the parameter.
     */
    function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns the output of the list renderer as HTML.
     * @return string The HTML.
     */
    abstract function as_html();

    function get_feedback()
    {
        if($this->browser->get_parent()->get_course()->get_allow_feedback())
        {
            $fbm = new FeedbackManager($this->browser->get_parent());
            return $fbm->as_html();
        }
    }

    /**
     * @see ContentObjectPublicationBrowser :: get_url()
     */
    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->browser->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @see ContentObjectPublicationBrowser :: is_allowed()
     */
    function is_allowed($right)
    {
        return $this->browser->is_allowed($right);
    }
    /**
     *
     */
    protected function object2color($object)
    {
        $color_number = substr(ereg_replace('[0a-zA-Z]','',md5(serialize($object))),0,9);
        $rgb = array();
        $rgb['r'] = substr($color_number,0,3)%255;
        $rgb['g'] = substr($color_number,2,3)%255;
        $rgb['b'] = substr($color_number,4,3)%255;

        $rgb['fr'] = round(($rgb['r'] + 234) / 2);
        $rgb['fg'] = round(($rgb['g'] + 234) / 2);
        $rgb['fb'] = round(($rgb['b'] + 234) / 2);

        return $rgb;
    }
}
?>