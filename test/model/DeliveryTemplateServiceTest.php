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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoDeliveryTemplate\test\model;

use oat\tao\test\TaoPhpUnitTestRunner;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \common_ext_ExtensionsManager;
use \taoResultServer_models_classes_ResultServerAuthoringService;
use oat\taoDeliveryTemplate\model\DeliveryTemplateService;
use oat\taoDeliveryTemplate\rdf\DeliveryTemplate;

class DeliveryTemplateServiceTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @var DeliveryTemplateService
     */
    protected $deliveryTemplateService = null;

    /**
     * tests initialization
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        TaoPhpUnitTestRunner::initTest();
        $this->deliveryTemplateService = DeliveryTemplateService::singleton();
    }

    /**
     * verify delivery class
     * 
     * @return void
     */
    public function testService()
    {
        $this->assertIsA($this->deliveryTemplateService, 'oat\taoDeliveryTemplate\model\DeliveryTemplateService');
    }

    /**
     * create delivery instance
     * 
     * @return \core_kernel_classes_Resource
     */
    public function testCreateInstance()
    {
        $delivery = $this->deliveryTemplateService->createInstance(new core_kernel_classes_Class(DeliveryTemplate::CLASS_URI), 'UnitTestDelivery2');
        $this->assertInstanceOf('core_kernel_classes_Resource', $delivery);
        $delivyType = current($delivery->getTypes());
        $this->assertEquals(DeliveryTemplate::CLASS_URI, $delivyType->getUri());
        return $delivery;
    }

    /**
     * Check if the delivery server exists
     * @depends testCreateInstance
     * 
     * @param $delivery
     * @return void
     */
    public function testDeliveryServer($delivery)
    {
        $deliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        $this->assertNotNull($deliveryServer);
        
        return $deliveryServer;
    }

    /**
     * Verify the delivery server is the same as default server
     * @depends testDeliveryServer
     * 
     * @param $deliveryServer
     * @return void
     */
    public function testVerifyDeliveryServer($deliveryServer)
    {
        $defaultDeliveryServer = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        $this->assertEquals($defaultDeliveryServer->getUri(), $deliveryServer->getUri());
    }

    /**
     * Delete delivery instance
     * @depends testCreateInstance
     * 
     * @param $delivery
     */
    public function testDeleteInstance($delivery)
    {
        $this->deliveryTemplateService->deleteInstance($delivery);
        $this->assertFalse($delivery->exists());
    }

    /**
     * Verify delivery instance deletion
     * @depends testCreateInstance
     * 
     * @param $delivery
     */
    public function testVerifyInstanceDeletion($delivery)
    {
        $this->assertNull($delivery->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE)));
    }

    /**
     * Verify that just created delivery class exists
     * 
     * @param $deliveryClass
     * @return \core_kernel_classes_Resource
     */
    public function testCreateDeliveryInstance()
    {
        $deliveryClass = new core_kernel_classes_Class(DeliveryTemplate::CLASS_URI);
        $deliveryInstance = $this->deliveryTemplateService->createInstance($deliveryClass, 'UnitTestDelivery3');
        $this->assertInstanceOf('core_kernel_classes_Resource', $deliveryInstance);
        
        return $deliveryInstance;
    }

    /**
     * Verify that just created delivery instance exists
     * @depends testCreateDeliveryInstance
     * 
     * @param $deliveryInstance
     * @return void
     */
    public function testDeliveryInstanceExists($deliveryInstance)
    {
        $this->assertTrue($deliveryInstance->exists());
    }

    /**
     * Verify tye number of types of deliveryInstance
     * @depends testCreateDeliveryInstance
     * 
     * @param $deliveryInstance
     * @return void
     */
    public function testDeliveryInstanceTypes($deliveryInstance)
    {
        $this->assertEquals(1, count($deliveryInstance->getTypes()));
    }

    /**
     * Verify deliveryInstance is an instance of deliveryClass
     * @depends testCreateDeliveryInstance
     * 
     * @param $deliveryInstance
     * @param $deliveryClass
     * @return void
     */
    public function testVerifyDeliveryInstance($deliveryInstance)
    {
        $this->assertTrue($deliveryInstance->isInstanceOf(new core_kernel_classes_Class(DeliveryTemplate::CLASS_URI)));
    }

    /**
     * Clone deliveryInstance
     * @depends testCreateDeliveryInstance
     * 
     * @param $deliveryInstance
     * @return void
     */
    public function testCloneInstance($deliveryInstance)
    {
        $clone = $this->deliveryTemplateService->cloneInstance($deliveryInstance);
        $this->assertInstanceOf('core_kernel_classes_Resource', $clone);
        $this->assertTrue($clone->exists());
        $this->assertTrue($this->deliveryTemplateService->deleteInstance($clone));
        $this->assertFalse($clone->exists());
        $this->assertTrue($deliveryInstance->exists());
    }

    /**
     * Delete delivery instance
     * @depends testCreateDeliveryInstance
     * 
     * @param $deliveryInstance
     */
    public function testDeleteDeliveryInstance($deliveryInstance)
    {
        $this->deliveryTemplateService->deleteInstance($deliveryInstance);
        $this->assertFalse($deliveryInstance->exists());
    }
    
    /**
     * @expectedException common_exception_NoImplementation
     */
    public function testGetImplementationByContentClass()
    {
        $inexistingClass = new core_kernel_classes_Class('doesnotexist');
        DeliveryTemplateService::singleton()->getImplementationByContentClass($inexistingClass);
    }
    
    /**
     * @expectedException common_exception_NoImplementation
     */
    public function testGetImplementationByContent()
    {
        $inexistingClass = new core_kernel_classes_Class('doesnotexist');
        DeliveryTemplateService::singleton()->getImplementationByContent($inexistingClass);
    }
}