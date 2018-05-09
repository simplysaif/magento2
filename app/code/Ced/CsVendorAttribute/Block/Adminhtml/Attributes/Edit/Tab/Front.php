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
namespace Ced\CsVendorAttribute\Block\Adminhtml\Attributes\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
//use Magento\Framework\Setup\ModuleContextInterface;

class Front extends Generic
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;
    
    protected $_objectManager;
    protected $_coreRegistry;

    /**
     * @param Context        $context
     * @param Registry       $registry
     * @param FormFactory    $formFactory
     * @param Yesno          $yesNo
     * @param PropertyLocker $propertyLocker
     * @param array          $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        PropertyLocker $propertyLocker,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
   
    
    
    
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $this->setForm($form);
        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');//Mage::getModel('csmarketplace/vendor');
        $entityTypeId  = $vendor->getEntityTypeId();
        $setIds =$this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')->setEntityTypeFilter($entityTypeId)->getAllIds(); //Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeId)->getAllIds();
        $setId = isset($setIds[0])?$setIds[0]:0;
    
        $options = $this->getGroupOptions($setId, true);
        $attribute = $this->_coreRegistry->registry('entity_attribute');//Mage::registry('entity_attribute');
        $groupName = '';
        //$installer = new Ced_CsVAttribute_Model_Resource_Setup();
        //$attributeGroupObject = new Varien_Object($installer->getAttributeGroup($entityTypeId ,$attributeSetProductsId,"Prices"));
        foreach($this->getGroupOptions($setId, false) as $id=>$label) {
            $attributes = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')->getCollection()->setAttributeGroupFilter($id)->getAllIds();//Mage::getModel('eav/entity_attribute')->getCollection()->setAttributeGroupFilter($id)->getAllIds();
            if(in_array($attribute->getId(), $attributes)) {
                $groupName = $label;
                break;
            }
        }
    
        $fieldset = $form->addFieldset('group_fieldset', array('legend'=>__('Group')));
    
    
        $element = $fieldset->addField(
            'group_select', 'select',
            array(
                        'name'      => "group",
                        'label'     => __('Group'),
                        'required'  => true,
                        'values'    => $options,
                        'after_element_html' => $this->getChildHtml('csmarketplace_add_new_group_button'),
            )
        );
        if(strlen($groupName)) {
            $element->setValue($groupName);
        }
        /* if( Mage::getSingleton('adminhtml/session')->getEntityAttribute()){
        $form->setValues(Mage::getSingleton('adminhtml/session')->getEntityAttribute());
        Mage::getSingleton('adminhtml/session')->setEntityAttribute(null);
        }elseif( Mage::registry('entity_attribute')){
        $form->setValues(Mage::registry('entity_attribute')->getData());
        } */
        return parent::_prepareForm();
    }
    
    
    protected function getGroupOptions($setId,$flag = false) 
    {
        
        $groupCollection = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection')//Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($setId);
        
      
            $groupCollection->setSortOrder()
                ->load();
        
        $options = array();
        if($flag) {
            foreach ($groupCollection as $group) {
                $options[$group->getAttributeGroupName()] = __($group->getAttributeGroupName());
            }
        } else {
            foreach ($groupCollection as $group) {
                $options[$group->getId()] = $group->getAttributeGroupName();
            }
        }
        return     $options;
    }
}
