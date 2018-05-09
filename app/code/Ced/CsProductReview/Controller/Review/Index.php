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
 * @package   Ced_CsProductReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsProductReview\Controller\Review;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
    	
    	if(!$this->_objectManager->create('Ced\CsProductReview\Helper\Data')->IsEnable()){
			 $resultPage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			return $resultPage->setPath('csmarketplace/vendor/index');
		} 
        
        $resultPage = $this->resultPageFactory->create();        
        $resultPage->getConfig()->getTitle()->set(__('Manage Review'));
        return $resultPage;
        
    }
}