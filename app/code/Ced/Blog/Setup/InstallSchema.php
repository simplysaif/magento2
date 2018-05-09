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
 * @package   Ced_Blog
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Blog\Setup;


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

     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('ced_blog_category'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )

            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                256,
                ['nullable' => false],
                'title'
            )

            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'url_key'
            )

            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'post_id'
            )

            ->addColumn(
                'sortOrder',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                256,
                ['nullable' => false],
                'sortOrder'
            )

            ->addColumn(
                'keywords',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'keywords'
            )

            ->addColumn(

                'about',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'about'
            )

            ->addColumn(
                'meta_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'meta_title'
            )

            ->addColumn(
                'profile',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                256,
                ['nullable' => false],
                'profile'
            )

            ->setComment('ced_blog_category');

        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('ced_blog_comments'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )

            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'post_id'
            )

            ->addColumn(
                'post_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                256,
                ['nullable' => false],
                'post_title'
            )

            ->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                256,
                ['nullable' => false],
                'url'
            )

            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'description'
            )

            ->addColumn(
                'attachment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'attachment'
            )

            ->addColumn(
                'user_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'user_type'
            )

            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'user_id'
            )

            ->addColumn(
                'ip_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'ip_address'
            )

            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                32,
                ['nullable' => false],
                'created_at'
            )

            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                32,
                ['nullable' => false],
                'updated_at'
            )

            /* additional data in comment section */

            ->addColumn(
                'user',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'user'
            )

            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'email'
            )

            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'status'
            )

            ->addColumn(
                'approve',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'approve'
            )

            ->setComment('ced_blog_comments');

        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('ced_blog_post'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )

            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'user_id'
            )

            ->addColumn(
                'ip_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'ip_address'
            )

            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'title'
            )

            ->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'url'
            )

            ->addColumn(
                'post_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'post_text'
            )

            ->addColumn(
                'featured_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'featured_image'
            )

            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                32,
                ['nullable' => false],
                'created_at'
            )

            ->addColumn(
                'update_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                32,
                ['nullable' => false],
                'update_at'
            )

            ->addColumn(
                'author',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'author'
            )

            ->addColumn(
                'meta_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'meta_content'
            )

            ->addColumn(
                'meta_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'meta_title'
            )

            ->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'meta_description'
            )

            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'status'
            )

            ->addColumn(
                'short_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'short_description'
            )

            ->addColumn(
                'publish_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                32,
                ['nullable' => true],
                'publish_date'
            )->addColumn(

                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'product_id'
            )

            ->addColumn(
                'blog_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'blog_status'
            )

            ->addColumn(
                'blog_category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'blog_category'
            )

            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'category_id'
            )

            ->addColumn(
                'tags',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'tags'
            )

            ->addColumn(
                'cms_identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => true],
                'cms_identifier'
            )

            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'store_id'
            )

            ->setComment('ced_blog_post');

        $installer->getConnection()->createTable($table);



        /* category table for 'all users' */



        $table = $installer->getConnection()
            ->newTable($installer->getTable('ced_blog_user'))
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'category_id'
            )

            ->addColumn(
                'blog_category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'blog_category'
            )

            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'user_id'
            )

            ->addColumn(

                'profile',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'profile'
            )

            ->addColumn(

                'about',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                65535,
                ['nullable' => false],
                'about'
            )

            ->setComment('ced_blog_users');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('ced_blog_category_post'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )

            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'post_id'
            )

            ->addColumn(

                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'cat_id'
            )

            ->setComment('ced_blog_category_post');

        $installer->getConnection()->createTable($table);

        /* end */
        $installer->endSetup();
    }

}        