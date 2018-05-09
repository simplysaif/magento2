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
 * @category Ced
 * @package Ced_CsProductfaq
 * @author CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductfaq\Block;

class Faq extends \Magento\Backend\Block\Widget\Container
{
    protected $_template = 'Ced_CsProductfaq::faq.phtml';
    protected $pageLayoutBuilder;
    public $_objectManager;
    
    
    public function __construct(
            \Magento\Backend\Block\Widget\Context $context,
            \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            array $data = []
    ) {

        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        
    }

    public function _construct()
    {
        parent::_construct();
        //$this->setData('area','adminhtml');
    }

    /**
     * Prepare button and grid
     *
     * @return Ced_CsCmsPage_Block_Adminhtml_CsCmsPage_Cmspage
     */
    protected function _prepareLayout()
    {
         
        $newurl=$this->getUrl('*/*/add');
        $this->addButton('add_new', array(
                'label'   => ('Add New Faq'),
                'onclick' => "setLocation('{$newurl}')",
                'class'   => 'add',
                'area' => 'adminhtml'
                        ));

        // $this->setChild('grid', $this->getLayout()->createBlock('Ced\CsCmsPage\Block\Grid'));
         
         
        $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Ced\CsProductfaq\Block\Faq\Grid', 'ced.csproductfaq.vendor.faq.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Deprecated since 1.3.2
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {

        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode())
        {
            return false;
        }
        return true;
    }
    
    public function getfaqValues($vid)
    {
        $collection= $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('vendor_id',$vid);
        /* print_r($collection); die("lkjg");
        $collection = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', '1')
            ->addAttributeToFilter('visibility', '4');
        
        print_r($collection->getData());die("lkj"); */
        foreach($collection as $key => $value){
            $arr[$value->getProductId()] = $value->getName();
        }
        foreach ($arr as $key=>$val){
            $options[]= array('value'=>$key, 'label'=>$val);
        }
      
        return $options;
    }
    
    
}
