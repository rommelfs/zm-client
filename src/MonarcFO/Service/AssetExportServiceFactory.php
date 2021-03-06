<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

/**
 * Asset Export Service Factory
 *
 * Class AssetExportServiceFactory
 * @package MonarcFO\Service
 */
class AssetExportServiceFactory extends AbstractServiceFactory
{
    protected $class = "\\MonarcCore\\Service\\AssetExportService";

    protected $ressources = [
        'table' => 'MonarcFO\Model\Table\AssetTable',
        'entity' => 'MonarcFO\Model\Entity\Asset',
        'amvService' => 'MonarcFO\Service\AmvService', // Ça devrait le faire
    ];
}
