<?php
/**
 * $Id$
 * @package repository
 */
/**
 * Class to display the footer of a HTML-page
 */
class Footer
{
	private $version;
	/**
	 * Create a new Footer
	 */
	function Footer($version)
	{
		$this->version = $version;
	}
	/**
	 * Display the footer
	 */
	function display()
	{
		echo $this->toHtml();
	}
	/**
	 * Returns the HTML code for the footer
	 */
	function toHtml()
	{
		$output[] = '    <div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
		$output[] = '   </div> <!-- end of #main" started at the end of claro_init_banner.inc.php -->';
		$output[] = '   <div id="footer"> <!-- start of #footer section -->';
		$output[] = '    <div class="copyright">';
		$output[] = '     '.get_lang('Platform').'&nbsp;<a href="http://www.dokeos.com">'.$this->version.'</a>&nbsp;&copy;&nbsp;'.date('Y');
		$output[] = '    </div>';
		$admin_data = '';
		if (get_setting('show_administrator_data') == "true")
		{
			$admin_data .= get_lang('Manager');
			$admin_data .= ':&nbsp;';
			$admin_data .= Display :: encrypted_mailto_link(get_setting('emailAdministrator'), get_setting('administratorSurname').' '.get_setting('administratorName'));
		}
		$output[] = '    '.$admin_data.'&nbsp;';
		$output[] = '   </div> <!-- end of #footer -->';
		$output[] = '  </div> <!-- end of #outerframe opened in header -->';
		$output[] = ' </body>';
		$output[] = '</html>';
		return implode("\n",$output);
	}
}
?>