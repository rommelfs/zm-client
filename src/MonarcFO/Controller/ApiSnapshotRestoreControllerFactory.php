<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

/**
 * Api Snapshots Restore Controller Factory
 *
 * Class ApiSnapshotRestoreControllerFactory
 * @package MonarcFO\Controller
 */
class ApiSnapshotRestoreControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = '\MonarcFO\Service\SnapshotService';
}