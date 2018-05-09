<?php 

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */ 
namespace Ced\Barcode\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**

 * @codeCoverageIgnore

 */
class InstallData implements InstallDataInterface {

   
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
    	
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        /** 
          @var
          EavSetup
          $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /**
         *
          Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'ean', [
            'group'=> 'General',
            'type'=>'varchar',
            'backend'=>'',
            'frontend'=>'',
            'label'=>'Ean',
            'input'=>'text',
            'class'=>'',
            'source'=>'',
            'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible'=>true,
            'required'=>false,
            'user_defined'=>true,
            'default'=>'',
            'searchable'=>false,
            'filterable'=>false,
            'comparable'=>false,
            'visible_on_front'=>false,
            'used_in_product_listing'=>true,
        	'is_used_in_grid' =>true,
            'unique'=>true,
            'apply_to'=>'simple,configurable,virtual,bundle,downloadable'
           ]);
        
      $eavSetup->addAttributeToSet ( 'catalog_product', 'Default', 'General', 'ean');
      
      $value ='a:4:{s:18:"_1473231277493_493";a:3:{s:5:"field";s:4:"name";s:5:"print";s:1:"1";s:8:"position";s:6:"90,825";}s:18:"_1473231351543_543";a:3:{s:5:"field";s:3:"sku";s:5:"print";s:1:"1";s:8:"position";s:6:"90,812";}s:18:"_1473231355492_492";a:3:{s:5:"field";s:5:"price";s:5:"print";s:1:"1";s:8:"position";s:6:"90,799";}s:18:"_1473231609786_786";a:3:{s:5:"field";s:5:"image";s:5:"print";s:1:"1";s:8:"position";s:13:"10,750,70,792";}}';
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $configModel = $objectManager->create('Magento\Config\Model\ResourceModel\Config');
      $configModel->saveConfig('barcode/active11/desopt',$value,'default',0);
        
        
    }

}