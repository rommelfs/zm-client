<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

/**
 * Class RecommandationHistoricTable
 * @package MonarcFO\Model\Table
 */
class RecommandationHistoricTable extends AbstractEntityTable
{
    /**
     * RecommandationHistoricTable constructor.
     * @param \MonarcCore\Model\Db $dbService
     */
    public function __construct(\MonarcCore\Model\Db $dbService)
    {
        parent::__construct($dbService, '\MonarcFO\Model\Entity\RecommandationHistoric');
    }
}