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
 * @package     Ced_CsTeamMember
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\TeamMember\Controller;

use Ced\TeamMember\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class TeamMember extends \Magento\Framework\App\Action\Action
{
    public static $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
    'approval',
    'approvalPost',
    'checkAvailability',
    'denied',
    'noRoute'
        );
        
    public $_allowedResource = true;
    
    /**
* 
     *
 * @var Session 
*/
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /** 
     * @var \Magento\Framework\UrlInterface 
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
        
     /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param Context     $context
     * @param Session     $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $teammemberSession,
        PageFactory $resultPageFactory
    ) {
       
        $this->session = $teammemberSession;
        $this->resultPageFactory = $resultPageFactory;
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Registry');
        parent::__construct($context);
        //     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }
    
    /**
* 
 * Retrieve customer session model object
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }
    
    /**
     * Get list of actions that are allowed for not authorized users
     *
     * @return string[]
     */
    protected function getAllowedActions()
    {
        return self::$openActions;
    }
    
    /**
     * Dispatch request
     *
     * @param  RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {     
    	if (!$this->getRequest()->isDispatched()) {
    		parent::dispatch($request);
    	}
    	$result = parent::dispatch($request);
    	//$this->session->unsNoReferer(false);
    	return $result;
    	
    }
    
    public function aunthicate(TeamMember $action, $loginUrl = null)
    {
    	if (!$this->session->isLoggedIn()) {
    		
    	
    			$oAuthUrl = $this->urlModel->create()->getUrl('*/*/*', ['_current'=>true]);
    			if(!preg_match('/'.preg_quote('teammember').'/i', $oAuthUrl)) {
    				$oAuthUrl = $this->urlModel->create()->getUrl('csmarketplace/vendor/index', ['_current'=>true]);
    			
    			$this->session->setBeforeVendorAuthUrl($oAuthUrl);
    		}
    		if (is_null($loginUrl)) {
    	
    			$url='teamme/account/login';
    			$loginUrl = $this->urlModel->create()->getUrl($url, ['_secure' => $action->getRequest()->isSecure()]);
    		}
    		if($action->getRequest()->isAjax()) {
    			$ajaxResponse=array();
    			$ajaxResponse['ajaxExpired']=true;
    			$ajaxResponse['ajaxRedirect']=$loginUrl;
    			$action->getResponse()->setBody(json_encode($ajaxResponse));
    			$resultJson = $this->resultJsonFactory->create();
    			//print_r($resultJson);die(";lj");
    			return $resultJson->setData($response);
    		}
    		$action->getResponse()->setRedirect($loginUrl);
    		return false;
    	}
    	if($this->session->isLoggedIn() && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {
    		$vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($this->session->getCustomerId());
    		if($vendor && $vendor->getId()) {
    			$this->session->setVendorId($vendor->getId());
    			$this->session->setVendor($vendor->getData());
    			$this->_eventManager->dispatch(
    					'ced_csmarketplace_vendor_authenticate_after', [
    					'session' => $this->session
    					]
    			);
    		}
    	}
    	$this->_eventManager->dispatch(
    			'ced_csmarketplace_vendor_acl_check', [
    			'current' => $this,
    			'action'  => $action,
    			]
    	);
    	return $this->_allowedResource;
    }
    
    /**
     * Authenticate controller action by login customer
     *
     * @param  Mage_Core_Controller_Varien_Action $action
     * @param  bool                               $loginUrl
     * @return bool
     */
    
   public function execute(){}
}
