<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\PokemonTable;
use Application\Model\GeoTable;
use Application\Model\UserTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        // Trouble with CORS : vue & zend on 2 different domains
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, WWW-Authenticate, Origin, X-Requested-With, Content-Type, Accept');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Application\Model\PokemonTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PokemonTable($dbAdapter);
                    return $table;
                },
                'Application\Model\GeoTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new GeoTable($dbAdapter);
                    return $table;
                },
                'Application\Model\UserTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}
