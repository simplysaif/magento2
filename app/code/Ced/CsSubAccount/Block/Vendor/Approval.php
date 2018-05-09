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
 * @package     Ced_CsSubAccount
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Vendor;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
class Approval extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

	public $_vendorUrl;

	protected $urlModel;
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
	 * Set the Vendor object and Vendor Id in customer session
	 */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Ced\CsMarketplace\Model\Url $vendorUrl,
		UrlFactory $urlFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
		$this->_vendorUrl = $vendorUrl;
		$this->urlModel = $urlFactory;
		$this->_customerSession = $customerSession;
		$this->_objectManager = $objectManager;
		parent::__construct($context, $customerSession, $objectManager, $urlFactory);
	}
	
	
	
	/**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_vendorUrl->getBaseUrl();
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_vendorUrl->getLogoutUrl();
    }
	
	/**
     * Approval message
     *
     * @return String
     */
	public function getApprovalMessage() {
		$message = '';
		$message .= __('Your sub-vendor account is under approval.');
		return $message;
	}

	public function getName(){
		$email = $this->_objectManager->create('Magento\Customer\Model\Session')->getSubvendorEmail();
		$subvendor = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($email,'email');
		return $subvendor->getFirstName().' '.$subvendor->getLastName();
	}
}
