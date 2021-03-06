<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
class PackageManagerDeactivatorComponent extends PackageManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Install')));
        $trail->add_help('administration install');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $ids = Request :: get(PackageManager :: PARAM_REGISTRATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $registration = $this->get_parent()->retrieve_registration($id);
                
                $registration->toggle_status();
                if (! $registration->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRegistrationNotDeactivated';
                }
                else
                {
                    $message = 'SelectedRegistrationsNotDeactivated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRegistrationDeactivated';
                }
                else
                {
                    $message = 'SelectedRegistrationsDeactivated';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_MANAGE_PACKAGES, PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoRegistrationSelected')));
        }
    }
}
?>