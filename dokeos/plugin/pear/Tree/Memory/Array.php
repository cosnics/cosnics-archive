<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//
//  $Id: Array.php,v 1.15 2007/06/01 23:38:16 dufuz Exp $

/**
 * EXPERIMENTAL
 *
 * @access     public
 * @author     Wolfram Kriesing <wolfram@kriesing.de>
 * @version    2002/08/30
 * @package    Tree
 */
class Tree_Memory_Array
{

    var $data = array();

    /**
     * this is the internal id that will be assigned if no id is given
     * it simply counts from 1, so we can check if($id) i am lazy :-)
     */
    var $_id = 1;

    // {{{ Tree_Memory_Array()

    /**
     * set up this object
     *
     * @version    2002/08/30
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  $dsn    the path on the filesystem
     * @param      array   $options  additional options you can set
     */
    function Tree_Memory_Array(&$array, $options = array())
    {
        $this->_array = &$array;
        $this->_options = $options; // not in use currently
    }

    // }}}
    // {{{ setup()

    /**
     *
     *
     * @version    2002/08/30
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     boolean     true on success
     */
    function setup()
    {
        unset($this->data);
        if (is_array($this->_array)) {
            $this->data[0] = null;
            $theData = array(&$this->_array);
            $this->_setup($theData);
        }
        return $this->data;
    }

    // }}}
    // {{{ _setup()

    /**
     * we modify the $this->_array in here, we also add the id
     * so methods like 'add' etc can find the elements they are searching for,
     * if you dont like your data to be modified dont pass them as reference!
     */
    function _setup(&$array, $parent_id = 0)
    {
        foreach ($array as $nodeKey => $aNode) {
            $newData = $aNode;
            // if the current element has no id, we generate one
            if (!isset($newData['id']) || !$newData['id']) {
                // build a unique numeric id
                $newData['id'] = $this->_id++;
                // set the id
                $array[$nodeKey]['id'] = $newData['id'];
            } else {
                $idAsInt = (int)$newData['id'];
                if ($idAsInt > $this->_id) {
                    $this->_id = $idAsInt;
                }
            }
            // set the parent-id, since we only have a 'children' array
            $newData['parent_id'] = $parent_id;
            $children = null;
            // remove the 'children' array, since this is only info for
            // this class
            foreach ($newData as $key => $val) {
                if ($key == 'children') {
                    unset($newData[$key]);
                }
            }

            $this->data[$newData['id']] = $newData;
            if (isset($aNode['children']) && $aNode['children']) {
                if (!isset($array[$nodeKey]['children'])) {
                    $array[$nodeKey]['children'] = array();
                }
                $this->_setup($array[$nodeKey]['children'], $newData['id']);
            }
        }
    }


    // }}}
    // {{{ setData()

    /**
     * this is mostly used by switchDataSource
     * this method put data gotten from getNode() in the $this->_array
     *
     */
    function setData($data)
    {
        $unsetKeys = array('childId', 'left', 'right');

        foreach ($data as $aNode) {
            foreach ($aNode as $key => $val) {
                if (is_array($val) || in_array($key, $unsetKeys)) {
                    unset($aNode[$key]);
                }
            }
            $this->add($aNode,$aNode['parent_id']);
        }
        $this->_array = $this->_array['children'][0];
    }

    // }}}
    // {{{ add()

    /**
     * add a new item to the tree
     * what is tricky here, we also need to add it to the source array
     *
     * @param  array   the data for the new node
     * @param  int     the ID of the parent node
     * @param  int     the ID of the previous node
     */
    function add($data, $parent_id, $previousId = null)
    {
        if (!isset($data['id'])) {
            $data['id'] = ++$this->_id;
        } elseif((int)$data['id'] > $this->_id) {
            // Since we dont want to overwrite anything. just in case update
            // the $this->_id if the data['id'] has a higher number.
            $this->_id = (int)$data['id'];
        }
        $data['parent_id'] = $parent_id;
        $this->data[$data['id']] = $data;

        // there might not be a root element yet
        if (!isset($this->_array['children'])) {
            $data['parent_id'] = 0;
            $this->_array['children'][] = $data;
        } else {
            array_walk($this->_array['children'],
                        array(&$this, '_add'),
                        array($data, $parent_id, $previousId)
                    );
        }
        return $data['id'];
    }

