<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class AnrServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(

        //core
        'entity'    => 'MonarcCore\Model\Entity\Anr',
        'table'     => 'MonarcCore\Model\Table\AnrTable',
        'anrTable'     => 'MonarcCore\Model\Table\AnrTable',
        'amvTable'  => 'MonarcCore\Model\Table\AmvTable',
        'anrObjectCategoryTable'   => 'MonarcCore\Model\Table\AnrObjectCategoryTable',
        'assetTable' => 'MonarcCore\Model\Table\AssetTable',
        'instanceTable' => 'MonarcCore\Model\Table\InstanceTable',
        'instanceConsequenceTable' => 'MonarcCore\Model\Table\InstanceConsequenceTable',
        'instanceRiskTable' => 'MonarcCore\Model\Table\InstanceRiskTable',
        'instanceRiskOpTable' => 'MonarcCore\Model\Table\InstanceRiskOpTable',
        'measureTable' => 'MonarcCore\Model\Table\MeasureTable',
        'modelTable' => 'MonarcCore\Model\Table\ModelTable',
        'objectTable'   => 'MonarcCore\Model\Table\ObjectTable',
        'objectCategoryTable'   => 'MonarcCore\Model\Table\ObjectCategoryTable',
        'objectObjectTable'   => 'MonarcCore\Model\Table\ObjectObjectTable',
        'rolfCategoryTable' => 'MonarcCore\Model\Table\RolfCategoryTable',
        'rolfRiskTable' => 'MonarcCore\Model\Table\RolfRiskTable',
        'rolfTagTable' => 'MonarcCore\Model\Table\RolfTagTable',
        'scaleTable' => 'MonarcCore\Model\Table\ScaleTable',
        'scaleCommentTable' => 'MonarcCore\Model\Table\ScaleCommentTable',
        'scaleImpactTypeTable' => 'MonarcCore\Model\Table\ScaleImpactTypeTable',
        'threatTable' => 'MonarcCore\Model\Table\ThreatTable',
        'themeTable' => 'MonarcCore\Model\Table\ThemeTable',
        'vulnerabilityTable' => 'MonarcCore\Model\Table\VulnerabilityTable',

        //fo
        'cliEntity'=> 'MonarcFO\Model\Entity\Anr',
        'cliTable'=> 'MonarcFO\Model\Table\AnrTable',
        'anrCliTable'=> 'MonarcFO\Model\Table\AnrTable',
        'amvCliTable' => 'MonarcFO\Model\Table\AmvTable',
        'anrObjectCategoryCliTable' => 'MonarcFO\Model\Table\AnrObjectCategoryTable',
        'assetCliTable' => 'MonarcFO\Model\Table\AssetTable',
        'instanceCliTable' => 'MonarcFO\Model\Table\InstanceTable',
        'instanceConsequenceCliTable' => 'MonarcFO\Model\Table\InstanceConsequenceTable',
        'instanceRiskCliTable' => 'MonarcFO\Model\Table\InstanceRiskTable',
        'instanceRiskOpCliTable' => 'MonarcFO\Model\Table\InstanceRiskOpTable',
        'measureCliTable' => 'MonarcFO\Model\Table\MeasureTable',
        'objectCliTable' => 'MonarcFO\Model\Table\ObjectTable',
        'objectCategoryCliTable' => 'MonarcFO\Model\Table\ObjectCategoryTable',
        'objectObjectCliTable' => 'MonarcFO\Model\Table\ObjectObjectTable',
        'rolfCategoryCliTable' => 'MonarcFO\Model\Table\RolfCategoryTable',
        'rolfRiskCliTable' => 'MonarcFO\Model\Table\RolfRiskTable',
        'rolfTagCliTable' => 'MonarcFO\Model\Table\RolfTagTable',
        'scaleCliTable' => 'MonarcFO\Model\Table\ScaleTable',
        'scaleCommentCliTable' => 'MonarcFO\Model\Table\ScaleCommentTable',
        'scaleImpactTypeCliTable' => 'MonarcFO\Model\Table\ScaleImpactTypeTable',
        'threatCliTable' => 'MonarcFO\Model\Table\ThreatTable',
        'themeCliTable' => 'MonarcFO\Model\Table\ThemeTable',
        'vulnerabilityCliTable' => 'MonarcFO\Model\Table\VulnerabilityTable',
    );
}