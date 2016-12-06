<?php
namespace MonarcFO\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class AnrRiskServiceFactory extends \MonarcCore\Service\AbstractServiceFactory
{
    protected $ressources = array(
        'table' => 'MonarcFO\Model\Table\InstanceRiskTable',
        'entity' => 'MonarcFO\Model\Entity\InstanceRisk',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
        'instanceTable' => 'MonarcFO\Model\Table\InstanceTable',
        'instanceRiskTable' => 'MonarcFO\Model\Table\InstanceRiskTable',
    );
}