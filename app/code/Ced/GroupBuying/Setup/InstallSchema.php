<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\GroupBuying\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;


/**
 * Upgrade the Catalog module DB scheme
 */
class InstallSchema implements InstallSchemaInterface {
    /**
     *
     * {@inheritdoc}
     *
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $installer = $setup;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $installer->startSetup();

        /**
         * Create table 'catalog_product_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('group_main_table'))
            ->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'group_id'
        )->addColumn(
            'owner_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'owner_customer_id'
        )->addColumn(
            'show_contribution_to_guest',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'show_contribution_to_guest'
        )->addColumn(
            'original_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'original_product_id'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'price'
        )->addColumn(
            'receiver_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'receiver_name'
        )->addColumn(
            'gift_receiver_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'gift_receiver_email'
        )->addColumn(
            'gift_msg',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'gift_msg'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            255,
            [],
            'created_at'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'status'
        );

        $table2 = $installer->getConnection()
            ->newTable($installer->getTable('guest_information'))
            ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'groupgift_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'groupgift_id'
        )->addColumn(
            'guest_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'guest_name'
        )->addColumn(
            'guest_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'guest_email'
        )->addColumn(
            'request_approval',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'request_approval'
        )->addColumn(
            'quantity',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'quantity'
        )->addColumn(
            'pay_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'pay_status'
        );
        
        $installer->getConnection()->createTable($table);
        $installer->getConnection()->createTable($table2);   
        $installer->endSetup();
        $eavsetup=$objectManager->create('\Magento\Eav\Setup\EavSetup');
             
             if(!$objectManager->create('\Magento\Catalog\Model\ResourceModel\Eav\Attribute')->loadByCode('catalog_product','group_buy')->getId()) {
                $eavsetup->addAttribute('catalog_product', 'group_buy', array(
                        'input'         => 'select',
                        'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'type'          => 'int',
                        'label'         => 'Group Buy Enable',
                        'backend'       => '',
                        'default'       => 0,
                        'visible'       => 1,
                        'required'      => true,
                        'group'         => 'Group-Buying',
                        'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                ));
            
            }
             
        $setup->endSetup();
            
    }
}