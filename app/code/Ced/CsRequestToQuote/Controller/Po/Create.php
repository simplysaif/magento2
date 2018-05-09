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
  * @package   Ced_CsRequestToQuote
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsRequestToQuote\Controller\Po; 
 
class Create extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * 
     *
     * @var \Magento\Framework\View\Result\Page 
     */
    protected $resultPageFactory;

    /**
     * Try to load valid order by order_id and register it
     *
     * @param  int $orderId
     * @return bool
     */

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    { 
        $resultPage = $this->resultPageFactory->create();
        $quote_increment_id = $this->_objectManager->create('Ced\RequestToQuote\Model\Quote')->load($this->getRequest()->getParam('quote_id'))->getQuoteIncrementId();
        $resultPage->getConfig()->getTitle()->set(__('Submit PO for quote #'.$quote_increment_id));
        return $resultPage;

    }
}

