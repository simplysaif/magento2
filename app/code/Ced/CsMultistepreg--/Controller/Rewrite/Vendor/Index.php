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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Controller\Rewrite\Vendor;

use Zend\Crypt\PublicKey\Rsa\PrivateKey;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
class Index extends \Ced\CsMarketplace\Controller\Vendor\Index{
	
	protected $session;
	protected $resultPageFactory;
	public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
		UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
       
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
	}
	
	
	public function execute(){
		/**
		 *
		 *
		 * @var \Magento\Framework\View\Result\Page $resultPage
		 */
		
		$vendorId = $this->session->getVendorId();
		$vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
		if(!$vendor->getMultistepDone()){
			$enabled = $this->_objectManager->create('Ced\CsMultistepreg\Helper\Data')->isEnabled();
			if($enabled == '1')	
			{
				$this->_redirect('csmultistep/multistep/index',array('id'=>$vendor->getId()));
			}
				
		}
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Dashboard'));
		return $resultPage;
	}
}