<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use MonarcCore\Model\Table\UserRoleTable;
use MonarcCore\Service\AbstractService;
use MonarcFO\Model\Table\UserAnrTable;
use Zend\Http\Header\GenericHeader;

/**
 * User Role Service
 *
 * Class UserRoleService
 * @package MonarcFO\Service
 */
class UserRoleService extends AbstractService
{
    protected $userAnrCliTable;
    protected $userTable;
    protected $userRoleTable;
    protected $userTokenTable;
    protected $userRoleEntity;
    protected $dependencies = ['user'];

    /**
     * Get List
     *
     * @param int $page
     * @param int $limit
     * @param null $order
     * @param null $filter
     * @param array $options
     * @return array
     */
    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $options = [])
    {
        /** @var UserRoleTable $userRoleTable */
        $userRoleTable = $this->get('userRoleTable');

        return $userRoleTable->getRepository()->createQueryBuilder('t')
            ->select(['t.id', 't.role'])
            ->where('t.user = :id')
            ->setParameter(':id', $filter)
            ->getQuery()->getResult();
    }

    /**
     * Get Entity
     *
     * @param $id
     * @return mixed
     */
    public function getEntity($id)
    {
        return $this->get('userRoleTable')->get($id);
    }

    /**
     * Get By User Id
     *
     * @param $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        /** @var UserRoleTable $userRoleTable */
        $userRoleTable = $this->get('userRoleTable');

        return $userRoleTable->getRepository()->createQueryBuilder('t')
            ->select(['t.id', 't.role'])
            ->where('t.user = :id')
            ->setParameter(':id', $userId)
            ->getQuery()->getResult();
    }

    /**
     * Get By User Token
     *
     * @param $token
     * @return array
     * @throws \Exception
     */
    public function getByUserToken($token)
    {
        if ($token instanceof GenericHeader) {
            $token = $token->getFieldValue();
        }

        //retrieve users for this token
        $userTokenTable = $this->get('userTokenTable');
        $userToken = $userTokenTable->getRepository()->createQueryBuilder('t')
            ->select(['t.id', 'IDENTITY(t.user) as userId', 't.token', 't.dateEnd'])
            ->where('t.token = :token')
            ->setParameter(':token', $token)
            ->getQuery()
            ->getResult();

        if (count($userToken)) {

            //retrieve user access
            $userId = $userToken[0]['userId'];
            $anrs = [];
            /** @var UserAnrTable $userAnrCliTable */
            $userAnrCliTable = $this->get('userAnrCliTable');
            $userAnrs = $userAnrCliTable->getEntityByFields(['user' => $userId]);
            foreach ($userAnrs as $userAnr) {
                $anrs[] = [
                    'anr' => $userAnr->anr->id,
                    'rwd' => $userAnr->rwd
                ];
            }

            return [
                'roles' => $this->getByUserId($userId),
                'anrs' => $anrs
            ];
        } else {
            throw new \Exception('No user');
        }
    }
}