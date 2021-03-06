<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Controller;

use Zend\View\Model\JsonModel;

/**
 * Api Admin Users Roles Controller
 *
 * Class ApiAdminUsersRolesController
 * @package MonarcFO\Controller
 */
class ApiAdminUsersRolesController extends \MonarcCore\Controller\AbstractController
{
    protected $name = 'roles';

    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        $request = $this->getRequest();
        $token = $request->getHeader('token');

        $currentUserRoles = $this->getService()->getByUserToken($token);

        return new JsonModel($currentUserRoles);
    }

    /**
     * @param mixed $id
     * @return JsonModel
     */
    public function get($id)
    {
        $userRoles = $this->getService()->getByUserId($id);

        return new JsonModel([
            'count' => count($userRoles),
            $this->name => $userRoles
        ]);
    }

    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}