<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Service;

/**
 * Anr Rolf Category Service
 *
 * Class AnrRolfCategoryService
 * @package MonarcFO\Service
 */
class AnrRolfCategoryService extends \MonarcCore\Service\AbstractService
{
    protected $anrTable;
    protected $userAnrTable;
    protected $filterColumns = ['code', 'label1', 'label2', 'label3', 'label4'];
    protected $dependencies = ['anr'];
}
