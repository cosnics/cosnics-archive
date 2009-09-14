<?php

class UserQuotaCellRenderer
{
	private $browser;
	
	function UserQuotaCellRenderer($browser)
	{
		$this->browser = $browser;
	}

	function render_cell($index, $user_quota)
	{
		switch($index)
		{
			case 0: $data = $user_quota['days']; break;
			case 1: $data = $user_quota['max_credits']; break;
			case 2: $data = $user_quota['used_credits']; break;
		}
        
        if (is_null($data))
        {
            $data = '-';
        }
        
        return $data;
	}

	function get_properties()
	{
		return array(
					'Days',
					'MaxCredits',
					'UsedCredits'
			);
	}
	
 	function get_property_count()
    {
        return count($this->get_properties());
    }
}
?>