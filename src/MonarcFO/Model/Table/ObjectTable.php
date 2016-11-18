<?php
namespace MonarcFO\Model\Table;

use MonarcCore\Model\Table\AbstractEntityTable;

class ObjectTable extends AbstractEntityTable {
    public function __construct(\MonarcCore\Model\Db $dbService) {
        parent::__construct($dbService, '\MonarcFO\Model\Entity\Object');
    }
}