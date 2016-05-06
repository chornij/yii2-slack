<?php

namespace chornij\slack\log;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;
use yii\web\Request;

/**
 * Class SlackTarget
 *
 * @package chornij\slack\log
 */
class SlackTarget extends Target
{
    /**
     * @var string
     */
    public $emoji;

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        if (\Yii::$app->has('slack')) {
            \Yii::$app->slack->send('Log error messages', $this->emoji, $this->getAttachments());
        }
    }

    /**
     * @return array
     */
    protected function getAttachments()
    {
        $attachments = [];

        foreach ($this->messages as $index => $message) {
            $attachment = [
                'fallback' => 'Log message ' . ($index + 1),
                'text' => $this->formatMessage($message),
                'color' => $this->getLevelColor($message[1]),
                'fields' => [
                    [
                        'title' => 'Application ID',
                        'value' => \Yii::$app->id,
                        'short' => true,
                    ],
                ],
            ];

            if (\Yii::$app->has('request') && \Yii::$app->request instanceof Request) {
                $attachment['fields'][] = [
                    'title' => 'Url',
                    'value' => \Yii::$app->request->absoluteUrl,
                    'short' => true,
                ];
            }

            $attachments[] = $attachment;
        }
        return $attachments;
    }

    /**
     * @param int $level
     * @return string
     */
    private function getLevelColor($level)
    {
        $colors = [
            Logger::LEVEL_ERROR => 'danger',
            Logger::LEVEL_WARNING => 'danger',
            Logger::LEVEL_INFO => 'good',
            Logger::LEVEL_PROFILE => 'warning',
            Logger::LEVEL_TRACE => 'warning',
        ];

        return ArrayHelper::getValue($colors, $level, 'good');
    }

    /**
     * @inheritdoc
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;

        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Exception) {
                $text = (string)$text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        return $text . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
    }
}
