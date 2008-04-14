<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importdocument.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/document/document.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once 'dokeos185itemproperty.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learningobject.class.php';

/**
 * This class represents an old Dokeos 1.8.5 document
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185Document extends ImportDocument
{
	private static $mgdm;
	private $item_property;
	private static $counter = 0;

	/**
	 * document properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PATH = 'path';
	const PROPERTY_TITLE = 'title';
 	const PROPERTY_SIZE = 'size';
 	const PROPERTY_COMMENT = 'comment';
 	const PROPERTY_FILETYPE = 'filetype';
	
	/**
	 * Default properties of the document object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new document object.
	 * @param array $defaultProperties The default properties of the document
	 *                                 object. Associative array.
	 */
	function Dokeos185Document($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this document object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this document.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all documents.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,self :: PROPERTY_PATH,self :: PROPERTY_TITLE,
			self :: PROPERTY_SIZE,self :: PROPERTY_COMMENT, self :: PROPERTY_FILETYPE);
	}
	
	/**
	 * Sets a default property of this document by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default document
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this document.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the path of this document.
	 * @return String The path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}
	
	/**
	 * Returns the title of this document.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the size of this document.
	 * @return int The size.
	 */
	function get_size()
	{
		return $this->get_default_property(self :: PROPERTY_SIZE);
	}
	
	/**
	 * Returns the comment of this document.
	 * @return String The comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}
	
	/**
	 * Returns the filetype of this document.
	 * @return String The filetype.
	 */
	function get_filetype()
	{
		return $this->get_default_property(self :: PROPERTY_FILETYPE);
	}

	static function get_counter()
	{
		return self :: $counter;
	}
	
	/**
	 * Check if the document is valid
	 * @param Course $course the course of the document
	 * @return true if the dropbox category is valid
	 */
	function is_valid($array)
	{
		$course = $array['course'];
		$this->item_property = self :: $mgdm->get_item_property($course->get_db_name(),'document',$this->get_id());	
		
		$pos = strrpos($this->get_path(), '/');
		$filename = substr($this->get_path(), $pos);
		$old_path = substr($this->get_path(), 0, $pos);
		
		$old_rel_path = 'courses/' . $course->get_directory() . '/document/'  . $old_path;

		$filename = iconv("UTF-8", "ISO-8859-1", $filename);
		$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

		if(!$this->get_id() || !$this->get_path() || !$this->get_filetype() || !$this->item_property || !$this->item_property->get_ref()
			|| !$this->item_property->get_insert_date() || !file_exists(self :: $mgdm->append_full_path(false,$old_rel_path . $filename)) )
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.document');
			
			if(	file_exists(self :: $mgdm->append_full_path(false,$old_rel_path . $filename)) )
			{
				$filesize = filesize(self :: $mgdm->append_full_path(false,$old_rel_path . $filename));
				self :: $counter += $filesize;
			}
			
			return false;
		}
		return true;
	}
	
	/**
	 * Convert to new blog
	 * @param Course $course the course of the document
	 * @return the new document
	 */
	function convert_to_lcms($array)
	{
		$course = $array['course'];
		$start_time = Logger :: get_microtime();
		$new_user_id = self :: $mgdm->get_id_reference($this->item_property->get_insert_user_id(),'user_user');	
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');	
		$end_time = Logger :: get_microtime();
		$passedtime_idref = $end_time - $start_time;
		
		$pos = strrpos($this->get_path(), '/');
		$filename = substr($this->get_path(), $pos);
		$old_path = substr($this->get_path(), 0, $pos);
		
		if(!$new_user_id)
		{
			$start_time = Logger :: get_microtime();
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
			$end_time = Logger :: get_microtime();
			$passedtime_orphan = $end_time - $start_time;
		}
		
		$new_path = $new_user_id . '/';
		$old_rel_path = 'courses/' . $course->get_directory() . '/document/'  . $old_path;

		$new_rel_path = 'files/repository/' . $new_path;
		
		$lcms_document = null;

		$filename = iconv("UTF-8", "ISO-8859-1", $filename);
		$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

		$document_md5 = md5_file(self :: $mgdm->append_full_path(false,$old_rel_path . $filename)); 
		$document_id = self :: $mgdm->get_document_from_md5($new_user_id,$document_md5);
		
		if(!$document_id)
		{
			
			$start_time = Logger :: get_microtime();
			$file = self :: $mgdm->move_file($old_rel_path, $new_rel_path, 
				$filename);
			$end_time = Logger :: get_microtime();
			$passedtime_copy = $end_time - $start_time;

			if($file)
			{
				$start_time = Logger :: get_microtime();
				//document parameters
				$lcms_document = new Document();
	
				$lcms_document->set_filesize($this->get_size());
				if($this->get_title())
					$lcms_document->set_title($this->get_title());
				else
					$lcms_document->set_title($filename);
				$lcms_document->set_description('...');
				$lcms_document->set_comment($this->get_comment());
				
				$lcms_document->set_owner_id($new_user_id);
				$lcms_document->set_creation_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
				$lcms_document->set_modification_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
				$lcms_document->set_path($new_path . $file);
				$lcms_document->set_filename($file);
				
				// Category for announcements already exists?
				$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
					Translation :: get('documents'));
				if(!$lcms_category_id)
				{
					//Create category for tool in lcms
					$lcms_repository_category = new Category();
					$lcms_repository_category->set_owner_id($new_user_id);
					$lcms_repository_category->set_title(Translation :: get('documents'));
					$lcms_repository_category->set_description('...');
			
					//Retrieve repository id from course
					$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
						'category', Translation :: get('MyRepository'));
					$lcms_repository_category->set_parent_id($repository_id);
					
					//Create category in database
					$lcms_repository_category->create();
					
					$lcms_document->set_parent_id($lcms_repository_category->get_id());
				}
				else
				{
					$lcms_document->set_parent_id($lcms_category_id);	
				}
			
				if($this->item_property->get_visibility() == 2)
					$lcms_document->set_state(1);
				
				//create document in database
				$lcms_document->create_all();
				
				//Add id references to temp table
				self :: $mgdm->add_id_reference($this->get_id(), $lcms_document->get_id(), 'repository_document');
				
				$end_time = Logger :: get_microtime();
				$passedtime_document = $end_time - $start_time;
			
				self :: $mgdm->add_file_md5($new_user_id, $lcms_document->get_id(), $document_md5);
			}
			else
			{ 
				$start_time = Logger :: get_microtime();
				$document_id = self :: $mgdm->get_document_id($new_rel_path . $filename, $new_user_id);
				if($document_id)
				{
					$lcms_document = new LearningObject();
					$lcms_document->set_id($document_id);
				}
				$end_time = Logger :: get_microtime();
				$passedtime_doublefile = $end_time - $start_time;
			}
			
		}
		else
		{
			$lcms_document = new LearningObject();
			$filesize = filesize(self :: $mgdm->append_full_path(false,$old_rel_path . $filename));
			self :: $counter += $filesize;
			
		}
			
		//publication
		$start_time = Logger :: get_microtime();
		
		if($this->item_property->get_visibility() <= 1 && $lcms_document) 
		{
			
			$start_time_cat = Logger :: get_microtime();
			
			// Categories already exists?
			$file_split = array();
			$file_split = split('/', $old_path);
			
			array_shift($file_split);
			array_pop($file_split);
			
			$parent = 0;
			
			foreach($file_split as $cat)
			{
				$lcms_category_id = self :: $mgdm->publication_category_exist($cat, $new_course_code,
					'document',$parent);
				
				if(!$lcms_category_id)
				{
					//Create category for tool in lcms
					$lcms_category = new LearningObjectPublicationCategory();
					$lcms_category->set_title($cat);
					$lcms_category->set_course($new_course_code);
					$lcms_category->set_tool('document');
					$lcms_category->set_parent_category_id($parent);
					
					//Create category in database
					$lcms_category->create();
					$parent = $lcms_category->get_id();
				}
				else
				{
					$parent = $lcms_category_id;
				}
				
			}	
			
			$end_time_cat = Logger :: get_microtime();
			$passedtime_categories = $end_time_cat - $start_time_cat;
		
			$publication = new LearningObjectPublication();
			
			$publication->set_learning_object($lcms_document);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('document');
			$publication->set_category_id($parent);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();		
		}
		
		$end_time = Logger :: get_microtime();
		$passedtime_publication = $end_time - $start_time;
		
		flush();
		
		return array('document' => $lcms_document, 'copy_time' => $passedtime_copy, 'document_time' => $passedtime_document, 
					 'categories_time' => $passedtime_categories, 'publication_time' => $passedtime_publication, 'idref_time' => $passedtime_idref,
					 'orphan_time' => $passedtime_orphan, 'doublefile_time' => $passedtime_doublefile); 
	}
	
	/**
	 * Retrieve all documents from the database
	 * @param Course $course the course of the document
	 * @param MigrationDataManager $mgdm the migration data manager
	 * @param bool $include_deleted_files 
	 * @return array of blogs
	 */
	static function get_all($parameters)
	{
		
		self :: $mgdm = $parameters['mgdm'];
		$course = $parameters['course'];
		return self :: $mgdm->get_all_documents($course, $parameters['del_files']);
	}
}
?>
