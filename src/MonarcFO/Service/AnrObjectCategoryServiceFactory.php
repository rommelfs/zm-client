<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use \MonarcCore\Service\AbstractServiceFactory;

/**
 * Anr Object Category Service Factory
 *
 * Class AnrObjectCategoryServiceFactory
 * @package MonarcFO\Service
 */
class AnrObjectCategoryServiceFactory extends AbstractServiceFactory
{
    protected $class = "\\MonarcCore\\Service\\ObjectCategoryService";

    protected $ressources = [
        'table' => '\MonarcFO\Model\Table\ObjectCategoryTable',
        'entity' => '\MonarcFO\Model\Entity\ObjectCategory',
        'userAnrTable' => 'MonarcFO\Model\Table\UserAnrTable',
        'anrObjectCategoryTable' => '\MonarcFO\Model\Table\AnrObjectCategoryTable',
        'objectTable' => '\MonarcFO\Model\Table\ObjectTable',
        'rootTable' => 'MonarcFO\Model\Table\ObjectCategoryTable',
        'parentTable' => 'MonarcFO\Model\Table\ObjectCategoryTable',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
    ];
}
