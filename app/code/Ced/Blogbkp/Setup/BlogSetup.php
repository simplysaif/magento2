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

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;

/**
 * @codeCoverageIgnore
 */

class BlogSetup extends EavSetup
{
    /**
     * EAV configuration
     * @var Config
     */

    protected $eavConfig;

    /**
     * Setup model
     *
     * @var ModuleDataSetupInterface
     */

    private $setup;

    /**
     * Init
     * @param ModuleDataSetupInterface $setup
     * @param Context                  $context
     * @param CacheInterface           $cache
     * @param CollectionFactory        $attrGroupCollectionFactory
     * @param Config                   $eavConfig
     */

    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        $this->setup = $setup;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }


    /**
     * Cedcommerce
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function getDefaultEntities()
    {
        $entities = [
            'blog' => [
                'entity_model' => 'Ced\Blog\Model\ResourceModel\BlogPost',
                'table' => 'ced_blog_attribute_post',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'attributes' => [
                    'ip_address' => [
                    'type' => 'static',
                    'label' => 'Ip address',
                    'input' => 'int',
                    'sort_order' => 40,
                    'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                    'position' => 40,
                    'note'=>0,
                ],

                'id' => [
                'type' => 'int',
                'label' => 'Id',
                'input' => 'int',
                'sort_order' => 40,
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                'position' => 40,
                'note'=>0,
                ],

                'title' => [
                'type' => 'static',
                'label' => 'Post Title',
                'input' => 'text',
                'sort_order' => 1,
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                'position' => 1,
                'note'=>1,
                ],

                'url' => [
                'type' => 'varchar',
                'label' => 'Post url',
                'input' => 'text',
                'required' => false,
                'sort_order' => 2,
                'position' => 2,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>1,
                ],

                'post_text' => [
                'type' => 'varchar',
                'label' => 'Post Content',
                'input' => 'text',
                'required' => false,
                'sort_order' => 3,
                'position' => 3,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>1,
                ],

                'featured_image' => [
                'type' => 'varchar',
                'label' => 'Post Featured Image',
                'input' => 'text',
                'required' => false,
                'sort_order' => 4,
                'position' => 4,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>1,
                ],
                'create' => [
                'type' => 'varchar',
                'label' => 'Blog Create',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'update' => [
                'type' => 'varchar',
                'label' => 'Blog Update',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'author' => [
                'type' => 'varchar',
                'label' => 'Blog Author',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'meta_content' => [
                'type' => 'varchar',
                'label' => 'Blog Meta Content ',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'meta_title' => [
                'type' => 'varchar',
                'label' => 'Blog Meta Title ',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'meta_description' => [
                'type' => 'varchar',
                'label' => 'Blog Meta Description',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>0,
                ],

                'status' => [
                'type' => 'varchar',
                'label' => 'Post Status',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'note'=>1,
                ],

                'publish_date' => [
                'type' => 'static',
                'label' => 'Publish Date',
                'input' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\Time\Created',
                'sort_order' => 5,
                'position' => 5,
                'visible' => false,
                'note'=>1,
                ],
            ],
        ],
    ];
        return $entities;
    }

    /**
     * Gets EAV configuration
     *
     * @return Config
     */

    public function getEavConfig()
    {
        return $this->eavConfig;
    }

}

