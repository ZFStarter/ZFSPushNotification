<?php

namespace ZFSPushNotification\Controller;

use ZFSPushNotification\Model\Manager\UserTokensManager;
use ZFSPushNotification\Model\PushNotification;
use Zend\Mvc\Controller\AbstractActionController;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $params = array(
            'userId' => 22,
            'message' => 'My test notification!',
        );

        if ($this->getRequest()->isPost()) {
            /** @var UserTokensManager $userTokensManager */
            $userTokensManager = $this->getServiceLocator()->get('UserTokensManager');
            $token = $this->params()->fromQuery('token');
            $userToken = $userTokensManager->saveToken($this->identity()->id, $token);
            $params['userId'] = $userToken->userId;

            PushNotification::sendNotification($this->getServiceLocator(), $params);
        } else {
            PushNotification::sendNotification($this->getServiceLocator(), $params);
        }
        exit('ok');
    }
}
