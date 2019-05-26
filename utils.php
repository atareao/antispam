<?php
function mlog($text)
{
        $date = date('Y-m-d H:i:s');
        file_put_contents("webhook.log", "$date - $text\n\n", FILE_APPEND);
}

function http_post($url, $json)
{
    $ans = null;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    try
    {
        $data_string = json_encode($json);
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $ans = json_decode(curl_exec($ch));
        if($ans->ok !== TRUE)
        {
            $ans = null;
        }
    }
    catch(Exception $e)
    {
        mlog("Error: ".$e->getMessage());
    }
    curl_close($ch);
    return $ans;
}

