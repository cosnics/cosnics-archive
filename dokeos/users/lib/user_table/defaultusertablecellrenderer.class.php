<?php
/**
 * @package users.lib.user_table
 */

require_once dirname(__FILE__).'/usertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../../../common/imagemanipulation/imagemanipulation.class.php';

class DefaultUserTableCellRenderer implements UserTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultUserTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param UserTableColumnModel $column The column which should be
	 * rendered
	 * @param User $user The User to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $user)
	{
		if ($property = $column->get_user_property())
		{
			switch ($property)
			{
				case User :: PROPERTY_USER_ID :
					return $user->get_user_id();
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
				case User :: PROPERTY_USERNAME :
					return $user->get_username();
				case User :: PROPERTY_EMAIL :
					return $user->get_email();
				case User :: PROPERTY_STATUS :
					return $user->get_status();
				case User :: PROPERTY_PLATFORMADMIN :
					return $user->get_platformadmin();
				case User :: PROPERTY_OFFICIAL_CODE :
					return $user->get_official_code();
				case User :: PROPERTY_LANGUAGE :
					return $user->get_language();
				case User :: PROPERTY_VERSION_QUOTA :
					return $user->get_version_quota();
				case User :: PROPERTY_PICTURE_URI :
					return $this->render_picture($user);
			}
		}
		return '&nbsp;';
	}
	private function render_picture($user)
	{
		if ($user->has_picture())
		{
			$picture = $user->get_full_picture_path();
			$thumbnail_path = $this->get_thumbnail_path($picture);
			$thumbnail_url = Path :: get_path(WEB_TEMP_PATH).basename($thumbnail_path);
			return '<span style="display:none;">1</span><img src="'.$thumbnail_url.'" alt="'.htmlentities($user->get_fullname()).'" border="0"/>';
		}
		else
		{
			return '<span style="display:none;">0</span>';
		}
	}
	private function get_thumbnail_path($image_path)
	{
		$thumbnail_path = Path :: get_path(WEB_TEMP_PATH).md5($image_path).basename($image_path);
		if(!is_file($thumbnail_path))
		{
			$thumbnail_creator = ImageManipulation::factory($image_path);
			$thumbnail_creator->create_thumbnail(20);
			$thumbnail_creator->write_to_file($thumbnail_path);
		}
		return $thumbnail_path;
	}
}
?>