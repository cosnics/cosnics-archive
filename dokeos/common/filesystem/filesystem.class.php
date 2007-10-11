<?php
/**
 * $Id: fileManage.lib.php 7893 2006-03-02 09:54:40Z  $
 * @package filesystem
 */
/**
 * This class implements some usefull functions to hanlde the filesystem.
 * @todo Implement other usefull functions which are now in files like
 * fileManage.lib.php, document.lib.php, fileUpload.lib.php But keep the
 * functions to filesystem-related stuff. So this isn't the place for code for
 * getting an icon to match a documents filetype for example.
 * @todo Make sure all functions in this class remove special chars before doing
 * stuff. So other modules shouldn't take care of the special chars problems.
 * This also means some functions which now return boolean should return the
 * changed pathname or filename after they successfully finished their
 * work.
 */
class Filesystem
{
	/**
	 * Creates a directory.
	 * This function creates all missing directories in a given path.
	 * @param string $path
	 * @param string $mode
	 * @return boolean True if successfull, false if not.
	 */
	public static function create_dir($path,$mode = '0777')
	{
		return mkdir($path,$mode,true);
	}
	/**
	 * Removes a directory and all its contents.
	 * @param $string $path
	 * @return boolean True if successfull, false if not.
	 */
	public static function remove_dir($path)
	{
		if (!is_writable($path))
		{
			// If path is not writable, try to change permissions
			if (!@ chmod($path, 0777))
			{
				return false;
			}
		}
		$d = dir($path);
		// Recursively remove all entries in the directory
		while (false !== ($entry = $d->read()))
		{
			if ($entry == '.' || $entry == '..')
			{
				continue;
			}
			$entry = $path.'/'.$entry;
			if (is_dir($entry))
			{
				if (!Filesystem::remove_dir($entry))
				{
					return false;
				}
				continue;
			}
			if (!@ unlink($entry))
			{
				$d->close();
				return false;
			}
		}
		$d->close();
		// And finally remove the directory itself
		return rmdir($path);
	}
	/**
	 * Copies a file. If the destination directory doesn't exist, this function
	 * tries to create the directory using the Filesystem::create_dir function.
	 * @param string $source The full path to the source file
	 * @param string $destination The full path to the destination file
	 * @param boolean $overwrite If the destination file allready exists, should
	 * it be overwritten?
	 * @return boolean True if successfull, false if not.
	 */
	public static function copy_file($source,$destination,$overwrite = false)
	{
		if(file_exists($destination) && !$overwrite)
		{
			return false;
		}
		$destination_dir = dirname($destination);
		if(file_exists($source) && Filesystem::create_dir($destination_dir))
		{
			return copy($source,$destination);
		}
	}
	/**
	 * Creates a unique filename. This function will also use the function
	 * Filesystem::create_safe_filename to make sure the resulting filename is
	 * safe to use.
	 * @param string $path The path where the file will be created
	 * @param string $desired_filename The desired filename
	 * @return string A unique filename based on the given wanted filename
	 */
	public static function create_unique_filename($path,$desired_filename)
	{
		$filename = Filesystem::create_safe_filename($desired_filename);
		$new_filename = $filename;
		$index = 0;
		while (file_exists($path.'/'.$new_filename))
		{
			$file_parts = explode('.', $filename);
			$new_filename = array_shift($file_parts). ($index ++).'.'.implode('.',$file_parts);
		}
		return $new_filename;
	}
	/**
	 * Creates a safe filename
	 * @param string $desired_filename The desired filename
	 * @return string The safe filename
	 */
	public static function create_safe_filename($desired_filename)
	{
		//Change encoding
		$safe_filename = mb_convert_encoding($desired_filename,"ISO-8859-1","UTF-8");
		//Replace .php by .phps
		$safe_filename = eregi_replace("\.(php.?|phtml)$", ".phps", $safe_filename);
		//If first letter is . add something before
		$safe_filename = eregi_replace("^\.","0.",$safe_filename);
		//Replace accented characters
		$safe_filename = strtr($safe_filename, 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïðñòóôõöøùúûüýÿ', 'aaaaaaaceeeeiiiidnoooooouuuuyaaaaaaceeeeiiiidnoooooouuuuyy');
		//Replace all except letters, numbers, - and . to underscores
	    $safe_filename =  ereg_replace('[^0-9a-zA-Z\-\.]', '_',$safe_filename);
	    //Replace set of underscores by a single underscore
		$safe_filename = ereg_replace('[_]+','_',$safe_filename);
		return $safe_filename;
	}
	/**
	 * Writes content to a file. This function will try to create the path and
	 * the file if they don't exist yet.
	 * @param string $file The full path to the file
	 * @param string $content
	 * @param boolean $append If true the given conten will be appended to the
	 * end of the file
	 */
	public static function write_to_file($file,$content,$append = false)
	{
		if(Filesystem::create_dir(dirname($file)))
		{
			if($create_file = fopen($file, $append ? 'a': 'w'))
			{
				fwrite($create_file, $values['html_content']);
				fclose($create_file);
				chmod($file, 0777);
				return true;
			}
			return false;
		}
		return false;
	}
	/**
	 * Determines the number of bytes taken by a given directory or file
	 * @param string $path The full path to the file or directory of which the
	 * disk space should be determined
	 * @return int The number of bytes taken on disk by the given directory or
	 * file
	 */
	public static function get_disk_space($path)
	{
		if(is_file($path))
		{
			return filesize($path);
		}
		if(is_dir($path))
		{
			return total_disk_space($path);
		}
		// If path doesn't exist, return null
		return 0;
	}
	/**
	 * Guesses the disk space used when the given content would be written to a
	 * file
	 * @param string $content
	 * @return int The number of bytes taken on disk by a file containing the
	 * given content
	 */
	public static function guess_disk_space($content)
	{
		$tmpfname =tempnam();
		$handle = fopen($tmpfname, "w");
		fwrite($handle, $content);
		fclose($handle);
		$disk_space = filesize($tmpfname);
		unlink($tmpfname);
		return $disk_space;
	}
}
?>