<?php
namespace MonarcFO\Service;

use MonarcCore\Service\AbstractServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnrLibraryServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'table'             => 'MonarcFO\Model\Table\ObjectTable',
        'entity'            => 'MonarcFO\Model\Entity\Object',
        'objectObjectTable' => 'MonarcFO\Model\Table\ObjectObjectTable',
        'objectService'     => 'MonarcFO\Service\ObjectService'
    );

    public function createService(ServiceLocatorInterface $serviceLocator){

        $class = "\\MonarcCore\\Service\\AnrObjectService";

        if(class_exists($class)){
            $ressources = $this->getRessources();
            if (empty($ressources)) {
                $instance = new $class();
            } elseif (is_array($ressources)) {
                $sls = array();
                foreach ($ressources as $key => $value) {
                    $sls[$key] = $serviceLocator->get($value);
                }
                $instance = new $class($sls);
            } else {
                $instance = new $class($serviceLocator->get($ressources));
            }

            $instance->setLanguage($this->getDefaultLanguage($serviceLocator));
            $conf = $serviceLocator->get('Config');
            $instance->setMonarcConf(isset($conf['monarc'])?$conf['monarc']:array());

            return $instance;
        }else{
            return false;
        }
    }
}