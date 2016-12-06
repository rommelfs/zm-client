<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class AnrRecommandationRiskServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'entity'=> 'MonarcFO\Model\Entity\RecommandationRisk',
        'table'=> 'MonarcFO\Model\Table\RecommandationRiskTable',
        'anrTable'=> 'MonarcFO\Model\Table\AnrTable',
        'recommandationTable'=> 'MonarcFO\Model\Table\RecommandationTable',
        'instanceRiskTable'=> 'MonarcFO\Model\Table\InstanceRiskTable',
        'instanceRiskOpTable'=> 'MonarcFO\Model\Table\InstanceRiskOpTable',
    );
}