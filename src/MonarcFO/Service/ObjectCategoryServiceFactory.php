<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Object Category Service Factory
 *
 * Class ObjectCategoryServiceFactory
 * @package MonarcFO\Service
 */
class ObjectCategoryServiceFactory extends AbstractServiceFactory
{
    protected $class = "\\MonarcCore\\Service\\ObjectCategoryService";

    protected $ressources = [
        'table' => '\MonarcFO\Model\Table\ObjectCategoryTable',
        'entity' => '\MonarcFO\Model\Entity\ObjectCategory',
        'anrObjectCategoryTable' => '\MonarcFO\Model\Table\AnrObjectCategoryTable',
        'objectTable' => '\MonarcFO\Model\Table\ObjectTable',
        'rootTable' => 'MonarcFO\Model\Table\ObjectCategoryTable',
        'parentTable' => 'MonarcFO\Model\Table\ObjectCategoryTable',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
    ];
}