<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class AnrCartoRiskServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'entity'=> 'MonarcFO\Model\Entity\Scale',
        'table'=> 'MonarcFO\Model\Table\ScaleTable',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
        'instanceTable' => 'MonarcFO\Model\Table\InstanceTable',
		'instanceRiskTable' => 'MonarcFO\Model\Table\InstanceRiskTable',
		'instanceConsequenceTable' => 'MonarcFO\Model\Table\InstanceConsequenceTable',
		'threatTable' => 'MonarcFO\Model\Table\ThreatTable',
    );
}
