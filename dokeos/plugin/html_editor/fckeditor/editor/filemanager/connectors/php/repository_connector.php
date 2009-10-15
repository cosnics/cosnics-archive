<?php

require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

function GetRepositoryCategoriesAndDocuments($sResourceType)
{
	$user_id = Session :: get_user_id();
	$rdm = RepositoryDataManager :: get_instance();
	$category_id = Request :: get('CurrentCategory');
	
	$html[] = '<Folders>';
	
	$conditions = array();
	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category_id);
	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
	$condition = new AndCondition($conditions);
	
	$categories = $rdm->retrieve_categories($condition);
	while($category = $categories->next_result())
	{
		$html[] = '<Folder name="' . $category->get_name() . '" id="' . $category->get_id() . '"/>';
	}
	
	$html[] = '</Folders>';
	$html[] = '<Files>';
	
	$conditions = array();
	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category_id);
	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'document');
	$condition = new AndCondition($conditions);
	
	$documents = $rdm->retrieve_content_objects('document', $condition);

	while($document = $documents->next_result())
	{
		$html[] = '<File name="' . $document->get_filename() . '" size="' . $document->get_filesize() . '" url="' . $document->get_url() . '"/>';
	}
	
	$html[] = '</Files>';

	echo implode($html, "\n");
}

function GetRepositoryCategories($sResourceType, $sCurrentFolder)
{
	$user_id = Session :: get_user_id();
	$rdm = RepositoryDataManager :: get_instance();
	$category_id = Request :: get('CurrentCategory');
	
	$html[] = '<Folders>';
	
	$conditions = array();
	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category_id);
	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
	$condition = new AndCondition($conditions);
	
	$categories = $rdm->retrieve_categories($condition);
	while($category = $categories->next_result())
	{
		$html[] = '<Folder name="' . $category->get_name() . '" />';
	}
	
	$html[] = '</Folders>';
	
	echo implode($html, "\n");
}

?>