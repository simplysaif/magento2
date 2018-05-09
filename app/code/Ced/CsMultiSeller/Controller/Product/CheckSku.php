<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsMultiSeller\Controller\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class CheckSku extends \Ced\CsMarketplace\Controller\Vendor
{
    /** @var  \Magento\Framework\View\Result\Page */
   
    
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
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param ModuleFactory $moduleManager
     */
    public function __construct(
    		Context $context,
    		\Magento\Customer\Model\Session $customerSession,
    		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    		\Magento\Framework\UrlFactory $urlFactory,
    		\Magento\Framework\Module\Manager $moduleManager
    ) {
    	$this->session = $customerSession;
    	$this->resultPageFactory = $resultPageFactory;
    	$this->urlModel = $urlFactory;
    	$this->_resultPageFactory  = $resultPageFactory;
    	$this->_moduleManager = $moduleManager;
    	parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
    	//     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }
    
    /**
     * Check whether the sku is available or not
     */
    public function execute()
    {
      	/*if(!$this->_getSession()->getVendorId()) return;
  		$sku=$this->getRequest()->getParam('sku');
  		$current_id=$this->getRequest()->getParam('id');
  		$id = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($sku);
  		if($current_id && $id==$current_id)
  			$result=1;
  		else if($id)
  			$result=0; 
  		else
  			$result=1;
  		echo json_encode(array("result"=>$result));*/    

      if(!$this->_getSession()->getVendorId()) return;
        $sku=$this->getRequest()->getParam('sku');
        $current_id=$this->getRequest()->getParam('id');
        $id = 0;
        $id = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($sku);
        
        if($id){ 
            echo 'false';die;
        }else{
            echo 'true';die;
        } 
    }
}
