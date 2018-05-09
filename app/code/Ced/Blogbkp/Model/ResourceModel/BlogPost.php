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

namespace Ced\Blog\Model\ResourceModel;

use Ced\Blog\Model\IFrontendRoute;

class BlogPost extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected function _construct()
    {

        $this->_init('ced_blog_post', 'id');

    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     * @param string $identifier
     * @param int    $storeId
     * @return int
     */

    public function checkIdentifier($identifier)
    {
        $select = $this->getConnection()->select()->from(
            ['blog_post' => $this->getMainTable()]            
        )->where(
            'blog_post.url = ?',
            $identifier
        )->where('blog_post.blog_status ="publish"');
        return $this->getConnection()->fetchOne($select);
    } 

    

    /**
     * Check if Author exist for specific url
     * return page id if page exists
     * @param string $identifier
     * @param int    $storeId
     * @return int
     */

    public function checkAuthor($identifier)
    {
        $select = $this->getConnection()->select()->from(
            ['blog_post' => $this->getMainTable()]
        )->where(
            'blog_post.author = ?',
            $identifier
        );
        return $this->getConnection()->fetchOne($select);

    }

    /**
     * Check if tag  exist for specific url
     * return  id if url exists
     * @param string $identifier
     * @param int    $storeId
     * @return int
     */

    public function checkTag($identifier)
    {
        $select = $this->getConnection()->select()->from(
            ['blog_post' => $this->getMainTable()]
        )->where(
            'blog_post.tags like ?',
            '%'.$identifier.'%'
        )->where('blog_post.blog_status ="publish"');
        return $this->getConnection()->fetchOne($select);

    }



    public function validate($object, $request)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeSet = $objectManager->create('Ced\Blog\Model\BlogPost')->getCollection()
            ->addFieldToFilter('url', array('like'=>$request->getParam('url')));
        if($object->getId()) {                
            $attributeSet->addFieldToFilter('id', array('nin'=>$object->getId())); 
        }

        if(count($attributeSet)>0) {
            return ['url' => 'Url already exists'];
        }
        return true;
    }
}
