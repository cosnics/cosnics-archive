<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', -1);
ini_set('memory_limit',-1);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/user_import.csv';
$user = parse_csv($file);
/*
 * change location to the location of the test server
 */
$location = 'http://www.dokeosplanet.org/skible/user/webservices/webservices_user.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';



update_user($user[0]);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{
	if(file_exists($file) && $fp = fopen($file, "r"))
	{
		$keys = fgetcsv($fp, 1000, ";");
		$users = array();

		while($user_data = fgetcsv($fp, 1000, ";"))
		{
			$user = array();
			foreach($keys as $index => $key)
			{
				$user[$key] = trim($user_data[$index]);
			}
			$users[] = $user;
		}
		fclose($fp);
	}
	else
	{
		log("ERROR: Can't open file ($file)");
	}

	return $users;
}

function update_user($user)
{
	global $hash, $client;
	log_message('Updating user ' . $user['username']);
	$hash = ($hash == '') ? login() : $hash;

	 $result = $client->call('WebServicesUser.update_user', array('input' => $user, 'hash' => $hash));
    if($result == 1)
    {
        log_message(print_r('User successfully updated', true));
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
	$username = 'Samumon';
    //$username = 'Soliber';

    $password = hash('sha1','193.190.172.141'.hash('sha1','60d9efdb7c'));
    //$password = hash('sha1','127.0.0.1'.hash('sha1','werk'));

	/*
     * change location to server location for the wsdl
     */
	$login_client = new nusoap_client('http://www.dokeosplanet.org/skible/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
	$result = $login_client->call('LoginWebservice.login', array('username' => $username, 'password' => $password));

    log_message(print_r($result, true));
    if(is_array($result) && array_key_exists('hash', $result))
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