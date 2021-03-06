<?php
//  $Id: getLevel.php,v 1.3 2007/06/01 23:38:17 dufuz Exp $

require_once 'UnitTest.php';

class tests_getLevel extends UnitTest
{
    // check if we get the right ID, for the given path
    function test_MemoryDBnested()
    {
        $tree = $this->getMemoryDBnested();        
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $this->assertEquals(2, $tree->getLevel($id));
    }

    function test_MemoryMDBnested()
    {
        $tree = $this->getMemoryMDBnested();
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $this->assertEquals(2, $tree->getLevel($id));
    }

    // do this for XML
            
    // do this for Filesystem

    // do this for DBsimple
    
    // do this for DynamicSQLnested
    function test_DynamicSQLnested()
    {
        $tree =& $this->getDynamicSQLnested();
//        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $id = 5;
        $this->assertEquals(2, $tree->getLevel($id));
    }
}

?>
