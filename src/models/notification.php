<?php
namespace src\models;

class notification {
    public function chatworkMsgTemplate($text, $title){
        $msg = <<<TEXT
[info][title]{$title}[/title]{$text}[/info]
TEXT;
        return $msg;
    }
    
    public function chatwork($option){
        \wataridori\ChatworkSDK\ChatworkSDK::setApiKey($option['apiKey']);
        $room = new \wataridori\ChatworkSDK\ChatworkRoom($option['roomId']);
        $text = $this->chatworkMsgTemplate($option['text'], $option['title']);
        $res  = $room->sendMessageToList($option['to'], $text);
    }
}
