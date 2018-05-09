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
  * @category  Ced
  * @package   Ced_CustomerMembership
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\TeamMember\Setup;
 
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
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup; 
        $installer->startSetup();

         $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_teammember')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'first_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Email'
        )->addColumn(
            'last_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Email'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            'Email'
        )->addColumn(
            'password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['unsigned' => true, 'nullable' => false],
            'Password'
        )->addColumn(
            'confirm_password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['unsigned' => true, 'nullable' => false],
            'Confirm Password'
        )
        ->setComment(
        		'Team Member'
        );
        $installer->getConnection()->createTable($table);
        
        
        $table = $installer->getConnection()->newTable(
        		$installer->getTable('ced_teammember_mesaages')
        )->addColumn(
        		'id',
        		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        		null,
        		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        		'Id'
        )->addColumn(
        		'sender',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'Sender'
        )->addColumn(
        		'sender_email',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'Sender Email'
        )->addColumn(
        		'receiver',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'receiver'
        )->addColumn(
        		'receiver_email',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['unsigned' => true, 'nullable' => false],
        		'Receiver Email'
        )->addColumn(
        		'message',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'2000',
        		['unsigned' => true, 'nullable' => false],
        		'Message'
        )->addColumn(
                'time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2000',
                ['unsigned' => true, 'nullable' => false],
                'time'
        )
        ->setComment(
        		'Team Member'
        );
        $installer->getConnection()->createTable($table);
        
        
        
        
        $table = $installer->getConnection()->newTable(
        		$installer->getTable('ced_teammember_profile')
        )->addColumn(
        		'id',
        		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        		null,
        		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        		'Id'
        )->addColumn(
        		'teammember_id',
        		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        		11,
        		['nullable' => false],
        		'Teammember Id'
        )->addColumn(
        		'name',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'Name'
        )->addColumn(
        		'email',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'Email'
        )->addColumn(
        		'contact_no',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['nullable' => false],
        		'Contact No'
        )->addColumn(
        		'address',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'2000',
        		['unsigned' => true, 'nullable' => false],
        		'Address'
        )->addColumn(
        		'city',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['unsigned' => true, 'nullable' => false],
        		'City'
        )->addColumn(
        		'logo',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'255',
        		['unsigned' => true, 'nullable' => false],
        		'Logo'
        )
        ->setComment(
        		'Team Member'
        );
        $installer->getConnection()->createTable($table);
        
        
        
        
        
        
        
        $installer->endSetup(); 
    }
}
