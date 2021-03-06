<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . '/lib/remote_package.class.php';

class PackageManagerSynchroniserComponent extends PackageManagerComponent
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

        $data = $this->get_remote_packages_data();

        if ($data)
        {
            if ($this->parse_remote_packages_data($data))
            {
                $message = 'RemotePackagesListSynchronised';
                $failures = 0;
            }
            else
            {
                $message = 'RemotePackagesDataError';
                $failures = 1;
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_MANAGE_PACKAGES, PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_REMOTE_PACKAGE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoValidRemotePackagesList')));
        }
    }

    function get_remote_packages_data()
    {
        $xml_data = file_get_contents(Path :: get(WEB_PATH) . 'packages.xml');

        if ($xml_data)
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('package', 'dependency'));

            // userialize the document
            $status = $unserializer->unserialize($xml_data);

            if (PEAR :: isError($status))
            {
                $this->display_error_page($status->getMessage());
            }
            else
            {
                return $unserializer->getUnserializedData();
            }
        }
    }

    function parse_remote_packages_data($data)
    {
        $adm = AdminDataManager :: get_instance();

        foreach ($data['package'] as $package)
        {
            $package['dependencies'] = serialize($package['dependencies']);

            $condition = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $package['code']);
            $remote_packages = $adm->retrieve_remote_packages($condition, array(), 0);
            if ($remote_packages->size() === 1)
            {
                $remote_package = $remote_packages->next_result();
                $package['id'] = $remote_package->get_id();
                $remote_package->set_default_properties($package);
                if (! $remote_package->update())
                {
                    return false;
                }
            }
            else
            {
                $remote_package = new RemotePackage($package);

                if (! $remote_package->create())
                {
                    return false;
                }
            }
        }

        return true;
    }
}
?>