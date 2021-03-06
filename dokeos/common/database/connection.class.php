<?php
/**
 * @package common.database
 */
require_once Path :: get_library_path() . 'configuration/configuration.class.php';

/**
 *	This class represents the current database connection.
 *
 *	@author Hans De Bisschop
 */

class Connection
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    /**
     * The MDB2 Connection object.
     */
    private $connection;

    //'debug_handler'=>array('DatabaseRepositoryDataManager','debug')
    

    /**
     * Constructor.
     */
    private function Connection()
    {
        $configuration = Configuration :: get_instance();
        $this->connection = MDB2 :: connect($configuration->get_parameter('database', 'connection_string'), array('debug' => 3));
    }

    /**
     * Returns the instance of this class.
     * @return Connection The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets the database connection.
     * @return mixed MDB2 DB Conenction.
     */
    function get_connection()
    {
        return $this->connection;
    }

    function set_option($option, $value)
    {
        $this->connection->setOption($option, $value);
    }
}
?>