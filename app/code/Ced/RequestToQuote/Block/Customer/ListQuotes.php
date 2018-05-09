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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\RequestToQuote\Block\Customer;

use Magento\Framework\View\Element\Template\Context;

class ListQuotes extends \Magento\Framework\View\Element\Template {
	public function __construct(
		Context $context, 
		\Magento\Customer\Model\Session $customerSession, 
		\Magento\Framework\ObjectManagerInterface $objectManager
		) {
			$this->_getSession = $customerSession;
			$this->_objectManager = $objectManager;
			parent::__construct ( $context );
	}
	public function _construct() {
		$this->setTemplate ( 'customer/listquotes.phtml' );
		$this->getUrl();
		$customer = $this->_getSession->getCustomer ();
		$customer_Id = $customer->getId ();
		$quoteModel = $this->_objectManager->create ( 'Ced\RequestToQuote\Model\Quote' )->getCollection ()->addFieldtoFilter ( 'customer_id', [ 
				'customer_id' => $customer_Id 
		] );
		$this->setCollection ( $quoteModel );
	}
	
	/**
	 * Prepare Pager Layout
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout ();
		if ($this->getCollection ()) {
			$pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'my.custom.pager' )->setLimit ( 5 )->setCollection ( $this->getCollection () );
			$this->setChild ( 'pager', $pager );
		}
		$this->pageConfig->getTitle ()->set ( "My Quotes" );
		return $this;
	}
	public function getPagerHtml() {
		return $this->getChildHtml ( 'pager' );
	}

	 public function getCurrencyCode(){

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
       $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
       return $currency->getCurrencySymbol();
    }

}