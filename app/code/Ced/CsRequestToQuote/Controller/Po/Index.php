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
 * @package     Ced_CsRequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsRequestToQuote\Controller\Po; 
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
//\Ced\CsMarketplace\Controller\Vendor 
class Index extends \Ced\CsMarketplace\Controller\Vendor  {
    /** @var  \Magento\Framework\View\Result\Page */
   
    protected $resultPageFactory;
	
    /**
     * Blog Index, shows a list of recent blog posts.
	 * @return \Magento\Framework\View\Result\PageFactory
     */
	public function execute(){
		$resultPage = $this->resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->set(__('Vendor PO Management'));		
		return $resultPage; 
   }
}