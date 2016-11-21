<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class AnrAmvServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'entity'=> 'MonarcFO\Model\Entity\Amv',
        'table'=> 'MonarcFO\Model\Table\AmvTable',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
        'assetTable' => 'MonarcFO\Model\Table\AssetTable',
        'threatTable' => 'MonarcFO\Model\Table\ThreatTable',
        'vulnerabilityTable' => 'MonarcFO\Model\Table\VulnerabilityTable',
        'measureTable' => 'MonarcFO\Model\Table\MeasureTable',
    );
}