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

namespace Ced\Blog\Model;

/**
 * Overide sitemap
 */

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{

    /**
     * Initialize sitemap items
     *
     * @return void
     */

    protected function _initSitemapItems()
    {

        parent::_initSitemapItems();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Ced\Blog\Helper\Data')->getFrontendName();
        $collection =  \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Ced\Blog\Model\BlogPost'
        )->getCollection();
        $collection->getSelect()->columns(array('url' => new \Zend_Db_Expr('concat("'.$helper.'/post/" ,url)')));
        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' => $collection ,
            ]
        );
        $category_collection =   \Magento\Framework\App\ObjectManager::getInstance()->create(

            'Ced\Blog\Model\Blogcat'
        )->getCollection();
        $category_collection->getSelect()->columns(array('url' => new \Zend_Db_Expr('concat("'.$helper.'/category/",url_key)')));
        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' => $category_collection ,
            ]
        );
    }
}
