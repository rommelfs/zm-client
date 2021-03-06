<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use MonarcFO\Model\Entity\Anr;
use MonarcFO\Model\Entity\Object;
use MonarcFO\Model\Table\AnrTable;
use MonarcFO\Model\Table\SnapshotTable;
use MonarcFO\Service\AbstractService;

/**
 * Snapshot Service
 *
 * Class SnapshotService
 * @package MonarcFO\Service
 */
class SnapshotService extends \MonarcCore\Service\AbstractService
{
    protected $dependencies = ['anr', 'anrReference'];
    protected $filterColumns = [];
    protected $anrTable;
    protected $userAnrTable;
    protected $anrService;

    /**
     * Get List
     *
     * @param int $page
     * @param int $limit
     * @param null $order
     * @param null $filter
     * @return mixed
     */
    public function getList($page = 1, $limit = 25, $order = null, $filter = null, $filterAnd = null)
    {
        /** @var SnapshotTable $table */
        $table = $this->get('table');
        return $table->fetchAllFiltered(
            array_keys($this->get('entity')->getJsonArray()),
            $page,
            $limit,
            $this->parseFrontendOrder($order),
            $this->parseFrontendFilter($filter, $this->filterColumns),
            $filterAnd
        );
    }

    /**
     * Create
     *
     * @param $data
     * @return mixed
     */
    public function create($data, $last = true)
    {
        //duplicate anr and create snapshot record with new id
        /** @var AnrService $anrService */
        $anrService = $this->get('anrService');
        $anrId = $anrService->duplicateAnr($data['anr'], \MonarcFO\Model\Entity\Object::SOURCE_CLIENT, null, [], true);

        $data['anr'] = $anrId;

        return parent::create($data);
    }

    /**
     * Patch
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function patch($id, $data)
    {
        foreach ($data as $key => $value) {
            if ($key != 'comment') {
                unset($data[$key]);
            }
        }

        return parent::patch($id, $data);
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        return $this->patch($id, $data);
    }

    /**
     * Delete
     *
     * @param $id
     */
    public function delete($id)
    {
        /** @var SnapshotTable $snapshotTable */
        $snapshotTable = $this->get('table');
        $snapshot = $snapshotTable->getEntity($id);

        /** @var AnrService $anrService */
        $anrService = $this->get('anrService');

        return $anrService->delete($snapshot->anr->id);
    }

    /**
     * Delete From Anr
     *
     * @param $id
     * @param null $anrId
     * @return mixed
     * @throws \Exception
     */
    public function deleteFromAnr($id, $anrId = null)
    {
        //verify user authorization to do this action
        if (!is_null($anrId)) {
            $entity = $this->get('table')->getEntity($id);
            if ($entity->anrReference->id != $anrId) {
                throw new \Exception('Anr id error', 412);
            }

            $connectedUser = $this->get('table')->getConnectedUser();

            /** @var UserAnrTable $userAnrTable */
            $userAnrTable = $this->get('userAnrTable');
            $rights = $userAnrTable->getEntityByFields(['user' => $connectedUser['id'], 'anr' => $anrId]);
            $rwd = 0;
            foreach ($rights as $right) {
                if ($right->rwd == 1) {
                    $rwd = 1;
                }
            }

            if (!$rwd) {
                throw new \Exception('You are not authorized to do this action', 412);
            }
        }

        return $this->delete($id);
    }

    /**
     * Restore
     *
     * @param $anrId
     * @return mixed
     */
    public function restore($anrId, $id)
    {
        //switch anr and anrReference
        /** @var SnapshotTable $snapshotTable */
        $snapshotTable = $this->get('table');
        /** @var AnrService $anrService */
        $anrService = $this->get('anrService');
        /** @var AnrTable $anrTable */
        $anrTable = $this->get('anrTable');

        $anrSnapshot = current($snapshotTable->getEntityByFields(['anrReference' => $anrId, 'id' => $id]));

        $newAnrId = $anrService->duplicateAnr($anrSnapshot->get('anr')->get('id'), Object::SOURCE_CLIENT, null, [], false, true); // on duplique l'anr liée au snapshot

        $anrSnapshots = $snapshotTable->getEntityByFields(['anrReference' => $anrId]);
        $i = 1;
        foreach ($anrSnapshots as $s) {
            //define new reference for all snapshots
            $s->set('anrReference', $newAnrId);
            $this->setDependencies($s, $this->dependencies);
            $snapshotTable->save($s, count($anrSnapshots) >= $i);
            $i++;
        }

        //resume access
        $userAnrCliTable = $anrService->get('userAnrCliTable');
        $userAnr = $userAnrCliTable->getEntityByFields(['anr' => $anrId]);
        $i = 1;
        foreach ($userAnr as $u) {
            $u->set('anr', $newAnrId);
            $this->setDependencies($u, ['anr', 'user']);
            $userAnrCliTable->save($u, count($userAnr) >= $i);
            $i++;
        }

        //delete old anr
        $anrTable->delete($anrId);

        /** @var Anr $newAnrSnapshot */
        $newAnrSnapshot = $anrTable->getEntity($newAnrId);

        //remove the snap suffix
        for ($i = 1; $i <= 4; ++$i) {
            $newAnrSnapshot->set('label' . $i, substr($newAnrSnapshot->get('label' . $i), 7));
        }
        $anrTable->save($newAnrSnapshot);

        return $newAnrId;
    }
}