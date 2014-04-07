<?php

namespace ZFSPushNotification\Model\Gateway;

use DomainModel\Feature\FilterColumnsFeature;
use DomainModel\Gateway\DomainObjectTableGatewayAbstractFactory;
use DomainModel\Gateway\DomainObjectTableGatewayAbstractFactory\Options;

/**
 * Class GatewayAbstractFactory
 * @package ZFSPushNotification\Model\Gateway
 */
class GatewayAbstractFactory extends DomainObjectTableGatewayAbstractFactory
{
    public function __construct()
    {
        $this->provides = array(
            'UserTokensGateway' => array(
                Options::OPTION_TABLE_NAME              => 'user_tokens',
                Options::OPTION_TABLE_FEATURES          => array(new FilterColumnsFeature()),
                Options::OPTION_DOMAIN_OBJECT_PROTOTYPE => 'ZFSPushNotification\Model\UserTokens',
            )
        );
    }
}
