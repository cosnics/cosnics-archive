<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/group_subscribe.csv';
$groups = parse_csv($file);
$location = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

foreach($groups as $group)
{
	subscribe_user($group);
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{
	if(file_exists($file) && $fp = fopen($file, "r"))
	{
		$keys = fgetcsv($fp, 1000, ";");
		$groups = array();
		while($group_data = fgetcsv($fp, 1000, ";"))
		{
			$group = array();
			foreach($keys as $index => $key)
			{
				$group[$key] = trim($group_data[$index]);
			}
			$groups[] = $group;
		}
		fclose($fp);
	}
	else
	{
		log("ERROR: Can't open file ($file)");
	}
    return $groups;
}

function subscribe_user($group_rel_user)
{
    global $hash, $client;
	log_message('Subscribing user ' . $group_rel_user['user_id'] . ' to group '. $group_rel_user['group_id']);
    if(empty($hash))
    $hash = login();
    $group_rel_user['hash'] = $hash;
	$result = $client->call('WebServicesGroup.subscribe_user', $group_rel_user);
    if($result == 1)
    {
        log_message(print_r('User successfully subscribed', true));
    }
    else
    	log_message(print_r($result, true));
}

function login()
{
    global $client;
	/* Change the username and password to the ones corresponding to  your database.
     * The password for the login service is :
     * IP = the ip from where the call to the webservice is made
     * PW = your hashed password from the db
     *
     * $password = Hash(IP+PW) ;
     */
	$username = 'admin';
	$password = 'ef1fffa5a0b8736f2d8a5f0c913bb0930d1f16c0';
    $login_client = new nusoap_client('http://localhost/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
	$result = $login_client->call('LoginWebservice.login', array('username' => $username, 'password' => $password));
    //log_message(print_r($result, true));
    if(!empty($result['hash']))
        return $result['hash']; //hash 3

	return '';
}

function dump($value)
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

function log_message($text)
{
	echo date('[H:m] ', time()) . $text . '<br />';
}

function debug($client)
{
	echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
}



?>