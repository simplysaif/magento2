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
 * @package     Ced_Vbadges
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {
	/**
	 *
	 * {@inheritdoc} @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup ();

		/**
         * Create table 'ced_badges'
         */
		$table = $installer->getConnection ()->newTable ( 
            $installer->getTable ( 'ced_badges' ) 
        )->addColumn ( 
            'badges_id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ], 
            'badges_id'
        )->addColumn ( 
            'badge_name', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            255, 
            ['unsigned' => true, 'nullable' => false ], 
            'badge_name'
        )->addColumn ( 
            'badge_description', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            300, 
            [ 'nullable' => false], 
            'badge_description' 
        )->addColumn ( 
            'badge_image', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            500, 
            [ 'nullable' => false], 
            'badge_image' 
        )->addColumn ( 
            'badge_status', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, [ 'nullable' => false ], 
            'badge_status' 
        )->addColumn ( 
            'created_at', 
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, 
            null, 
            [ 'nullable' => false ], 
            'created_at'
        )->addColumn ( 
            'sales', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11,
            ['nullable' => false ], 
            'sales' 
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ced_vendor_badges'
         */
        $table = $installer->getConnection ()->newTable ( 
            $installer->getTable ( 'ced_vendor_badges' ) 
        )->addColumn ( 
            'id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 
            'id'
        )->addColumn ( 
            'vendor_id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            ['unsigned' => true, 'nullable' => false], 
            'vendor_id' 
        )->addColumn ( 
            'badge_id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            200, 
            ['nullable' => false], 
            'badge_id' 
        )->addColumn ( 
            'review_badge_id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            200, 
            ['nullable' => false], 
            'review_badge_id' 
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ced_review_badges'
        */
        $table = $installer->getConnection ()->newTable ( 
            $installer->getTable ( 'ced_review_badges' ) 
        )->addColumn ( 
            'badges_id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11, 
            ['identity' => true, 'unsigned' => true, 'nullable' => false,'primary' => true], 
            'badges_id'
        )->addColumn ( 
            'badge_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            255, 
            ['unsigned' => true, 'nullable' => false], 
            'badge_name'
        )->addColumn ( 
            'badge_image', 
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 500, 
            ['nullable' => false], 
            'badge_image'
        )->addColumn ( 
            'badge_status', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11,
            ['nullable' => false], 
            'badge_status' 
        )->addColumn ( 
            'created_at', 
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, 
            null, 
            ['nullable' => false], 
            'created_at' 
        )->addColumn ( 
            'points', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            ['nullable' => false], 
            'points' 
        );
        $installer->getConnection()->createTable($table);
        
        /**
         * Create table 'ced_review_point'
        */
        $table = $installer->getConnection ()->newTable ( 
            $installer->getTable ( 'ced_review_point' ) 
        )->addColumn ( 
            'id', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 
            'id' 
        )->addColumn ( 
            'rating', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            ['unsigned' => true, 'nullable' => false], 
            'rating' 
        )->addColumn ( 
            'points', 
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 
            11, 
            ['nullable' => false], 
            'points' 
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}