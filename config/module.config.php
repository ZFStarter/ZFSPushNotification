<?php
return array(
    'ZFSPushNotification' => array(
        'pathToPem' => realpath('file.p12.pem'),
        'sandbox' => true,
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'ZFSPushNotification\Model\Manager\ManagerAbstractFactory',
            'ZFSPushNotification\Model\Gateway\GatewayAbstractFactory',
        ),
    )
);
