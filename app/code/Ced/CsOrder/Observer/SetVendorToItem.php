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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsOrder\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class SetVendorToItem implements ObserverInterface
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
     *Set vendor name and url to product incart
     *
     *@param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 

        $quote = $observer->getEvent()->getQuote();
        //$quote_items = $quote->getItemsCollection();
        
        $vProducts = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts');
        
        
            
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        if($product->getTypeId()=='configurable') {
            return $this; 
        }
                
        if($vendorId = $vProducts->getVendorIdByProduct($item->getProductId())) {
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $publicName = $vendor->getPublicName();
            $shopUrl = $vendor->getVendorShopUrl();
                
            $additionalOptions = array(array(
            'code'  => 'my_code',
            'label'  => 'Vendor',
            'value' => '<a href="'.$shopUrl.'" target="_blank">'.$publicName.'</a>'
            ));
            $item->addOption(
                array(
                'code' => 'additional_options',
                'value' => serialize($additionalOptions),
                )
            );
        }    
                
                

    }
}
