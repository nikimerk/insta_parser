<?php

class TelegramController {

   public function actionGetUpdates() {
        $telegramApi = new Telegram();

        while (true) {
            sleep(2);

            $messages = $telegramApi->getUpdates();

            echo '<pre>';
            print_r($messages);
            exit;
            foreach ($messages as $message) {
                $telegramApi->sendMessage($message->message->chat->id, 'Привет');
            }
        }

   }

}