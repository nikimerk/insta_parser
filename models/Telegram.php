<?php
/**
 * Created by PhpStorm.
 * User: Nikita
 * Date: 31.03.2019
 * Time: 22:18
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientInterface;

class Telegram
{

    protected $token = '';

    protected $update_id;

    protected function query($method, $params = []) {


        $url = 'https://api.telegram.org/bot';

        $url .= $this->token;

        $url .= '/' . $method;

        if (count($params) > 0) {
            $url .= '?' . http_build_query($params);
        }

        $client = new Client([
            'base_uri' => $url
        ]);

        $result = $client->request('GET');

        return json_decode($result->getBody());
    }

    public function getUpdates() {
        $response = $this->query('getUpdates', [
            'offset' => $this->update_id + 1
        ]);

        if (!empty($response)) {
            $this->update_id = $response->result[count($response->result) - 1]->update_id;
        }

        return $response->result;
    }

    public function sendMessage($chat_id, $text) {

        $btn11 = new class{};
        $btn11->text = 'Btn1';
        $btn2 = new class{};
        $btn2->text = 'btn 2';
        $btn3 = new class{};
        $btn3->text = 'btn 3';
        $btn4 = new class{};
        $btn4->text = 'btn 4';
        $reply = new class{};
        $reply->keyboard = [
            [
                $btn11,
                $btn2
            ],
            [
                $btn3,
                $btn4
            ]
        ];




        $response = $this->query('sendMessage', [
            'text' => $text,
            'chat_id' => $chat_id,
            'reply_markup' => json_encode($reply)
        ]);


        return $response->result;
    }


}

