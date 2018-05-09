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
  * @package   Ced_CsVendorAttribute
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsVendorAttribute\Controller\Adminhtml\Attributes;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Edit extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_entityTypeId;
    //protected $registry;
    
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    
    
    public function __construct(
        Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $attributeLabelCache, $coreRegistry, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Index action
     *
     * @return void
     */
    
    public function execute()
    {        
        $id = $this->getRequest()->getParam('attribute_id');
        /**
 * @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute 
*/
        $model = $this->_objectManager->create(
            'Ced\CsMarketplace\Model\Vendor\Attribute'
        );

        if ($id) {
        	$storeId = (int) $this->getRequest()->getParam('store', 0);
            $model->setStoreId($storeId)->load($id);
        
            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->_entityTypeId = $this->_objectManager->create(
                'Magento\Eav\Model\Entity'
            )->setType(
                'csmarketplace_vendor'
            )->getTypeId();
            
            //$this->_entityTypeId = Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId();
            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        //$attributeData = $this->getRequest()->getParam('attribute');
        //if (!empty($attributeData) && $id === null) {
        //$model->addData($attributeData);
        //}
        
        $this->_coreRegistry->register('entity_attribute', $model);
        
        $item = $id ? __('Edit Vendor Attribute') : __('New Vendor Attribute');
        
        $resultPage = $this->createActionPage($item);
        
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Vendor Attribute'));
        $resultPage->getLayout()
            ->getBlock('attribute_edit_js')
            ->setIsPopup((bool)$this->getRequest()->getParam('popup'));
        return $resultPage;
    }   
}