<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Model\Table;

/**
 * Class InstanceRiskOpTable
 * @package MonarcFO\Model\Table
 */
class InstanceRiskOpTable extends \MonarcCore\Model\Table\InstanceRiskOpTable
{
    /**
     * InstanceRiskOpTable constructor.
     * @param \MonarcCore\Model\Db $dbService
     */
    public function __construct(\MonarcCore\Model\Db $dbService)
    {
        parent::__construct($dbService, '\MonarcFO\Model\Entity\InstanceRiskOp');
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
                $qb->expr()->neq('t.brutProb', -1),
                $qb->expr()->neq('t.brutR', -1),
                $qb->expr()->neq('t.brutO', -1),
                $qb->expr()->neq('t.brutL', -1),
                $qb->expr()->neq('t.brutF', -1),
                $qb->expr()->neq('t.netProb', -1),
                $qb->expr()->neq('t.netR', -1),
                $qb->expr()->neq('t.netO', -1),
                $qb->expr()->neq('t.netL', -1),
                $qb->expr()->neq('t.netF', -1),
                $qb->expr()->neq('t.targetedProb', -1),
                $qb->expr()->neq('t.targetedR', -1),
                $qb->expr()->neq('t.targetedO', -1),
                $qb->expr()->neq('t.targetedL', -1),
                $qb->expr()->neq('t.targetedF', -1)
            ))->getQuery()->getSingleScalarResult();
        return $res > 0;
    }
}