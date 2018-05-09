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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\RequestToQuote\Setup;
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
        $setup->startSetup();
        //if (version_compare($context->getVersion(), '[SOME_VERSION]', '=')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ced_requestquote'),
                'quote_increment_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, // Or any other type
                    'nullable' => true,
                    'comment' => 'Quote Increment Id',
                    'after' => 'quote_id'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ced_requestquote_detail'),
                'remaining_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, // Or any other type
                    'nullable' => true,
                    'comment' => 'Quote remaining qty',
                    'after' => 'quote_updated_qty'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ced_requestquote'),
                'remaining_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, // Or any other type
                    'nullable' => true,
                    'comment' => 'Quote remaining qty',
                    'after' => 'quote_updated_qty'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ced_requestquote'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Quote Store Id',
                    'after' => 'telephone'
                ]
            );
        //}
        $setup->endSetup();
    }
}