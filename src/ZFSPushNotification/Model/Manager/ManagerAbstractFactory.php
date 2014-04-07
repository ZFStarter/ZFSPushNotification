<?php
namespace ZFSPushNotification\Model\Manager;

use Common\ServiceManager\AbstractServiceAbstractFactory;

/**
 * Class ManagerAbstractFactory
 * @package ZFSPushNotification\Model\Manager
 */
class ManagerAbstractFactory extends AbstractServiceAbstractFactory
{
    protected $provides = array(
        'UserTokensManager' => 'ZFSPushNotification\Model\Manager\UserTokensManager'
    );
}
