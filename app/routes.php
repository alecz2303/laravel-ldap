<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	//return View::make('hello');
	  $username = 'administrador';
	  $password = 'Ericko2303';


	if (Auth::attempt(array('username' => $username, 'password' => $password)))
	{
		error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

		function get_groups($user) {
		// Active Directory server
		$ldap_host = "192.168.1.10";
		 
		// Active Directory DN, base path for our querying user
		$ldap_dn = "OU=Notaria39,DC=NOTARIA39,DC=chiapas";
		 
		// Active Directory user for querying
		$query_user = "NOTARIA39\administrador";
		$password = "Ericko2303";
		 
		// Connect to AD
		$ldap = ldap_connect($ldap_host) or die("Could not connect to LDAP");
		ldap_bind($ldap,$query_user,$password) or die("Could not bind to LDAP");
		// Search AD
		$results = ldap_search($ldap,$ldap_dn,"(samaccountname=$user)",array("memberof","primarygroupid"));
		$entries = ldap_get_entries($ldap, $results);
		// No information found, bad user
		if($entries['count'] == 0) return false;
		// Get groups and primary group token
		$output = $entries[0]['memberof'];
		$token = $entries[0]['primarygroupid'][0];
		// Remove extraneous first entry
		array_shift($output);
		// We need to look up the primary group, get list of all groups
		$results2 = ldap_search($ldap,$ldap_dn,"(objectcategory=group)",array("distinguishedname","primarygrouptoken"));
		$entries2 = ldap_get_entries($ldap, $results2);
		// Remove extraneous first entry
		array_shift($entries2);
		// Loop through and find group with a matching primary group token
		foreach($entries2 as $e) {
			if($e['primarygrouptoken'][0] == $token) {
				// Primary group found, add it to output array
				$output[] = $e['distinguishedname'][0];
				// Break loop
				break;
			}
		}
		 
		return $output;
		
		}
		 
		// Example Usage
		var_dump(get_groups("arueda"));
		echo "<hr>";
		var_dump(get_groups("calvarez"));
	}
});
