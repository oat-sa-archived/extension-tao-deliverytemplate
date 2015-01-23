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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoDeliveryTemplate\model;

use tao_models_classes_ClassService;
use core_kernel_classes_Class;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use taoResultServer_models_classes_ResultServerAuthoringService;
use common_report_Report;
use taoDelivery_models_classes_DeliveryAssemblyService;

/**
 * Service to manage the authoring of delivery templates
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class TemplateAssemblyService extends \taoDelivery_models_classes_DeliveryAssemblyService
{
    /**
     * Returns the assemblies derived from the delivery template
     *
     * @param core_kernel_classes_Resource $deliveryTemplate
     * @param boolean $activeOnly
     * @return array
     */
    public function getAssembliesByTemplate(core_kernel_classes_Resource $deliveryTemplate, $activeOnly = false) {
        $searchArray = $activeOnly
        ? array(
            PROPERTY_COMPILEDDELIVERY_DELIVERY => $deliveryTemplate
        )
        : array(
            PROPERTY_COMPILEDDELIVERY_DELIVERY => $deliveryTemplate,
        );
        return $this->getRootClass()->searchInstances($searchArray, array('like' => 'false', 'recursive' => true));
    }
    
    /**
     * Creates a new assembly from the provided template
     * and desactivates other assemblies crearted from the same template
     *
     * @param core_kernel_classes_Resource $deliveryTemplate
     * @throws EmptyDeliveryException
     * @return common_report_Report
     */
    public function createAssemblyFromTemplate(core_kernel_classes_Resource $deliveryTemplate) {
    
        $assemblyClass = $this->getRootClass();
    
        $content = DeliveryTemplateService::singleton()->getContent($deliveryTemplate);
        if (is_null($content)) {
            throw new EmptyDeliveryException('Delivery '.$deliveryTemplate->getUri().' has no content');
        }
    
        $props = $deliveryTemplate->getPropertiesValues(array(
            RDFS_LABEL,
            TAO_DELIVERY_RESULTSERVER_PROP,
            TAO_DELIVERY_MAXEXEC_PROP,
            TAO_DELIVERY_START_PROP,
            TAO_DELIVERY_END_PROP,
            TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP
        ));
        $props[PROPERTY_COMPILEDDELIVERY_DELIVERY] = array($deliveryTemplate);
    
        return $this->createAssemblyByContent($assemblyClass, $content, $props);

    }
    
    /**
     *
     * @param core_kernel_classes_Class $deliveryClass
     * @param core_kernel_classes_Resource $content
     * @param unknown $properties
     * @return common_report_Report
     */
    public function createAssemblyByContent(core_kernel_classes_Class $deliveryClass, core_kernel_classes_Resource $content, $properties = array()) {
    
        // report will be replaced unless an exception occures
        $report = new common_report_Report(common_report_Report::TYPE_ERROR, __('Delivery could not be published'));
        try {
            $compiler = $this->getCompiler($content);
            $report = $compiler->compile();
            if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
                $serviceCall = $report->getData();
    
                $properties[PROPERTY_COMPILEDDELIVERY_DIRECTORY] = $compiler->getSpawnedDirectoryIds();
    
                $assemblyService = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
                $compilationInstance = $assemblyService->createAssemblyFromServiceCall($deliveryClass, $serviceCall, $properties);
                $report->setData($compilationInstance);
            }
        } catch (Exception $e) {
            if ($e instanceof common_exception_UserReadableException) {
                $report->add($e);
            } else {
                common_Logger::w($e->getMessage());
            }
        }
        return $report;
    
    }
    
    protected function getCompiler(core_kernel_classes_Resource $content){
        return DeliveryCompiler::createCompiler($content);
    }
    
}