Yii2 Slack component
====================
Simple Slack notifier

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist chornij/yii2-slack "~1.0"
```

or add

```
"chornij/yii2-slack": "~1.0"
```

to the require section of your `composer.json` file.

Usage
-----
To use Slack you need to set up Slack component:
```php
'components' => [
    'slack' => [
        'class' => 'chornij\slack\Client',
        'url' => 'https://hooks.slack.com/services/T00000000/B00000000/PS0000000000000000000000',
        'username' => 'My app backend',
    ],
],
```

You can also use it for logging:
```php
'components' => [
    'log' => [
        'targets' => [
            'slack-errors' => [
                'class' => 'chornij\slack\log\SlackTarget',
                'levels' => ['error'],
                'emoji' => ':monkey_face:',
                'exportInterval' => 1,
                'logVars' => [],
            ],
        ],
    ],
],
```
