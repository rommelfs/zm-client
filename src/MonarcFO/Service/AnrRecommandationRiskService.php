<?php
namespace MonarcFO\Service;

use MonarcFO\Model\Entity\InstanceRisk;
use MonarcFO\Model\Entity\InstanceRiskOp;
use MonarcFO\Model\Entity\Object;
use MonarcFO\Model\Table\AnrTable;
use MonarcFO\Model\Table\InstanceRiskOpTable;
use MonarcFO\Model\Table\InstanceRiskTable;
use MonarcFO\Model\Table\RecommandationHistoricTable;
use MonarcFO\Model\Table\RecommandationMeasureTable;
use MonarcFO\Model\Table\RecommandationRiskTable;
use MonarcFO\Model\Table\RecommandationTable;
use MonarcFO\Service\AbstractService;

/**
 * Anr Recommandation Risk Service
 *
 * Class AnrRecommandationRiskService
 * @package MonarcFO\Service
 */
class AnrRecommandationRiskService extends \MonarcCore\Service\AbstractService
{
    protected $dependencies = ['recommandation', 'asset', 'threat', 'vulnerability'];
    protected $anrTable;
    protected $recommandationTable;
    protected $recommandationHistoricTable;
    protected $recommandationMeasureTable;
    protected $instanceRiskTable;
    protected $instanceRiskOpTable;
    protected $recommandationHistoricEntity;
    protected $anrService;
    protected $anrInstanceService;

