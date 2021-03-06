<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

/**
 * Anr Interview Service Factory
 *
 * Class AnrInterviewServiceFactory
 * @package MonarcFO\Service
 */
class AnrInterviewServiceFactory extends AbstractServiceFactory
{
    protected $ressources = [
        'table' => '\MonarcFO\Model\Table\InterviewTable',
        'entity' => '\MonarcFO\Model\Entity\Interview',
        'anrTable' => 'MonarcFO\Model\Table\AnrTable',
        'userAnrTable' => 'MonarcFO\Model\Table\UserAnrTable',
    ];
}