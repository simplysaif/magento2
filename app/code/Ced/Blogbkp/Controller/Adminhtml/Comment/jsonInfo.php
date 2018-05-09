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
namespace Ced\Blog\Controller\Adminhtml\Comment;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObject;


class jsonInfo extends \Magento\Backend\App\Action
{
    /**
     * @var objectManager
     */
    protected $_objectManager;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * jsonInfo constructor.
     * @param Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
    
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new DataObject();
        $id = $this->getRequest()->getParam('id');
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        $user=$currentUser->getData();
        if($user['user_id']==1) {
            $collection= $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        }
        else
        {    
            $collection= $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
            //var_dump($collection->getData());die('here');
        }
        $response->setId($id);
        $response->addData($collection->getData());
        $resultJson = $this->resultPageFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response->toArray());
        return $resultJson;
        //echo 'ghgj';//$collection->getTitle();
    } 
    
    /**
     * @return string
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::comment');
    }
}
