<?php
/**
 * User: naxel
 * Date: 07.04.14 15:42
 */

namespace ZFSPushNotification\Model;

use ZFSPushNotification\Exception\PushNotificationException;
use ZFSPushNotification\Exception\PushNotificationInvalidArgumentException;
use ZFSPushNotification\Model\Manager\UserTokensManager;
use Zend\ServiceManager\ServiceManager;
use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Message\Alert;
use ZendService\Apple\Apns\Response\Message as Response;
use ZendService\Apple\Exception\RuntimeException;

class PushNotification
{

    /**
     * @var ServiceManager
     */
    public static $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     * @param array $params
     * @throws \ZFSPushNotification\Exception\PushNotificationException
     * @throws \ZFSPushNotification\Exception\PushNotificationInvalidArgumentException
     *
     * Example:
     *   $params = array(
     *       'userId' => 22,
     *       'message' => 'My notification!!!',
     *       'actionLocKey' => 'PLAY',
     *       'locKey' => 'GAME_PLAY_REQUEST_FORMAT',
     *       'locArgs' => array('Jenna', 'Frank'),
     *       'launchImage' => 'Play.png',
     *       'badge' => 3,
     *       'sound' => 'bingbong.aiff',
     *   );
     *   PushNotification::sendNotification($this->getServiceLocator(), $params);
     */
    public static function sendNotification(ServiceManager $serviceManager, $params)
    {
        if (!isset($params['message'])) {
            throw new PushNotificationInvalidArgumentException('Empty message');
        }
        if (!isset($params['userId'])) {
            throw new PushNotificationInvalidArgumentException('Empty userId');
        }

        $config = $serviceManager->get('Config');
        if (!isset($config['ZFSPushNotification']['pathToPem']) || !$config['ZFSPushNotification']['pathToPem']) {
            throw new PushNotificationException('Empty "pathToPem"');
        }

        $client = new Client();
        if (isset($config['ZFSPushNotification']['sandbox']) && $config['ZFSPushNotification']['sandbox'] === true) {
            $client->open(Client::SANDBOX_URI, $config['ZFSPushNotification']['pathToPem']);
        } else {
            $client->open(Client::PRODUCTION_URI, $config['ZFSPushNotification']['pathToPem']);
        }

        /** @var UserTokensManager $userTokensManager */
        $userTokensManager = $serviceManager->get('UserTokensManager');
        $tokens = $userTokensManager->getTokensByUserId($params['userId']);

        foreach ($tokens as $token) {
            $message = new Message();
            $message->setId('notification_unique_id_' . time());
            $message->setToken($token);

            $alert = new Alert();

            $alert->setBody($params['message']);
            if (isset($params['badge'])) {
                $message->setBadge($params['badge']);
            }
            if (isset($params['sound'])) {
                $message->setSound($params['sound']);
            }
            if (isset($params['actionLocKey'])) {
                $alert->setActionLocKey($params['actionLocKey']);
            }
            if (isset($params['locKey'])) {
                $alert->setLocKey($params['locKey']);
            }
            if (isset($params['locArgs'])) {
                $alert->setLocArgs($params['locArgs']);
            }
            if (isset($params['locArgs']) && is_array($params['locArgs'])) {
                $alert->setLocArgs($params['locArgs']);
            }
            if (isset($params['launchImage'])) {
                $alert->setLaunchImage($params['launchImage']);
            }

            $message->setAlert($alert);
            try {
                /** @var Response $response */
                $response = $client->send($message);
            } catch (RuntimeException $e) {
                throw new PushNotificationException($e->getMessage());
            }
            $client->close();

            if ($response->getCode() != Response::RESULT_OK) {
                switch ($response->getCode()) {
                    case Response::RESULT_PROCESSING_ERROR:
                        // you may want to retry
                        throw new PushNotificationException('You may want to retry');
                        break;
                    case Response::RESULT_MISSING_TOKEN:
                        // you were missing a token
                        throw new PushNotificationException('You were missing a token');
                        break;
                    case Response::RESULT_MISSING_TOPIC:
                        // you are missing a message id
                        throw new PushNotificationException('You are missing a message id');
                        break;
                    case Response::RESULT_MISSING_PAYLOAD:
                        // you need to send a payload
                        throw new PushNotificationException('You need to send a payload');
                        break;
                    case Response::RESULT_INVALID_TOKEN_SIZE:
                        // the token provided was not of the proper size
                        throw new PushNotificationException('The token provided was not of the proper size');
                        break;
                    case Response::RESULT_INVALID_TOPIC_SIZE:
                        // the topic was too long
                        throw new PushNotificationException('The topic was too long');
                        break;
                    case Response::RESULT_INVALID_PAYLOAD_SIZE:
                        // the payload was too large
                        throw new PushNotificationException('The payload was too large');
                        break;
                    case Response::RESULT_INVALID_TOKEN:
                        // the token was invalid; remove it from your system
                        throw new PushNotificationException('The token was invalid; remove it from your system');
                        break;
                    case Response::RESULT_UNKNOWN_ERROR:
                        // apple didn't tell us what happened
                        throw new PushNotificationException('Apple didn\'t tell us what happened');
                        break;
                }
            }
        }
    }
}