    /**
     * Get List
     *
     * @param int $page
     * @param int $limit
     * @param null $order
     * @param null $filter
     * @return mixed
     */
    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null){

        /** @var RecommandationRiskTable $table */
        $table = $this->get('table');
        $recosRisks =  $table->fetchAllFiltered(
            array_keys($this->get('entity')->getJsonArray()),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, $this->filterColumns),
            $filterAnd
        );

        /** @var RecommandationMeasureTable $recommandationMeasureTable */
        $recommandationMeasureTable = $this->get('recommandationMeasureTable');

        foreach($recosRisks as $key => $recoRisk) {

            $recommandationsMeasures = $recommandationMeasureTable->getEntityByFields(['recommandation' => $recoRisk['recommandation']->id]);

            $measures = [];
            foreach ($recommandationsMeasures as $recommandationMeasure) {
                $recommandationMeasure = $recommandationMeasure->getJsonArray();
                $recommandationMeasure['measure'] = $recommandationMeasure['measure']->getJsonArray();
                $measures[] = $recommandationMeasure;
            }

            $recosRisks[$key]['measures'] = $measures;
        }

        return $recosRisks;
    }

    /**
     * Get Treatment Plans
     *
     * @param $anrId
     * @return mixed
     */
    public function getTreatmentPlan($anrId, $id = false){

        //retrieve recommandations risks
        /** @var RecommandationTable $table */
        $table = $this->get('table');
        $params = ['anr' => $anrId];
        if ($id) {
            $params['recommandation'] = $id;
        }
        $recommandationsRisks = $table->getEntityByFields($params);

        //retrieve recommandations
        /** @var RecommandationTable $recommandationTable */
        $recommandationTable = $this->get('recommandationTable');
        $recommandations = $recommandationTable->getEntityByFields(['anr' => $anrId], ['position' => 'ASC', 'importance' => 'DESC']);

        foreach($recommandations as $key => $recommandation) {
            $recommandations[$key] = $recommandation->getJsonArray();
            unset($recommandations[$key]['__initializer__']);
            unset($recommandations[$key]['__cloner__']);
            unset($recommandations[$key]['__isInitialized__']);
            $nbRisks = 0;
            $global = [];
            $risksToUnset = [];
            foreach($recommandationsRisks as $recommandationRisk) {
                if ($recommandationRisk->recommandation->id == $recommandation->id) {
                    //retrieve instance risk associated
                    if ($recommandationRisk->instanceRisk) {
                        if ($recommandationRisk->instanceRisk->kindOfMeasure != InstanceRisk::KIND_NOT_TREATED) {
                            $instanceRisk = $recommandationRisk->instanceRisk;
                            if (is_object($instanceRisk->asset)) {
                                $instanceRisk->asset = $instanceRisk->asset->getJsonArray();
                            }
                            if (is_object($instanceRisk->threat)) {
                                $instanceRisk->threat = $instanceRisk->threat->getJsonArray();
                            }
                            if (is_object($instanceRisk->vulnerability)) {
                                $instanceRisk->vulnerability = $instanceRisk->vulnerability->getJsonArray();
                            }
                            $recommandations[$key]['risks'][] = $instanceRisk->getJsonArray();
                            $nbRisks++;
                        }
                    }
                    //retrieve instance risk op associated
                    if ($recommandationRisk->instanceRiskOp) {
                        if ($recommandationRisk->instanceRiskOp->kindOfMeasure != InstanceRiskOp::KIND_NOT_TREATED) {
                            $recommandations[$key]['risksop'][] = $recommandationRisk->instanceRiskOp->getJsonArray();
                            $nbRisks++;
                        }
                    }

                    //delete risk of global with risk value is not the higher
                    if ($recommandationRisk->objectGlobal) {
                        foreach($global as $glob) {
                            if ($glob['objectId'] == $recommandationRisk->objectGlobal->id) {
                                if ($glob['maxRisk'] < $recommandationRisk->instanceRisk->cacheMaxRisk) {
                                    $risksToUnset[] = $glob['riskId'];
                                } else {
                                    $risksToUnset[] = $recommandationRisk->instanceRisk->id;
                                }
                            }
                        }

                        $global[] = [
                            'objectId' => $recommandationRisk->objectGlobal->id,
                            'maxRisk' => $recommandationRisk->instanceRisk->cacheMaxRisk,
                            'riskId' => $recommandationRisk->instanceRisk->id,
                        ];
                    }
                }
            }

            if (isset($recommandations[$key]['risks'])) {
                foreach ($recommandations[$key]['risks'] as $k => $risk) {
                    if (in_array($risk['id'], $risksToUnset)) {
                        unset($recommandations[$key]['risks'][$k]);
                    }
                }
            }

            if (!$nbRisks) {
                unset($recommandations[$key]);
            }
        }

        return array_values($recommandations);
    }

    /**
     * Create
     *
     * @param $data
     * @param bool $last
     * @return mixed
     */
    public function create($data, $last = true) {

        //$entity = $this->get('entity');
        $class = $this->get('entity');
        $entity = new $class();
        $entity->setLanguage($this->getLanguage());
        $entity->setDbAdapter($this->get('table')->getDb());
        $entity->exchangeArray($data);

        $dependencies =  (property_exists($this, 'dependencies')) ? $this->dependencies : [];
        $this->setDependencies($entity, $dependencies);

        //retrieve risk
        if ($data['op']) {
            /** @var InstanceRiskOpTable $instanceRiskOpTable */
            $instanceRiskOpTable = $this->get('instanceRiskOpTable');
            $risk = $instanceRiskOpTable->getEntity($data['risk']);

            $entity->setInstanceRisk(null);
            $entity->setInstanceRiskOp($risk);

        } else {
            /** @var InstanceRiskTable $instanceRiskOpTable */
            $instanceRiskTable = $this->get('instanceRiskTable');
            $risk = $instanceRiskTable->getEntity($data['risk']);

            $entity->setInstanceRisk($risk);
            $entity->setInstanceRiskOp(null);

            $entity->setAsset($risk->getAsset());
            $entity->setThreat($risk->getThreat());
            $entity->setVulnerability($risk->getVulnerability());
        }

        $entity->setInstance($risk->getInstance());

        if ($risk->getInstance()->getObject()->get('scope') == Object::SCOPE_GLOBAL) {
            $entity->setObjectGlobal($risk->getInstance()->getObject());
        }


        /** @var AnrTable $table */
        $table = $this->get('table');

        return $table->save($entity, $last);
    }

    public function initPosition($anrId) {

        //retrieve recommandations
        /** @var RecommandationTable $recommandationTable */
        $recommandationTable = $this->get('recommandationTable');
        $recommandations = $recommandationTable->getEntityByFields(['anr' => $anrId], ['importance' => 'DESC']);

        $position = 0;
        $i = 1;
        foreach ($recommandations as $recommandation) {
            $last = ($i == count($recommandations)) ? true : false;
            $recommandation->position = $position;
            $recommandationTable->save($recommandation, $last);

            $position++;
            $i++;
        }
    }

    /**
     * Validate For
     *
     * @param $recoRiskId
     * @param $data
     */
    public function validateFor($recoRiskId, $data, $vulA = null, $commentA = null, $maxriskA = null) {

        /** @var RecommandationRiskTable $table */
        $table = $this->get('table');
        $recommandationRisk = $table->getEntity($recoRiskId);

        $reco = $recommandationRisk->recommandation;
        $risk = $recommandationRisk->instanceRisk;
        $anr = $recommandationRisk->anr;

        //verify if risk is final or intermediate (risk attach to others recommandations)
        $riskRecommandations = $table->getEntityByFields(['instanceRisk' => $risk->id]);
        $final = (count($riskRecommandations) == 1) ? true : false;

        //repositioning recommendation in hierarchy
        $this->detach($recommandationRisk, $final);

        //automatically record in history before modify recommendation and risk values
        $anrService = $this->get('anrService');
        $anrInstanceService = $this->get('anrInstanceService');

        $histo = [
            'final'                 => $final,
            'implComment'           => $data['comment'],
            'recoCode'			    => $reco->get('code'),
            'recoDescription'	    => $reco->get('description'),
            'recoImportance'	    => $reco->get('importance'),
            'recoComment'		    => $reco->get('comment'),
            'recoDuedate'		    => $reco->get('duedate'),
            'recoResponsable'	    => $reco->get('responsable'),
            'riskInstance'          => $risk->get('instance')->get('name1'),
            'riskInstanceContext'   => $anrInstanceService->getDisplayedAscendance($risk->get('instance')),
            'riskAsset'             => $risk->get('asset')->get('code') . ' - ' . $risk->get('asset')->get('label1'),
            'riskThreat'            => $risk->get('threat')->get('code') . ' - ' . $risk->get('threat')->get('label1'),
            'riskThreatVal'         => $risk->get('threatRate'),
            'riskVul'               => $risk->get('vulnerability')->get('code') . ' - ' . $risk->get('vulnerability')->get('label1'),
            'riskVulValBefore'      => $risk->get('vulnerabilityRate'),
            'riskVulValAfter'       => ($final) ? max(0, $risk->get('vulnerabilityRate') - $risk->get('reductionAmount')) : $risk->get('vulnerabilityRate'),
            'riskKindOfMeasure'     => $risk->get('kindOfMeasure'),
            'riskCommentBefore'     => $risk->get('comment'),
            'riskCommentAfter'      => ($final) ? $risk->get('commentAfter') : $risk->get('comment'),
            'riskMaxRiskBefore'     => $risk->get('cacheMaxRisk'),
            'riskMaxRiskAfter'      => ($final) ? $risk->get('cacheTargetedRisk') : $risk->get('cacheMaxRisk'),
            'riskColorBefore'       => ($risk->get('cacheMaxRisk') != -1) ? $anrService->getColor($anr, $risk->get('cacheMaxRisk')) : '',
            'riskColorAfter'        => ($final) ? ((($risk->get('cacheTargetedRisk') != -1) ? $anrService->getColor($anr, $risk->get('cacheTargetedRisk')) : '')) : (($risk->get('cacheMaxRisk') != -1) ? $anrService->getColor($anr, $risk->get('cacheMaxRisk')) : ''),
            'cacheCommentAfter'     => $recommandationRisk->get('commentAfter'),
        ];

        $class = $this->get('recommandationHistoricEntity');
        $recoHisto = new $class();
        $recoHisto->setLanguage($this->getLanguage());
        $recoHisto->setDbAdapter($this->get('recommandationHistoricTable')->getDb());
        $recoHisto->exchangeArray($histo);

        $recoHisto->anr = $anr;
        $recoHisto->instanceRisk = $risk;

        /** @var RecommandationHistoricTable $recoHistoTable */
        $recoHistoTable = $this->get('recommandationHistoricTable');
        $recoHistoTable->save($recoHisto);

        if ($final) {

            //overload constatation for volatile comment (after measure)
            $cacheCommentAfter = '';
            $riskRecoHistos = $recoHistoTable->getEntityByFields(['instanceRisk' => $recommandationRisk->get('instanceRisk')->get('id')]);
            foreach ($riskRecoHistos as $riskRecoHisto) {
                if (strlen($cacheCommentAfter) && strlen($riskRecoHisto->get('cacheCommentAfter'))) {
                    $cacheCommentAfter .= '<br>' . $riskRecoHisto->get('cacheCommentAfter');
                } else if (strlen($cacheCommentAfter) == 0) {
                    $cacheCommentAfter = $riskRecoHisto->get('cacheCommentAfter');
                }
            }
            $risk->comment = $cacheCommentAfter;
            $risk->commentAfter = '';

            //apply reduction vulnerability on risk
            $newVulnerabilityRate = $risk->get('vulnerabilityRate') - $risk->get('reductionAmount');
            $risk->vulnerabilityRate = ($newVulnerabilityRate >= 0) ? $newVulnerabilityRate : 0;

            //set reduction amount to 0
            $risk->reductionAmount = 0;

            //change status to NOT_TREATED
            $risk->kindOfMeasure = InstanceRisk::KIND_NOT_TREATED;

            /** @var InstanceRiskTable $instanceRiskOpTable */
            $instanceRiskTable = $this->get('instanceRiskTable');
            $instanceRiskTable->save($risk);
        }

        $reco->counterTreated = $reco->get('counterTreated') + 1;

        //if is final, clean comment, duedate and responsable
        if($final){
            $reco->duedate = null;
            $reco->responsable = '';
            $reco->comment = '';
        }

        /** @var RecommandationTable $recommandationTable */
        $recommandationTable = $this->get('recommandationTable');
        $recommandationTable->save($reco);
    }

    public function detach($recommandationRisk, $final = true){

        /** @var RecommandationRiskTable $table */
        $table = $this->get('table');

        //global
        if ($recommandationRisk->objectGlobal) {

            $brothersRecommandationsRisks = $table->getEntityByFields([
                'recommandation' => $recommandationRisk['recommandation']->get('id'),
                'objectGlobal' => $recommandationRisk['objectGlobal']->get('id'),
                'asset' => $recommandationRisk['asset']->get('id'),
                'threat' => $recommandationRisk['threat']->get('id'),
                'vulnerability' => $recommandationRisk['vulnerability']->get('id'),
            ]);

            $i = i;
            foreach($brothersRecommandationsRisks as $brotherRecommandationRisk) {
                $last = ($i == count($brothersRecommandationsRisks)) ? true : false;
                $table->delete($brotherRecommandationRisk['id'], $last);
                $i++;
            }
        } else {
            $table->delete($recommandationRisk->id);
        }

        $this->updatePosition($recommandationRisk->recommandation, $final);
    }

    /**
     * Update Position
     *
     * @param $recommandation
     * @param bool $final
     */
    public function updatePosition($recommandation, $final = true) {

        /** @var RecommandationTable $recommandationTable */
        $recommandationTable = $this->get('recommandationTable');

        if(!$final && $recommandation->get('position') == 0){
            $recommandation->position = 1;
            $recommandationTable->save($recommandation);
        }
        else if($final && $recommandation->get('position') > 0){
            $recommandation->position = 0;
            $recommandationTable->save($recommandation);
        }
    }
}