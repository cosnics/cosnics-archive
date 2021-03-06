<?php

require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_view_form.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class RepositoryManagerUserViewUpdaterComponent extends RepositoryManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository userviews');
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS)), Translation :: get('UserViewList')));

        $id = Request :: get(RepositoryManager :: PARAM_USER_VIEW);
        if ($id)
        {
            $user_view = $this->retrieve_user_views(new EqualityCondition(UserView :: PROPERTY_ID,$id))->next_result();
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_USER_VIEW => $id)), $user_view->get_name()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_USER_VIEW => $id)), Translation :: get('Update')));

            if (!$this->get_user()->is_platform_admin())
            {
                $this->display_header($trail, false, true);
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit;
            }

            $form = new UserViewForm(UserViewForm :: TYPE_EDIT, $user_view, $this->get_url(array(RepositoryManager :: PARAM_USER_VIEW => $id)), $this->get_user());

            if($form->validate())
            {
                $success = $form->update_user_view();
                $user_view = $form->get_user_view();
                $this->redirect(Translation :: get($success ? 'UserViewUpdated' : 'UserViewNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_USER_VIEWS));
            }
            else
            {
                $this->display_header($trail, false, true);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoUserViewSelected')));
        }
    }
}
?>