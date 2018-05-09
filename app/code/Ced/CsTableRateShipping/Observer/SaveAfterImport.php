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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsTableRateShipping\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
Class SaveAfterImport implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_quoteFactory;
    protected $_advanceFactory;
    protected $_object;
    private $messageManager;
    
    public function __construct(        
        \Ced\CsTableRateShipping\Model\Resource\Carrier\TablerateFactory $advanceFactory,
        \Magento\Framework\DataObject $object,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        ManagerInterface $messageManager
    ) {
    
        $this->_object = $object;
        $this->_objectManager = $objectManager;
        $this->_quoteFactory = $quoteFactory;
        $this->_advanceFactory = $advanceFactory;
        $this->messageManager = $messageManager;
    }
    
    /**
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
   
        $eventData = $observer->getData();
        if (empty($_FILES['groups']['name']['tablerate']['import'])) {
            return $this;
        }
        try{
            $this->_advanceFactory->create()->uploadAndImport($this->_object);   
        }catch(\Exception $e){
            $this->messageManager->addError(__($e->getMessage()));
        }
    }    
}
