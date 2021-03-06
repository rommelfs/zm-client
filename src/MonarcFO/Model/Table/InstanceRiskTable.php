<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

/**
 * Class InstanceRiskTable
 * @package MonarcFO\Model\Table
 */
class InstanceRiskTable extends AbstractEntityTable
{
    /**
     * InstanceRiskTable constructor.
     * @param \MonarcCore\Model\Db $dbService
     */
    public function __construct(\MonarcCore\Model\Db $dbService)
    {
        parent::__construct($dbService, '\MonarcFO\Model\Entity\InstanceRisk');
    }

    /**
     * @param $anrId
     * @return bool
     */
    public function started($anrId)
    {
        $qb = $this->getRepository()->createQueryBuilder('t');
        $res = $qb->select('COUNT(t.id)')
            ->where('t.anr = :anrid')
            ->setParameter(':anrid', $anrId)
            ->andWhere($qb->expr()->orX(
                $qb->expr()->neq('t.threatRate', -1),
                $qb->expr()->neq('t.vulnerabilityRate', -1)
            ))->getQuery()->getSingleScalarResult();

        return $res > 0;
    }
}