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
namespace Ced\CsRma\Block\Adminhtml\AllRma\Edit;

class Form extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'edit/form.phtml';

     /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    public $dateTime;
    
    /**
    * @var \Ced\CsRma\Model\RequestFactory
    */
    
    public $rmaRequestFactory;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    public $groupRepository;
    
    /**
    * @var \Ced\CsRma\Helper\Config $rmaConfigHelper
    */    
    
    public $rmaDataHelper;
    
    /**
    * @var \Ced\CsRma\Helper\Config $rmaConfigHelper
    */
    
    public $rmaConfigHelper;

    /**
    * @var   \Magento\Framework\ObjectManagerInterface $objectManager
    */
    
    public $objectManager;

     /**
    * @var Magento\Sales\Model\OrderFactory
    */
    protected $orderAddressRepository;

    protected $rmaItemFactory;
    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\Rma\Model\RmaitemsFactory $rmaItemFactory
     * @param \Ced\Rma\Model\RequestFactory $rmaRequestFactory
     * @param \Ced\Rma\Helper\Config $rmaConfigHelper
     * @param \Ced\Rma\Helper\Data $rmaDataHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Ced\CsRma\Model\RmaitemsFactory $rmaItemFactory,
        \Ced\CsRma\Model\RequestFactory $rmaRequestFactory,
        \Ced\CsRma\Helper\Config $rmaConfigHelper,
        \Ced\CsRma\Helper\Data $rmaDataHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        array $data = []
    ) {
        
        $this->orderAddressRepository = $orderAddressRepository;
        $this->objectManager = $objectManager;
        $this->groupRepository = $groupRepository;
        $this->dateTime = $dateTime;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->rmaDataHelper = $rmaDataHelper;
        $this->rmaConfigHelper = $rmaConfigHelper;
        parent::__construct($context,$data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'ced_csrma_chat',
            'Ced\CsRma\Block\Adminhtml\AllRma\Chat'
        );
        return parent::_prepareLayout();
    }
    /**
     * Return the Url for saving.
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_urlBuilder->getUrl(
            'csrma/allRma/save',['_secure' => true]
        );
    }
    
    /**
     * Return rma request table collection.
     *
     * @return array
     */
    public function getRequestCollection()
    {
        $request_collect = $this->rmaRequestFactory->create()->load($this->getRequest()->getParam('id'));
        return $request_collect;
    }

    public function getOrderData()
    {
        return $this->rmaDataHelper->getOrderCollection($this->getRequestCollection()->getOrderId());
    }

    /**
     * Return rma request order-item collection.
     *
     * @return array
     */
    public function getItemCollection()
    {
        $item_collect = $this->rmaItemFactory->create()
                ->getCollection()
                ->addFieldToFilter('rma_request_id',
                    $this->getRequest()->getParam('id'));
        return $item_collect;
    }

    public function getYesNo()
    {
        $yesnoSource = $this->objectManager->create('Magento\Config\Model\Config\Source\Yesno')
                ->toOptionArray();
        return $yesnoSource;
    }
}
    