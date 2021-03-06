<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Controller;

use MonarcCore\Service\PasswordService;
use Zend\View\Model\JsonModel;


/**
 * Api Adin Passwords Controller
 *
 * Class ApiAdminPasswordsController
 * @package MonarcFO\Controller
 */
class ApiAdminPasswordsController extends \MonarcCore\Controller\AbstractController
{
    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     * @throws \Exception
     */
    public function create($data)
    {
        /** @var PasswordService $service */
        $service = $this->getService();
        //password forgotten
        if (!empty($data['email']) && empty($data['password'])) {
            try {
                $service->passwordForgotten($data['email']);
            } catch (\Exception $e) {
                // Ignore the exception: We don't want to leak any data
            }
        }

        //verify token
        if (!empty($data['token']) && empty($data['password'])) {
            $result = $service->verifyToken($data['token']);

            return new JsonModel(['status' => $result]);
        }

        //change password not logged
        if (!empty($data['token']) && !empty($data['password']) && !empty($data['confirm'])) {
            if ($data['password'] == $data['confirm']) {
                $service->newPasswordByToken($data['token'], $data['password']);
            } else {
                throw  new \Exception('Password must be the same', 422);
            }
        }

        return new JsonModel(['status' => 'ok']);
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($token, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

