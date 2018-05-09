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
 * @package   Ced_RequestToQuote
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\RequestToQuote\Controller\Customer;

use Magento\Backend\App\Action\Context;

class DeleteQuote extends \Magento\Framework\App\Action\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected $__entityTypeId;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request

    ) {
        $this->_request = $request;
        parent::__construct($context);

    }

    public function execute()
    {

        $quote_id = $this->getRequest()->getParam('quoteId');
        try {
            $quote = $this->_objectManager->get('Ced\RequestToQuote\Model\Quote')->load($quote_id);
            $quote->delete();
            $quotedetail =  $this->_objectManager->get('Ced\RequestToQuote\Model\QuoteDetail')->getCollection()->addFieldToFilter('quote_id',$quote_id);
            foreach ($quotedetail as $value) {
                $value->delete();
            }
            $this->messageManager->addSuccess(
                __('Deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('requesttoquote/customer/quotes');
    }

}
