ZFSPushNotification
===================

[![Latest Stable Version](https://poser.pugx.org/zfstarter/zfs-push-notification/v/stable.png)](https://packagist.org/packages/zfstarter/zfs-push-notification)
[![Total Downloads](https://poser.pugx.org/zfstarter/zfs-push-notification/downloads.png)](https://packagist.org/packages/zfstarter/zfs-push-notification)
[![Latest Unstable Version](https://poser.pugx.org/zfstarter/zfs-push-notification/v/unstable.png)](https://packagist.org/packages/zfstarter/zfs-push-notification)
[![License](https://poser.pugx.org/zfstarter/zfs-push-notification/license.png)](https://packagist.org/packages/zfstarter/zfs-push-notification)

ZFSPushNotification

###Установка:

Добавляем в `composer.json`:

```json
{
    "require-dev": {
        "zfstarter/zfs-push-notification": "dev-master"
    }
}
```

И обновляем зависимость:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update


В config\autoload\global.php

указываем SMTP настройки, дефолтные мыло и имя отправителя, а также, если необходимо заголовки:

```php
return array(
//...
    'ZFSPushNotification' => array(
        'pathToPem' => realpath('file.p12.pem'),
        'sandbox' => true,
    ),
);
```

В config\autoload\application.config.php
включаем модуль
```php
    'modules'  => array(
        //...
        'ZFSPushNotification'
    ),
);
```

Также нужно убедиться, что у вас уже создана под него табличка:
```sql
CREATE TABLE `user_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

###Отправка мыла:
```php
use ZFStarterMail\Model\Mail;
//...
$params = array(
    'userId' => 22,
    'message' => 'My notification!!!',
    'actionLocKey' => 'PLAY',
    'locKey' => 'GAME_PLAY_REQUEST_FORMAT',
    'locArgs' => array('Jenna', 'Frank'),
    'launchImage' => 'Play.png',
    'badge' => 5,
    'sound' => 'bingbong.aiff',
);
PushNotification::sendNotification($this->getServiceLocator(), $params);
```
