<?php
# DOKEOS version Dokeos LCMS 0.3
# File generated by /install/index.php script - Tue, 19 Feb 2008 12:50:55 +0100
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

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
/*
==============================================================================
		Configuration of virtual campus

This file contains a list of variables that can be modified by the campus
site administrator. Pay attention when changing these variables, some changes
can cause Dokeos to stop working.
If you changed some settings and want to restore them, please have a look at
claro_main.conf.dist.php. That file is an exact copy of the config file at
install time.
==============================================================================
*/

//============================================================================
//   Directory settings
//============================================================================
// URL to the root of your Dokeos installation
$rootWeb                     = 'http://localhost/LCMS/';
// Path to the root of your Dokeos installation
$rootSys                     = dirname(__FILE__).'/../../../';
// Path from your WWW-root to the root of your Dokeos installation
$urlAppend                   = '/LCMS';
// Directory of the Dokeos code
$clarolineRepositoryAppend   = "main/";
// Do not change the following values
$clarolineRepositorySys      = $rootSys.$clarolineRepositoryAppend;
$clarolineRepositoryWeb      = $rootWeb.$clarolineRepositoryAppend;

//============================================================================
//   Misc. settings
//============================================================================
// Dokeos version
$dokeos_version   = 'Dokeos LCMS 0.3';
// security word for password recovery
$security_key       = '56401a07d8d0f674be7cd353d268e4d7';
?>