    // }}}
    // {{{ _add()

    /**
     * we need to add the node to the source array
     * for this we have this private method which loops through
     * the source array and adds it in the right place
     *
     * @param  mixed   the value of the array, as a reference. So we work
     *                 right on the source
     * @param  mixed   the key of the node
     * @param  array   an array which contains the following
     *                 new data,
     *                 parent ID under which to add the node,
     *                 the prvious ID
     */
    function _add(&$val, $key, $data)
    {
        // is the id of the current elment ($val) == to the parent_id ($data[1])
        if ($val['id'] == $data[1]) {
            if (isset($data[2]) && $data[2] === 0) {
                // if the previousId is 0 means, add it as the first member
                $val['children'] = array_merge(array($data[0]), $val['children']);
            } else {
                $val['children'][] = $data[0];
            }
        } else {        // if we havent found the new element go on searching
            if (isset($val['children'])) {
                array_walk($val['children'],array(&$this, '_add'), $data);
            }
        }
    }

    // }}}
    // {{{ update()

    /**
     * update an entry with the given id and set the data as given
     * in the array $data
     *
     * @param  int     the id of the element that shall be updated
     * @param  array   the data, [key]=>[value]
     * @return void
     */
    function update($id, $data)
    {
        if ($this->_array['id'] == $id) {
            foreach ($data as $key => $newVal) {
                $this->_array[$key] = $newVal;
            }
        } else {
            array_walk($this->_array['children'],
                        array(&$this, '_update'),
                        array($id, $data)
                    );
        }
    }

    // }}}
    // {{{ _update()

    /**
     * update the element with the given id
     *
     * @param  array    a reference to an element inside $this->_array
     *                  has to be a reference, so we can really modify
     *                  the actual data
     * @param  int      not in use, but array_walk passes this param
     * @param  array    [0] is the id we are searching for
     *                  [1] are the new data we shall set
     * @return void
     */
    function _update(&$val, $key, $data)
    {
        // is the id of the current elment ($val) == to the parent_id ($data[1])
        if ($val['id'] == $data[0]) {
            foreach ($data[1] as $key => $newVal) {
                $val[$key] = $newVal;
            }
        } else {
            // if we havent found the new element go on searching
            // in the children
            if (isset($val['children'])) {
                array_walk($val['children'],array(&$this, '_update'), $data);
            }
        }
    }

    // }}}
    // {{{ remove()

    /**
     * remove an element from the tree
     * this removes all the children too
     *
     * @param  int the id of the element to be removed
     */
    function remove($id)
    {
        // we only need to search for element that do exist :-)
        // otherwise we save some processing time
        if ($this->data[$id]) {
            $this->_remove($this->_array, $id);
        }
    }

    // }}}
    // {{{ _remove()

    /**
     * remove the element with the given id
     * this will definitely remove all the children too
     *
     * @param  array    a reference to an element inside $this->_array
     *                  has to be a reference, so we can really modify
     *                  the actual data
     * @param  int      the id of the element to be removed
     * @return void
     */
    function _remove(&$val, $id)
    {
        if (isset($val['children'])) {
            foreach ($val['children'] as $key => $aVal) {
                if ($aVal['id'] == $id) {
                    if (count($val['children']) < 2) {
                        unset($val['children']);
                    } else {
                        unset($val['children'][$key]);
                    }
                } else {
                    $this->_remove($val['children'][$key], $id);
                }
            }
        }
    }

    // }}}
}
?>