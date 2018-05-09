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


namespace Ced\Blog\Controller\Index;


/**
 * @param Magento\Framework\Controller\ResultFactory
 * @param Magento\Paypal\Test\Unit\Model\ProTest
 * @param Ced\Blog\Model\Status
 * @param Ced\Blog\Model\BlogComment
 * @param Magento\Core\Controller\Varien\Action
 */

use Magento\Framework\Controller\ResultFactory;
use Magento\Paypal\Test\Unit\Model\ProTest;
use Ced\Blog\Model\Status;
use Ced\Blog\Model\BlogComment;
use Magento\Core\Controller\Varien\Action;


class Save extends  \Magento\Framework\App\Action\Action
{

    /**
     * @param formKeyValidator
     */

    protected $_formKeyValidator;

    /**
     * @param redirect
     */

    protected $_redirect;

    /**
     * @param reviewSession
     */

    protected $reviewSession;

    /**
     * @param objectManager
     */

    protected $_objectManager;

    /**
     * @param customerSession
     */

    protected $customerSession;

    /**
     * @param resultPageFactory
     */


    protected $resultPageFactory;

    /**
     * @param date
     */

    protected $date;

    /**
     * @param resultRedirectFactory
     */

    protected $resultRedirectFactory;

    /**
     * @param Magento\Framework\App\Action\Context
     * @param Magento\Framework\Session\Generic
     * @param Magento\Customer\Model\Session
     * @param Magento\Framework\Data\Form\FormKey\Validator
     * @param Magento\Framework\Stdlib\DateTime\TimezoneInterface
     * @param Magento\Framework\View\Result\PageFactory
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Backend\Model\View\Result\Redirect
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory
    ) {

        $this->messageManager = $context->getMessageManager();
        $this->date = $date;
        $this->resultPageFactory =$resultPageFactory;
        $this->_objectManager = $context->getObjectManager();
        $this->_formKeyValidator = $formKeyValidator;
        $this->_redirect = $context->getRedirect();
        $this->customerSession = $customerSession;
        parent::__construct($context);

    }

    /**
     * @param execute
     * return redirect page
     */

    public function execute()
    {

        $helper = $this->_objectManager->create('Ced\Blog\Helper\Data')->getFrontendName();
        $id =$this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $post_data=$model->getData();

        /* for remote address */

        $remote =  $this->_objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        if (!empty($data)) {
            $review = $this->_objectManager->create('Ced\Blog\Model\BlogComment')->setData($data);
            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $store= $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
                    $config_value=$store->getValue('ced_blog/comment/auto_approve_comments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
                    if($config_value) {
                        $review->setStatus(Status::STATUS_APPROVED);
                    }
                    else {
                        $review->setStatus(Status::STATUS_PENDING);
                    }

                    $review->setPostId($id)
                        ->setPostTitle($post_data['title'])
                        ->setApprove(Status::STATUS_DISAPPROVED)
                        ->setIpAddress($remote->getRemoteAddress())
                        ->setCreatedAt($this->date->formatDate())
                        ->setUserId($this->customerSession->getCustomerId());
                    if ($this->customerSession->getCustomerId()) {
                        $review->setUserType(1);
                    }
                    else {
                        $review->setUserType(2);
                    }
                    $review->save();
                    $this->messageManager->addSuccess(__('Your comment has been submitted'));
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addError(__('We can\'t post your comment right now.'));
                }
            }else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }

                } else {
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            }
        }
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}

