<?php

include('params.inc');

function sendMessage($chat_id, $text)
{
  global $TELEGRAM;
  $query = http_build_query(array(
    'chat_id'                  => $chat_id,
    'text'                     => $text,
    'parse_mode'               => "HTML", // Optional: Markdown | HTML
    'disable_web_page_preview' => TRUE,
  ));

  $response = file_get_contents("$TELEGRAM/sendMessage?$query");
  return $response;
}

function kickChatMember($chat_id, $user_id)
{
        global $TELEGRAM;
        $json = ['chat_id' => $chat_id,
                 'user_id' => $user_id];
        return http_post($TELEGRAM.'/kickChatMember', $json);
}

function sendMessage2($chat_id, $text)
{
        global $TELEGRAM;
        $data = [0 => "Humano",
                 1 => "Animal",
                 2 => "Robot",
                 3 => "Dios",
                 4 => "Extraterrestre",
                 5 => "Mineral",
                 6 => "Vegetal",
                 7 => "Extracorpóreo",
                 8 => "Marciano"];
        $valores = [0,1,2,3,4,5,6,7,8];
        $res = [];
        $answer = -1;
        foreach(range(0, 8) as $i)
        {
                $pos = rand(0, count($valores) - 1);
                $res[$i] = $valores[$pos];
                if($data[$res[$i]] == 'Humano')
                {
                        $answer = $i;
                }
                array_splice($valores, $pos, 1);
        }
        $inline_keyboard = array(
                array(
                        array("text"          => $data[$res[0]],
                              "callback_data" => "0"),
                        array("text"          => $data[$res[1]],
                              "callback_data" => "1"),
                        array("text"          => $data[$res[2]],
                              "callback_data" => "2")
                      ),
                array(
                        array("text"          => $data[$res[3]],
                              "callback_data" => "3"),
                        array("text"          => $data[$res[4]],
                              "callback_data" => "4"),
                        array("text"          => $data[$res[5]],
                              "callback_data" => "5")
                      ),
                array(
                        array("text"          => $data[$res[6]],
                              "callback_data" => "6"),
                        array("text"          => $data[$res[7]],
                              "callback_data" => "7"),
                        array("text"          => $data[$res[8]],
                              "callback_data" => "8")
                      )
              );

        $json = ['chat_id'       => $chat_id,
                 'text'          => $text,
                 'parse_mode'    => 'HTML',
                 'reply_markup'  => array('inline_keyboard' => $inline_keyboard)];
        http_post($TELEGRAM.'/sendMessage', $json);
        return $answer;
}
