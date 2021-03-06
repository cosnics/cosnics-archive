<?php

require_once Path :: get_repository_path() . 'lib/content_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_content_object_item_form.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';

class ForumDisplayForumPostQuoterComponent extends ForumDisplayComponent
{
    function run()
    {
        if($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get('pid');
            $cid = Request :: get('cid');

            $quote = Request :: get('quote');

            if(!$pid || !$cid || !$quote)
            {
            //trail here
                $this->display_error_message(Translation :: get('NoParentSelected'));
            }

            $rdm = RepositoryDataManager :: get_instance();

            $quote_item = $rdm->retrieve_complex_content_object_item($quote);
            $quote_lo = $rdm->retrieve_content_object($quote_item->get_ref());

            $content_object = new AbstractContentObject('forum_post', $this->get_user_id());
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $content_object, 'create', 'post', $this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_QUOTE_FORUM_POST,'pid' => $pid, 'cid' => $cid, 'quote' => $quote)));

            if(substr($quote_lo->get_title(), 0, 3) == 'RE:')
                $reply = $quote_lo->get_title();
            else
                $reply = 'RE: ' . $quote_lo->get_title();

            $defaults['title'] = $reply;
            $defaults['description'] = '[quote="' . UserDataManager :: get_instance()->retrieve_user($quote_lo->get_owner_id())->get_fullname() . '"]' . $quote_lo->get_description() . '[/quote]';

            $form->setParentDefaults($defaults);

            if($form->validate())
            {
                $object = $form->create_content_object();
                $cloi = ComplexContentObjectItem :: factory('forum_post');

                $item = $rdm->retrieve_complex_content_object_item($cid);

                $cloi->set_ref($object->get_id());
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent($item->get_ref());
                $cloi->set_display_order($rdm->select_next_display_order($item->get_ref()));

                if($quote)
                    $cloi->set_reply_on_post($quote);

                $cloi->create();
                $this->my_redirect($pid, $cid);
            }
            else
            {
            //trail here
                $form->display();
            }

        }
    }

    private function my_redirect($pid, $cid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));

        $params = array();
        $params['pid'] = $pid;
        $params['cid'] = $cid;
        $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_TOPIC;

        $this->redirect($message, false, $params);
    }

}
?>