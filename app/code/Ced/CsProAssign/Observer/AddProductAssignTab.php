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
  * @package   Ced_CsProAssign
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsProAssign\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class AddProductAssignTab implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request,
        ManagerInterface $messageManager
    ) {
    
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
    }
    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $onj=$this->_objectManager->get('Magento\Framework\View\Element\Context');
        $block=$observer->getEvent()->getTabs();
$block->removeTab('vendor_tabs_vproducts');
        $block->addTab(
            'vproducts33', array(
            'label'     => __('Assign Products'),
            'title'     => __('Vendor Products'),
            'content'   => $onj->getLayout()->createBlock('Ced\CsProAssign\Block\Adminhtml\AddPro')->toHtml().$onj->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vproducts')->toHtml(),
              )
        );
    }
}    
?>
