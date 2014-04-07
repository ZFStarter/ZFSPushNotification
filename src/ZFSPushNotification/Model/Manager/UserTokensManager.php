<?php
namespace ZFSPushNotification\Model\Manager;

use Common\ServiceManager\AbstractService;
use DomainModel\Gateway\DomainObjectTableGateway;
use ZFSPushNotification\Model\UserTokens;

/**
 * Class UserTokensManager
 * @package ZFSPushNotification\Model\Manager
 */
class UserTokensManager extends AbstractService
{
    /**
     * @return DomainObjectTableGateway
     */
    protected function getGateway()
    {
        return $this->serviceManager->get('UserTokensGateway');
    }

    /**
     * @param int $userId
     *
     * @return array|null
     */
    public function getTokensByUserId($userId)
    {
        $result = $this->getGateway()->select(array('user_id' => $userId))->toArray();
        $tokens = array();
        foreach ($result as $row) {
            $tokens[] = $row['token'];
        }
        return $tokens;
    }

    /**
     * @param int $userId
     * @param string $token
     *
     * @return UserTokens|null
     */
    public function getByUserIdAndToken($userId, $token)
    {
        return $this->getGateway()->select(array('user_id' => $userId, 'token' => $token))->current();
    }

    /**
     * Save token
     *
     * @param int $userId
     * @param string $token
     *
     * @return null|UserTokens
     */
    public function saveToken($userId, $token)
    {
        $userToken = $this->getByUserIdAndToken($userId, $token);
        if ($userToken) {
            return $userToken;
        }

        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'userId' => $userId,
            'token' => $token,
        );

        $userToken = new UserTokens($data);

        if (!$this->getGateway()->insertObject($userToken)) {
            return null;
        }

        $userToken->id = $this->getGateway()->getLastInsertValue();

        return $userToken;
    }
}
