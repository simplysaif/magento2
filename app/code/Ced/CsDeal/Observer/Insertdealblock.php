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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
Class Insertdealblock implements ObserverInterface
{
	
	protected $_objectManager;
	protected $_quoteFactory;
	protected $_advanceFactory;
	protected $_object;
    protected $_coreRegistry = null;
    protected $frontController;
    protected $request;
	
	public function __construct(		
			\Ced\CsDeal\Model\DealFactory $advanceFactory,
			\Magento\Framework\DataObject $object,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Quote\Model\QuoteFactory $quoteFactory,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\App\FrontControllerInterface $frontController,
            \Magento\Framework\App\Request\Http $request
	)
    {
    	$this->_object = $object;
        $this->_objectManager = $objectManager;
        $this->_quoteFactory = $quoteFactory;
       	$this->_advanceFactory = $advanceFactory;
        $this->_coreRegistry = $registry;
        $this->frontController = $frontController;
        $this->request = $request;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

     	$_block = $observer->getBlock();
        print_r(get_class($_block));
        $_type = $_block->getType();
       if($_type == 'Magento\Catalog\Pricing\Render')
       {    
            if(!$this->_coreRegistry->registry('view_id_deal_in')){
                $execute=true;
            }
    
             if($this->_objectManager->get('Magento\Framework\App\Request\Http')->getFullActionName()=='catalog_product_view' && $execute){
                $this->_coreRegistry->registry('view_id_deal_in','7');
                $_child = clone $_block;
                $_child->setType('core/template');
                $_block->setChild('child'.$_block->getProduct()->getId(), $_child);
                $_block->setTemplate('Ced_CsDeal::csdeal/show/deal.phtml');
            }else if($this->_objectManager->get('Magento\Framework\App\Request\Http')->getFullActionName()=='csmarketplace_vshops_view' || $this->_objectManager->get('Magento\Framework\App\Request\Http')->getFullActionName()=='catalog_category_view'){
                $_child = clone $_block;
                $_child->setType('view/template');
                $_block->setChild('child'.$_block->getProduct()->getId(), $_child);
                $_block->setTemplate('Ced_CsDeal::csdeal/show/deal.phtml');
            }   
       }
        
           
    }	
    

}