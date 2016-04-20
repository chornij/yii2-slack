<?php

namespace chornij\slack;

use yii\base\Component;
use yii\helpers\Json;

/**
 * Class Client
 *
 * @package chornij\slack
 */
class Client extends Component
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $defaultText = 'Message from Yii application';

    /**
     * @param string $message
     * @param string $icon
     * @param array $attachments
     */
    public function send($message, $icon, $attachments)
    {
        if ($this->url) {
            $request = Json::encode($this->getPayload($message, $icon, $attachments));

            \Yii::trace($request, __METHOD__);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['payload' => $request]);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            \Yii::trace($response, __METHOD__);

            curl_close($ch);
        }
    }

    /**
     * @param string|null $text
     * @param string|null $icon
     * @param array $attachments
     * @return array
     */
    protected function getPayload($text = null, $icon = null, $attachments = [])
    {
        if (is_null($text)) {
            $text = $this->defaultText;
        }

        $payload = [
            'text' => $text,
            'username' => $this->username,
            'attachments' => $attachments,
        ];

        if (!is_null($icon)) {
            $payload['icon_emoji'] = $icon;
        }

        return $payload;
    }
}
