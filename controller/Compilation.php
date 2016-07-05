<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoDeliveryTemplate\controller;

use tao_actions_SaSModule;
use oat\taoDeliveryTemplate\model\TemplateAssemblyService;

/**
 * Controller to compile Delivery Templates
 */
class Compilation extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return Delivery
     */
    public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        $this->service = TemplateAssemblyService::singleton();
        $this->defaultData();
    }

    /**
     * (non-PHPdoc)
     * @see tao_actions_SaSModule::getClassService()
     */
    protected function getClassService()
    {
        return $this->service;
    }
    
    /*
     * controller actions
    */
    /**
     * Render json data to populate the delivery tree
     * 'modelType' must be in the request parameters
     *
     * @return void
     */
    public function index()
    {
		$delivery = $this->getCurrentInstance();
		$this->setData('uri', $delivery->getUri());
		$this->setData('classUri', $this->getCurrentClass()->getUri());
		$this->setData("deliveryLabel", $delivery->getLabel());
		
		//compilation state:
		$compiled = $this->service->getAssembliesByTemplate($delivery, true);
		$this->setData("isCompiled", !empty($compiled));
		
		$this->setView("Compilation/index.tpl");
    }
    
	public function compile()
	{
	    $delivery = $this->getCurrentInstance();
	    $report = $this->service->createAssemblyFromTemplate($delivery);
	    
	    $this->returnReport($report);
	}
}