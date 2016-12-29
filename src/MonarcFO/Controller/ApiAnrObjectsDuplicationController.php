<?php

namespace MonarcFO\Controller;

use MonarcCore\Model\Entity\AbstractEntity;
use MonarcCore\Model\Entity\Object;
use MonarcCore\Service\ObjectObjectService;
use MonarcCore\Service\ObjectService;
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
            $id = $this->getService()->duplicate($data);

            return new JsonModel(
                array(
                    'status' => 'ok',
                    'id' => $id,
                )
            );
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