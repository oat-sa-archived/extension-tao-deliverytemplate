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

return array(
    'name' => 'taoDeliveryTemplate',
	'label' => 'Delivery Templates',
	'description' => 'Allows the generation of delivery templates to recreate the same delivery several times. This extension is designed for backward compatibility with Tao 2.6 and does not offer significant new features.',
    'license' => 'GPL-2.0',
    'version' => '2.0',
	'author' => 'Open Assessment Technologies SA',
	'requires' => array(
	    'taoDeliveryRdf' => '>=1.1'
	),
	'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoDeliveryTemplateManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoDeliveryTemplateManager', array('ext'=>'taoDeliveryTemplate')),
    ),
    'install' => array(
        'rdf' => array(
            dirname(__FILE__). '/install/install/deliveryTemplate.rdf'
        )
    ),
    'uninstall' => array(
    ),
    'autoload' => array (
        'psr-4' => array(
            'oat\\taoDeliveryTemplate\\' => dirname(__FILE__).DIRECTORY_SEPARATOR
        )
    ),
    'routes' => array(
        '/taoDeliveryTemplate' => 'oat\\taoDeliveryTemplate\\controller'
    ),    
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
	    
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'taoDeliveryTemplate/',
	    
	    #BASE WWW required by JS
	    'BASE_WWW' => ROOT_URL.'taoDeliveryTemplate/views/'
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);