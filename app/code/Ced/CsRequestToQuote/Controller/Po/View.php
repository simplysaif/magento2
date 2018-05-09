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
 
class View extends \Ced\CsMarketplace\Controller\Vendor
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
        $po_increment_id = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($this->getRequest()->getParam('id'))->getPoIncrementId();
        $resultPage->getConfig()->getTitle()->set(__('PO # '. $po_increment_id));
        return $resultPage;

    }
}

