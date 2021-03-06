<?php
require_once dirname(__FILE__) . '/cas/cas_authentication.class.php';
/**
 * $Id$
 * @package authentication
 */
/**
 * An abstract class for handling authentication. Impement new authentication
 * methods by creating a class which extends this abstract class.
 */
abstract class Authentication
{

    /**
     * Constructor
     */
    function Authentication()
    {
    }

    /**
     * Checks if the given username and password are valid
     * @param string $username
     * @param string $password
     * @return true
     */
    abstract function check_login($user, $username, $password = null);

    /**
     * Checks if this authentication method allows the password to be changed.
     * @return boolean
     */
    abstract function is_password_changeable();

    /**
     * Checks if this authentication method allows the username to be changed.
     */
    abstract function is_username_changeable();

    /**
     * Checks if this authenticaion method is able to register new users based
     * on a given username and password
     */
    public function can_register_new_user()
    {
        return false;
    }

    /**
     * Registers a new user
     * @param string $username
     * @param string $password
     * @return boolean True on success, false if not
     */
    public function register_new_user($username, $password = null)
    {
        return false;
    }

    /**
     * Logs the current user out of the platform. The different authentication
     * methods can overwrite this function if additional operations are needed
     * before a user can be logged out.
     * @param User $user The user which is logging out
     */
    function logout($user)
    {
        Session :: destroy();
    }

    function is_valid()
    {
        // TODO: Add system here to allow authentication via encrypted user key ?
        if (! Session :: get_user_id())
        {
            // Check whether external authentication is enabled
            $allow_external_authentication = PlatformSetting :: get('enable_external_authentication');
            $no_external_authentication = Request :: get('noExtAuth');
            
            if ($allow_external_authentication && ! isset($no_external_authentication))
            {
                $external_authentication_types = self :: get_external_authentication_types();
                
                foreach ($external_authentication_types as $type)
                {
                    $allow_authentication = PlatformSetting :: get('enable_' . $type . '_authentication');
                    $no_authentication = Request :: get('no' . DokeosUtilities :: underscores_to_camelcase($type) . 'Auth');
                    
                    if ($allow_authentication)
                    {
                        $authentication = self :: factory($type);
                        if ($authentication->check_login())
                        {
                            return true;
                        }
                    }
                }
                
                return false;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates an instance of an authentication class
     * @param string $authentication_method
     * @return Authentication An object of a class implementing this abstract
     * class.
     */
    function factory($authentication_method)
    {
        $authentication_class_file = dirname(__FILE__) . '/' . $authentication_method . '/' . $authentication_method . '_authentication.class.php';
        $authentication_class = DokeosUtilities :: underscores_to_camelcase($authentication_method) . 'Authentication';
        require_once $authentication_class_file;
        return new $authentication_class();
    }

    static function get_external_authentication_types()
    {
        $types = array();
        $types[] = 'cas';
        return $types;
    }

    static function get_internal_authentication_types()
    {
        $types = array();
        $types[] = 'ldap';
        $types[] = 'platform';
        return $types;
    }

    function get_configuration()
    {
        return array();
    }

    function is_configured()
    {
        $settings = $this->get_configuration();
        
        foreach ($settings as $setting => $value)
        {
            if (empty($value) || ! isset($value))
            {
                return false;
            }
        }
        
        return true;
    }
}
?>