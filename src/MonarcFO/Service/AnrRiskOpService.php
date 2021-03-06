<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use MonarcFO\Model\Entity\Asset;
use MonarcFO\Model\Entity\InstanceRiskOp;
use MonarcFO\Model\Table\InstanceRiskOpTable;

/**
 * Anr RiskOp Service
 *
 * Class AnrRiskOpService
 * @package MonarcFO\Service
 */
class AnrRiskOpService extends \MonarcCore\Service\AbstractService
{
    protected $filterColumns = [];
    protected $dependencies = ['anr', 'scale'];
    protected $instanceRiskOpService;
    protected $instanceTable;
    protected $rolfRiskTable;
    protected $rolfRiskService;
    protected $objectTable;
    protected $anrTable;
    protected $userAnrTable;

    /**
     * Find In Fields
     *
     * @param $obj
     * @param $search
     * @param array $fields
     * @return bool
     */
    protected function findInFields($obj, $search, $fields = [])
    {
        foreach ($fields as $field) {
            if (stripos((is_object($obj) ? $obj->{$field} : $obj[$field]), $search) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Risks Op
     *
     * @param $anrId
     * @param null $instance
     * @param array $params
     * @return array
     */
    public function getRisksOp($anrId, $instance = null, $params = [])
    {
        /** @var InstanceTable $instanceTable */
        $instanceTable = $this->get('instanceTable');

        $instances = [];
        if ($instance) {
            $instanceEntity = $instanceTable->getEntity($instance['id']);
            $instances[] = $instanceEntity;

            // Get children instances
            $instanceTable->initTree($instanceEntity);
            $temp = isset($instanceEntity->parameters['children']) ? $instanceEntity->parameters['children'] : [];
            while (!empty($temp)) {
                $sub = array_shift($temp);
                $instances[] = $sub;

                if (!empty($sub->parameters['children'])) {
                    foreach ($sub->parameters['children'] as $subsub) {
                        array_unshift($temp, $subsub);
                    }
                }
            }
        } else {
            $instances = $instanceTable->getEntityByFields(['anr' => $anrId]);
        }
        $instancesIds = [];
        $instancesInfos = [];
        foreach ($instances as $i) {
            if ($i->get('asset')->get('type') == Asset::TYPE_PRIMARY) {
                $instancesIds[] = $i->id;
                $instancesInfos[$i->id] = [
                    'id' => $i->id,
                    'scope' => $i->object->scope,
                    'name1' => $i->name1,
                    'name2' => $i->name2,
                    'name3' => $i->name3,
                    'name4' => $i->name4
                ];
            }
        }

        //retrieve risks instances
        /** @var InstanceRiskOpService $instanceRiskServiceOp */
        $instanceRiskOpService = $this->get('instanceRiskOpService');
        $instancesRisksOp = $instanceRiskOpService->getInstancesRisksOp($instancesIds, $anrId);

        //order by net risk
        $tmpInstancesRisksOp = [];
        $tmpInstancesMaxRisksOp = [];
        foreach ($instancesRisksOp as $instancesRiskOp) {
            $tmpInstancesRisksOp[$instancesRiskOp->id] = $instancesRiskOp;
            $tmpInstancesMaxRisksOp[$instancesRiskOp->id] = $instancesRiskOp->cacheNetRisk;
        }
        arsort($tmpInstancesMaxRisksOp);
        $instancesRisksOp = [];
        foreach ($tmpInstancesMaxRisksOp as $id => $tmpInstancesMaxRiskOp) {
            $instancesRisksOp[] = $tmpInstancesRisksOp[$id];
        }

        $riskOps = [];
        foreach ($instancesRisksOp as $instanceRiskOp) {
            // Process filters
            if (isset($params['kindOfMeasure'])) {
                if ($instanceRiskOp->kindOfMeasure != $params['kindOfMeasure']) {
                    continue;
                }
            }

            if (isset($params['thresholds'])) {
                $min = $params['thresholds'];

                if ($instanceRiskOp->cacheNetRisk < $min) {
                    continue;
                }
            }

            if (isset($params['keywords']) && !empty($params['keywords'])) {
                if (!$this->findInFields($instanceRiskOp, $params['keywords'], ['riskCacheLabel1', 'riskCacheLabel2', 'riskCacheLabel3', 'riskCacheLabel4',
                    'riskCacheDescription1', 'riskCacheDescription2', 'riskCacheDescription3', 'riskCacheDescription4', 'comment'])
                ) {
                    continue;
                }
            }

            // Add risk
            $riskOps[] = [
                'id' => $instanceRiskOp->id,
                'instanceInfos' => isset($instancesInfos[$instanceRiskOp->instance->id]) ? $instancesInfos[$instanceRiskOp->instance->id] : [],
                'label1' => $instanceRiskOp->riskCacheLabel1,
                'label2' => $instanceRiskOp->riskCacheLabel2,
                'label3' => $instanceRiskOp->riskCacheLabel3,
                'label4' => $instanceRiskOp->riskCacheLabel4,

                'description1' => $instanceRiskOp->riskCacheDescription1,
                'description2' => $instanceRiskOp->riskCacheDescription2,
                'description3' => $instanceRiskOp->riskCacheDescription3,
                'description4' => $instanceRiskOp->riskCacheDescription4,

                'netProb' => $instanceRiskOp->netProb,
                'netR' => $instanceRiskOp->netR,
                'netO' => $instanceRiskOp->netO,
                'netL' => $instanceRiskOp->netL,
                'netF' => $instanceRiskOp->netF,
                'netP' => $instanceRiskOp->netP,
                'cacheNetRisk' => $instanceRiskOp->cacheNetRisk,

                'brutProb' => $instanceRiskOp->brutProb,
                'brutR' => $instanceRiskOp->brutR,
                'brutO' => $instanceRiskOp->brutO,
                'brutL' => $instanceRiskOp->brutL,
                'brutF' => $instanceRiskOp->brutF,
                'brutP' => $instanceRiskOp->brutP,
                'cacheBrutRisk' => $instanceRiskOp->cacheBrutRisk,

                'kindOfMeasure' => $instanceRiskOp->kindOfMeasure,
                'comment' => $instanceRiskOp->comment,
                't' => (($instanceRiskOp->kindOfMeasure == InstanceRiskOp::KIND_NOT_TREATED) || (!$instanceRiskOp->kindOfMeasure)) ? false : true,

                'targetedProb' => $instanceRiskOp->targetedProb,
                'targetedR' => $instanceRiskOp->targetedR,
                'targetedO' => $instanceRiskOp->targetedO,
                'targetedL' => $instanceRiskOp->targetedL,
                'targetedF' => $instanceRiskOp->targetedF,
                'targetedP' => $instanceRiskOp->targetedP,
                'cacheTargetedRisk' => $instanceRiskOp->cacheTargetedRisk,

                'specific' => $instanceRiskOp->specific,
            ];
        }

        return $riskOps;
    }

    /**
     * Create Specific Risk Op
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function createSpecificRiskOp($data)
    {
        $data['specific'] = 1;

        $instance = $this->instanceTable->getEntity($data['instance']);
        $data['instance'] = $instance;
        $data['object'] = $this->objectTable->getEntity($instance->object->id);

        if ($data['source'] == 2) {
            // Create a new risk
            $anr = $this->anrTable->getEntity($data['anr']);

            $label = $data['label'];
            $desc = (isset($data['description'])) ? $data['description'] : '';
            unset($data['label']);
            unset($data['description']);

            $riskData = [
                'anr' => $anr,
                'code' => $data['code'],
                'label' . $anr->language => $label,
                'description' . $anr->language => $desc,
            ];
            $data['risk'] = $this->rolfRiskService->create($riskData, true);
        } else {
            // Check if we don't already have it
            /** @var InstanceRiskOpTable $table */
            $table = $this->get('table');
            if ($table->getEntityByFields(['anr' => $data['anr'], 'instance' => $data['instance']->id, 'rolfRisk' => $data['risk']])) {
                throw new \Exception("This risk already exists in this instance", 412);
            }

        }

        // Install an existing risk
        $sourceRiskId = $data['risk'];
        $risk = $this->rolfRiskTable->getEntity($sourceRiskId);

        $data['rolfRisk'] = $risk;
        $data['riskCacheCode'] = $risk->code;
        $data['riskCacheLabel1'] = $risk->label1;
        $data['riskCacheLabel2'] = $risk->label2;
        $data['riskCacheLabel3'] = $risk->label3;
        $data['riskCacheLabel4'] = $risk->label4;
        $data['riskCacheDescription1'] = $risk->description1;
        $data['riskCacheDescription2'] = $risk->description2;
        $data['riskCacheDescription3'] = $risk->description3;
        $data['riskCacheDescription4'] = $risk->description4;

        return $this->create($data, true);
    }

    /**
     * Delete From Anr
     *
     * @param $id
     * @param null $anrId
     * @return mixed
     * @throws \Exception
     */
    public function deleteFromAnr($id, $anrId = null)
    {
        $entity = $this->get('table')->getEntity($id);

        if (!$entity->specific) {
            throw new \Exception('You can not delete a not specific risk', 412);
        }

        return parent::deleteFromAnr($id, $anrId);
    }
}
