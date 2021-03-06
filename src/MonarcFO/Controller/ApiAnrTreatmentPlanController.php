<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Controller;

use MonarcFO\Service\AnrRecommandationRiskService;
use Zend\View\Model\JsonModel;

/**
 * Api Anr Treatment Plan
 *
 * Class ApiAnrTreatmentPlanController
 * @package MonarcFO\Controller
 */
class ApiAnrTreatmentPlanController extends ApiAnrAbstractController
{
    protected $name = 'recommandations-risks';

    /**
     * Get List
     *
     * @return JsonModel
     * @throws \Exception
     */
    public function getList()
    {
        $anrId = (int)$this->params()->fromRoute('anrid');
        if (empty($anrId)) {
            throw new \Exception('Anr id missing', 412);
        }

        /** @var AnrRecommandationRiskService $service */
        $service = $this->getService();
        $entities = $service->getTreatmentPlan($anrId);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel([
            $this->name => $entities
        ]);
    }

    /**
     * Get
     *
     * @param mixed $id
     * @return JsonModel
     * @throws \Exception
     */
    public function get($id)
    {
        $anrId = (int)$this->params()->fromRoute('anrid');
        if (empty($anrId)) {
            throw new \Exception('Anr id missing', 412);
        }

        /** @var AnrRecommandationRiskService $service */
        $service = $this->getService();
        $entities = $service->getTreatmentPlan($anrId, $id);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        if (count($entities)) {
            return new JsonModel($entities[0]);
        } else {
            throw new \Exception('ENtity not exist', 412);
        }


    }

    /**
     * Delete List
     *
     * @param mixed $data
     * @return JsonModel
     * @throws \Exception
     */
    public function deleteList($data)
    {
        $anrId = (int)$this->params()->fromRoute('anrid');
        if (empty($anrId)) {
            throw new \Exception('Anr id missing', 412);
        }

        /** @var AnrRecommandationRiskService $service */
        $service = $this->getService();
        $service->initPosition($anrId);

        return new JsonModel(['status' => 'ok']);
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($token, $data)
    {
        return $this->methodNotAllowed();
    }
}