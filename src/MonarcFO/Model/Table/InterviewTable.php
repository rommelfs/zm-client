<?php
namespace MonarcFO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

class InterviewTable extends AbstractEntityTable {
    public function __construct(\MonarcCore\Model\Db $dbService) {
        parent::__construct($dbService, '\MonarcFO\Model\Entity\Interview');
    }
}