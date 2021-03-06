<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
require_once path :: get_menu_path() . 'lib/menu_data_manager.class.php';
require_once path :: get_menu_path() . 'lib/navigation_item.class.php';

class PackageInstallerApplicationType extends PackageInstallerType
{

    function install()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('ApplicationDependenciesVerified'));
            $installer = Installer :: factory($application_name, array());

            if (! $installer->install())
            {
                return $this->get_parent()->installation_failed('initilization', Translation :: get('ApplicationInitilizationFailed'));
            }
            else
            {
                $this->add_message($installer->retrieve_message());
                $this->installation_successful('initilization');
            }

            $installer->set_message(array());

            if (! $installer->post_process())
            {
                return $this->get_parent()->installation_failed('processing', Translation :: get('ApplicationPostProcessingFailed'));
            }
            else
            {
                $this->add_message($installer->retrieve_message());
                $this->installation_successful('processing');
            }

            if (! $this->set_version())
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationVersionNotSet'), PackageInstaller :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationVersionSet'));
            }

            if (! $this->add_navigation_item())
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationMenuItemNotAdded'), PackageInstaller :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationMenuItemAdded'));
            }
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }

        $source->cleanup();

        return true;
    }

    function set_version()
    {

        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $application_name);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);

        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), 0, 1);
        $registration = $registrations->next_result();
        $registration->set_version($attributes->get_version());
        return $registration->update();
    }

    function add_navigation_item()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        $navigation_item = new NavigationItem();
        $navigation_item->set_title(DokeosUtilities :: underscores_to_camelcase_with_spaces($application_name));
        $navigation_item->set_application($application_name);
        $navigation_item->set_section($application_name);
        $navigation_item->set_category(0);
        return $navigation_item->create();
    }
}
?>