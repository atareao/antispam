<?php

include('params.inc');
require_once('utils.php');

$request = file_get_contents("php://input");
mlog($request);
$request = json_decode($request);
if(isset($request->user_id))
{
	$user_id = $request->user_id;
	$name = $request->name;

	session_write_close(); //close the session
	fastcgi_finish_request();
	sleep(60);
	http_post($WEBHOOK_URL.'/webhook.php',
		["internal" => "update",
		"user_id"   => $user_id,
		"name"	    => $name]);
}
