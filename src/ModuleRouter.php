<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ServerRequestInterface;

class ModuleRouter
{

    /**
     * @var ModuleManagerInterface
     */
    protected $moduleManager;

    public function __construct(ModuleManagerInterface $moduleManager){
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Module|null
     */
    public function dispatchRequest(ServerRequestInterface $request){
        $modules = $this->moduleManager->getModules();
        foreach($modules as $module){
            if($module->getAdapter()->requestIsValid($request)){
                return $module;
            }
        }
        return null;
    }

}
