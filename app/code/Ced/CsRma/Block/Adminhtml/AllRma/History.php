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
namespace Ced\CsRma\Block\Adminhtml\AllRma;

class History extends \Magento\Backend\Block\Template
{
   
    /**
     * @var \Ced\CsRma\Helper\Config
     */
    public $rmaConfigHelper;

    /**
     * @var \Ced\CsRma\Model\RmachatFactory
     */
    public $rmaChatFactory;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $_coreRegistry = null;

    /**
     * @var string
     */
    protected $_template = 'edit/history.phtml';

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    private $adminHelper;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    
    public $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsRma\Helper\Config $rmaConfigHelper,
        \Ced\CsRma\Model\RmachatFactory $rmaChatFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
    	$this->_storeManager = $context->getStoreManager();
        $this->rmaConfigHelper = $rmaConfigHelper;
        $this->rmaChatFactory = $rmaChatFactory;
        $this->_coreRegistry = $registry;
        $this->adminHelper = $adminHelper;
        parent::__construct($context, $data);
    }
    

    public function getCustomerValue()
    {
        var_dump($this->getItemCollection()->getCustomerId());die('dfdf');
    }

    public function getChatDataCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->rmaChatFactory->create()
                ->getCollection()
                ->addFieldToFilter('rma_request_id',$id);
        return $model;

    }

    /**
     * Retrieve customer model using customer id from rma request id
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getRmaCustomer()
    {
        $customerId = $this->_coreRegistry->registry('ced_csrma_request')->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')
            ->load($customerId)->getName();
        return $customer;
    }
}
