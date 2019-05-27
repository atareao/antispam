<?php
include('params.inc');
require_once('telegram.php');
require_once('utils.php');

try
{
	$request = file_get_contents("php://input");
	mlog($request);
	$request = json_decode($request);
	if(isset($request->callback_query))
	{
		if(file_exists("members.json"))
		{
			$members = json_decode(file_get_contents("members.json"), true);
			$user_id = $request->callback_query->from->id;
			$name = $request->callback_query->from->first_name;
			mlog('01');
			if(in_array($user_id, array_keys($members)))
			{
				mlog('11');
				if($request->callback_query->data == $members[$user_id]['nature'])
				{
					mlog(" - ".$request->callback_query->data." == ".$members[$user_id]['nature']);
					$members[$user_id]['nature'] = 'humane';
					file_put_contents('members.json', json_encode($members));
					sendMessage($GROUP, "Bienvenido a Ubuntizados,  <strong>$name</strong>, como ser humano que eres. Recuerda respetar las normas ancladas en la cabecera y si quieres, visita la web de mi amo https://www.atareao.es");
				}
				else
				{
					$members[$user_id]['nature'] = 'bot';
                                        file_put_contents('members.json', json_encode($members));
                                        kickChatMember($GROUP, $user_id);
					sendMessage($GROUP, "Me lo imaginaba <strong>$name</strong>, <strong>eres un bot</strong>. En este grupo solo puede haber un bot y ese soy <strong>yo</strong>. <em>Sayonara, baby</em>!");
				}
			}
		}
	}
	else if(isset($request->internal) && $request->internal == 'update')
	{
		$members = json_decode(file_get_contents("members.json"), true);
		$user_id = $request->user_id;
		$name = $request->name;
		if(in_array($user_id, array_keys($members)) && $members[$user_id]['nature'] != 'humane')
		{
			$members[$user_id]['nature'] = 'bot';
	        	file_put_contents('members.json', json_encode($members));
                	sendMessage($GROUP, "Te he dado suficiente tiempo para contestar, y me has ignorado <strong>$name</strong>. Supongo que <strong>eres un bot</strong>. En este grupo solo puede haber un bot y ese soy <strong>yo</strong>. <em>Sayonara, baby</em>!");
                	kickChatMember($GROUP, $user_id);
		}
	}
	else if(isset($request->message->new_chat_member))
	{
		if(file_exists("members.json"))
		{
			$members = json_decode(file_get_contents("members.json"), true);
		}
		$name = $request->message->new_chat_member->first_name;
		$user_id = $request->message->new_chat_member->id;
		if(isset($members) && in_array($user_id, array_keys($members)) && $members[$user_id]['nature'] == 'humane')
		{
			return;
		}
		if($request->message->new_chat_member->is_bot == TRUE || 
		  (isset($members) && in_array($user_id, array_keys($members)) && $members[$user_id]['nature'] != 'humane'))
		{
			sendMessage($GROUP, "Lo siento <strong>$name</strong>, solo puede haber un bot y ese soy <strong>yo</strong>. <em>Sayonara, baby</em>!");
			kickChatMember($GROUP, $user_id);
		}
		else
		{
			if(!isset($members) || !in_array($user_id, array_keys($members)))
			{
				$text = "Amigo <strong>$name</strong>, en este grupo de Ubuntu solo tienen cabida seres humanos y un bot (que soy yo). ¿Cual es tu naturaleza?";
				$answer = sendMessage2($GROUP, $text);
				if(!isset($members))
				{
					$members = [];
				}
				$members[$user_id]['nature'] = $answer;
				file_put_contents('members.json', json_encode($members));
				http_post($WEBHOOK_URL.'/slow-script.php',
					['user_id' => $user_id,
					 'name'    => $name]);
			}
		}
	}
}
catch(Exception $e)
{
	mlog("Error: ".$e->getMessage());
}
