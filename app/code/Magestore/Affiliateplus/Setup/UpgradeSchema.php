<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Affiliateplus\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*your code here*/
        if (version_compare($context->getVersion(), '1.1.1', '<')) {

            //order items
            $setup->getConnection()->addColumn(
                $setup->getTable('magestore_affiliateplus_account'),
                'key_shop',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'default' => '', 'comment' => 'key shop']

            );

            $table = $installer->getConnection()->newTable(
                $installer->getTable('magestore_affiliateplus_account_product')
            )
                ->addColumn(
                    'accountproduct_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Account Product Id'
                )->addColumn(
                    'account_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true,'nullable' => false],
                    'Account Id'
                )->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true,'nullable' => false],
                    'Product Id'
                )->setComment(
                    'Account  Product'
                );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
