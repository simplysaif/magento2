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

class Notification extends \Ced\CsRma\Block\Vrma\Request\Chat
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
        parent::__construct($context, $customerSession, $urlFactory, $objectManager, $rmaItemFactory, $orderAddressRepository, $groupRepository, $rmaConfigHelper, $rmaDataHelper);
        $this->orderAddressRepository = $orderAddressRepository;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->groupRepository = $groupRepository;
        $this->urlModel = $urlFactory;
        $this->rmaConfigHelper = $rmaConfigHelper;
        $this->rmaDataHelper = $rmaDataHelper;
        $this->_objectManager = $objectManager;
        $this->setTemplate('vrma/notification.phtml');
    }

    protected function _prepareLayout()
    {
        
        return $this;
    }

    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */

   public function getFullNotification()
    {
        $id = $this->getRmaCollection()->getId();
        $notification = $this->_objectManager->create('Ced\CsRma\Model\Rmanotification')
                    ->getCollection()
                    ->addFieldToFilter('rma_request_id',$id);
        return $notification;
    }
    
}
