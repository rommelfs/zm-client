<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Controller;

use MonarcCore\Model\Entity\AbstractEntity;
use Zend\View\Model\JsonModel;

/**
 * Api ANR Objects Duplication Controller
 *
 * Class ApiAnrObjectsDuplicationController
 * @package MonarcFO\Controller
 */
class ApiAnrObjectsDuplicationController extends ApiAnrAbstractController
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
        if (isset($data['id'])) {
            $id = $this->getService()->duplicate($data, AbstractEntity::FRONT_OFFICE);

            return new JsonModel([
                'status' => 'ok',
                'id' => $id,
            ]);
        } else {
            throw new \Exception('Object to duplicate is required');
        }
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
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