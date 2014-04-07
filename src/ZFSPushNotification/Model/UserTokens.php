<?php

namespace ZFSPushNotification\Model;

use DomainModel\Object\DomainObjectMagic;

/**
 * Class UserTokens
 * @property int userId
 * @property string token
 * @property string created
 * @package ZFSPushNotification\Model
 */
class UserTokens extends DomainObjectMagic
{
    /** @var array */
    protected $primaryColumns = array(
        'user_id',
        'token',
    );

    protected $fieldToColumnMap = array(
        'userId' => 'user_id'
    );
}
