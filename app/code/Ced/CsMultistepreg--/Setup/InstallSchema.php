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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMultistepreg\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	/**
	 * {@inheritdoc}
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
		
		$installer = $setup;
		$multistepTableName = $installer->getTable('ced_csmultistepreg_multisteps');
		$connection = $setup->getConnection();
		
		$table = $setup->getConnection()->newTable($multistepTableName)
			->addColumn(
	            'id',
	            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	            null,
	            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
	            'ID'
        	)
        	->addColumn(
        			'step_number',
        			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        			null,
        			[],
        			'Step Number')
        	->addColumn(
	            'step_label',
	            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	            255,
	            [],
	            'Step Label'
        	);
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
	}
	
}