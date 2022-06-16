<?php

function validate_message($message){
    $max_length = 4096;
    if(strlen($message) > $max_length) $message = substr($message, 0, $max_length);
    return $message;
}

function get_message($message_template, $fields_str){
    $message = $message_template;
    if(!empty($fields_str)){
        $fields = explode(',', $fields_str);
        if(is_array($fields)){
            foreach ($fields as $field){
                $field = trim($field);
                $field_tag = '%' . $field . '%';
                $field_value = !empty($_POST[$field]) ? $_POST[$field] : (!empty($_GET[$field]) ? $_GET[$field] : '');
                $message = str_replace($field_tag, $field_value, $message);
            }
        }
    }
    return $message;
}

function send_to_telegram($bot_token, $chat_id, $message)
{
    if(!empty($bot_token) && !empty($chat_id) && !empty($message))
    {
        $params=[
                'chat_id'=>$chat_id,
                   'text'=>$message
        ];
        $url= "https://api.telegram.org/bot" . $bot_token . '/sendMessage?'. http_build_query($params);
        $resp = file_get_contents($url);
    }
}

//Start
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

$options = get_option( 'telehook_options' );
if(!empty($options['bot_token']) && !empty($options['chat_id']) && !empty($options['message_template'])){
    $message = get_message($options['message_template'], $options['fields']);
    $message = validate_message($message);
    send_to_telegram($options['bot_token'], $options['chat_id'], $message);
}