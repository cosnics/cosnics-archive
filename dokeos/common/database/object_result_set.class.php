<?php
/**
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once dirname(__FILE__) . '/result_set.class.php';
/**
 * This class represents a resultset which represents a set of courses.
 */
class ObjectResultSet extends ResultSet
{
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';
    const POSITION_SINGLE = 'single';
    const POSITION_MIDDLE = 'middle';
    
    /**
     * The datamanager used to retrieve objects from the repository
     */
    private $data_manager;
    
    /**
     * An instance of DB_result
     */
    private $handle;
    
    /**
     * The classname to map the object to
     */
    private $class_name;
    
    private $current;

    /**
     * Create a new resultset for handling a set of learning objects
     * @param DataManager $data_manager The datamanager used to
     * retrieve objects from the repository
     * @param DB_result $handle The handle to retrieve records from a database
     * resultset
     */
    function ObjectResultSet($data_manager, $handle, $class_name)
    {
        $this->data_manager = $data_manager;
        $this->handle = $handle;
        $this->class_name = $class_name;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
        if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $this->current ++;
            return $this->data_manager->record_to_object($record, $this->class_name);
        }
        return null;
    }

    /*
	 * Inherited
	 */
    function size()
    {
        return $this->handle->numRows();
    }
    
    function is_empty()
    {
    	return $this->size() == 0;
    }

    /*
	 * Inherited
	 */
    function skip($count)
    {
        for($i = 0; $i < $count; $i ++)
        {
            $this->handle->fetchRow();
        }
    }

    function current()
    {
        return $this->current;
    }

    function position()
    {
        $current = $this->current();
        $size = $this->size();
        
        if ($current == 1 && $size == 1)
        {
            return self :: POSITION_SINGLE;
        }
        elseif ($size > 1 && $current == $size)
        {
            return self :: POSITION_LAST;
        }
        elseif ($size > 1 && $current == 1)
        {
            return self :: POSITION_FIRST;
        }
        else
        {
            return self :: POSITION_MIDDLE;
        }
    }

    function is_first()
    {
        return ($this->position() == self :: POSITION_FIRST || $this->is_single());
    }

    function is_last()
    {
        return ($this->position() == self :: POSITION_LAST || $this->is_single());
    }

    function is_middle()
    {
        return ($this->position() == self :: POSITION_MIDDLE || $this->is_single());
    }

    function is_single()
    {
        return ($this->position() == self :: POSITION_SINGLE);
    }
}
?>