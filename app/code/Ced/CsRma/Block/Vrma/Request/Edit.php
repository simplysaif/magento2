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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Block\Vrma\Request;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;

class Edit extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    protected $rmaItemFactory;
    /**
    * @var \Ced\CsRma\Helper\Config $rmaConfigHelper
    */
    
    public $rmaConfigHelper;

    /**
    * @var \Ced\CsRma\Helper\Config $rmaConfigHelper
    */    
    
    public $rmaDataHelper;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    public $groupRepository;

	public $_vendorUrl;

	protected $urlModel;
	
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    
    public $_objectManager;
	
    /**
    * @var Magento\Sales\Model\OrderFactory
    */
    protected $orderAddressRepository;

	/**
	 * Set the Vendor object and Vendor Id in customer session
	 */
    
    public function __construct(
        Context $context,
		Session $customerSession,
		UrlFactory $urlFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsRma\Model\RmaitemsFactory $rmaItemFactory,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Ced\CsRma\Helper\Config $rmaConfigHelper,
        \Ced\CsRma\Helper\Data $rmaDataHelper
    ) {
		parent::__construct($context, $customerSession, $objectManager, $urlFactory);
        $this->orderAddressRepository = $orderAddressRepository;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->groupRepository = $groupRepository;
		$this->urlModel = $urlFactory;
        $this->rmaConfigHelper = $rmaConfigHelper;
        $this->rmaDataHelper = $rmaDataHelper;
		$this->_objectManager = $objectManager;	
	}

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'csrma_vendor_chat',
            'Ced\CsRma\Block\Vrma\Request\Chat'
        );
        /*$this->addChild(
            'ced_csrma_notification',
            'Ced\CsRma\Block\Adminhtml\AllRma\Notification'
        );*/
        return parent::_prepareLayout();
    }


    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getRmaCollection()
    {
        $id = $this->getRequest()->getParam('rma_id');
        return $this->_objectManager->get('Ced\CsRma\Model\Request')->load($id);
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->_objectManager->get('Ced\CsMarketplace\Model\Session')->isLoggedIn()) {
            return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
        }
        return $this->getUrl('*/*/form',array('_secure'=>true,'_nosid'=>true));
    }

    public function getOrderData()
    {
        return $this->rmaDataHelper->getOrderCollection($this->getRmaCollection()->getOrderId());
    }

    /**
     * Return rma request order-item collection.
     *
     * @return array
     */
    public function getVendorItemCollection()
    {
        $vendor_item = $this->rmaItemFactory->create()->getCollection()
                ->addFieldToFilter('rma_request_id',$this->getRequest()->getParam('rma_id'));
        return $vendor_item;
    }

}
