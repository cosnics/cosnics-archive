<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
require_once ('HTML/QuickForm/Rule.php');
/**
 * QuickForm rule to check if uploading a document is possible compared to the
 * available disk quota.
 */
class HTML_QuickForm_Rule_DiskQuota extends HTML_QuickForm_Rule
{

    /**
     * Function to check if an uploaded file can be stored in the repository
     * @see HTML_QuickForm_Rule
     * @param mixed $file Uploaded file (array)
     * @return boolean True if the filesize doesn't cause a disk quota overflow
     */
    function validate($file)
    {
        $size = $file['size'];
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user(Session :: get_user_id());
        $quotamanager = new QuotaManager($user);
        $available_disk_space = $quotamanager->get_available_disk_space();
        return $size <= $available_disk_space;
    }
}
?>