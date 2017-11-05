<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');

$channelAccessToken = '8iI6qpZmG9WHsYoZOxD6oGbAd9ZMaLUm4oJwovhKFZNb3TnkSddgGZ0873Ai2WNzbeenBI4h1WrJy9FMhkn9rDUQ4ZjuiP6Dhhaa2K0+kob1XZwr2bKXcNoZmc4FzVnV15R031CpvM/0hVK04PyfTQdB04t89/1O/w1cDnyilFU=';
$channelSecret = 'd8f52411c64d8d85df91c594a46a5bda';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
                                    
            $json = file_get_contents('https://spreadsheets.google.com/feeds/list/1JpQkC6LI0-f09emn11osToA66eoFJJFP7Gmh8ZaBOIU/od6/public/values?alt=json');
            $data = json_decode($json, true);
            $result = array();

            foreach ($data['feed']['entry'] as $item) {
                $keywords = explode(',', $item['gsx$keyword']['$t']);

                foreach ($keywords as $keyword) {
                    if (mb_strpos($message['text'], $keyword) !== false) {
                        $candidate = array(
                                'type' => 'text',
                                'text' => $item['gsx$content']['$t'],
                                    ),
                                ),
                            );
                        array_push($result, $candidate);
                    }
                }
            }
            switch ($message['type']) {
                case 'text':
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                'text' => $message['text'].'讓我想想喔…',
                            ),                           
                            array(
                                'type' => 'sticker',
                                'packageId' => '1',
                                'stickerId' => '2',
                            )
                        )
                    ));
                    break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
