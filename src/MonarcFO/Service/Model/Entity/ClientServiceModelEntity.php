<?php
namespace MonarcFO\Service\Model\Entity;

use MonarcCore\Service\Model\Entity\AbstractServiceModelEntity;

class ClientServiceModelEntity extends AbstractServiceModelEntity
{
	protected $ressources = [
    	'setDbAdapter' => '\MonarcCli\Model\Db',
    ];
}