<?php

class Plugin_Message {
    
    const Error = 'Message_Error';
    const Message = 'Message_Message';

    static function addError($text) {
        $m = new Prodom_Session_Namespace(self::Error);
        $m->array[] = $text;
    }

    static function addMessage($text) {
        $m = new Prodom_Session_Namespace(self::Message);
        $m->array[] = $text;
    }

    public static function drawError() {
        self::draw(self::Error);
    }

    public static function drawMessage() {
        self::draw(self::Message);
    }

    private static function draw($subject) {
        // достаем из сессии ошибки
        $m = new Prodom_Session_Namespace($subject);
        if (!is_array($m->array) || !count($m->array)) {
            return;
        }
        $class = ($subject == self::Message) ? 'success' : 'error';
        ?>
        <div class="alert alert-<?=$class?>">
            <?foreach ($m->array as $message) {?>
                <?=$message?><br>
            <?}?>
        </div>
        <?
        // очистка сообщений
		Prodom_Session::namespaceUnset($subject);
    }

}