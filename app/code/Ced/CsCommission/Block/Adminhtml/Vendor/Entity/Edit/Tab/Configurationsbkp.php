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
 * @package     Ced_CsCommission
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsCommission\Block\Adminhtml\Vendor\Entity\Edit\Tab;
 
class Configurations extends \Magento\Config\Block\System\Config\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\Data\FormFactory $formFactory,
			\Magento\Config\Model\Config\Factory $configFactory,
			\Magento\Config\Model\Config\Structure $configStructure,
			\Magento\Config\Block\System\Config\Form\Fieldset\Factory $fieldsetFactory,
			\Magento\Config\Block\System\Config\Form\Field\Factory $fieldFactory,
			array $data = []
	) {
		
		$this->_configFactory = $configFactory;
		$this->_configStructure = $configStructure;
		$this->_fieldsetFactory = $fieldsetFactory;
		$this->_fieldFactory = $fieldFactory;
		$this->_coreRegistry = $registry;
	
		parent::__construct($context, $registry, $formFactory, $configFactory,$configStructure,$fieldsetFactory,$fieldFactory);
	}
	
	public function getTabLabel() {
		return __('Commission Configuration');
	}
	
	public function getTabTitle() {
		return __('Commission Configuration');
	}
	
	public function canShowTab() {
		if ($this->_coreRegistry->registry('vendor_data') && is_object($this->_coreRegistry->registry('vendor_data')) && $this->_coreRegistry->registry('vendor_data')->getId()) {
			return true;
		}
		return false;
	}
	
	public function isHidden() {
		return !$this->canShowButton();
	}
	
	public function getAfter() {
		return 'vpayments';
	}
	
	public function canShowButton() {
		if ($this->_coreRegistry->registry('vendor_data') && is_object($this->_coreRegistry->registry('vendor_data')) && $this->_coreRegistry->registry('vendor_data')->getId()) {
			return true;
		}
		return false;
	}
	
	protected function _initObjects()
	{
		parent::_initObjects();
		//$this->_defaultFieldsetRenderer = Mage::getBlockSingleton('csgroup/adminhtml_system_config_form_fieldset');
		//$this->_defaultFieldRenderer = Mage::getBlockSingleton('csgroup/adminhtml_system_config_form_field');
		return $this;
	}
	protected function _prepareForm(){
		try {
			$this->initForm();
		} catch (Exception $e) {
			print_r($e->getMessage());die;
		}
	}
	
	
	public function getSectionCode()
	{
		return 'ced_csmarketplace';
	}
	
	/**
	 * Checking field visibility
	 *
	 * @param   Varien_Simplexml_Element $field
	 * @return  bool
	 */
	protected function _canShowField($field)
	{
		$canShow = parent::_canShowField($field);
		if($canShow) {
			return (int)$field->show_in_commission;
		}
		return $canShow;
	
	}
	
	protected function _toHtml() {
		if($this->getRequest()->isAjax()) {
			return parent::_toHtml();
		}
		$switcher = '';
		/* if(Mage::helper('core')->isModuleEnabled('Ced_CsGroup')) {
			$switcher = $this->getLayout()->createBlock('adminhtml/template')->setStoreSelectOptions($this->getStoreSelectOptions())->setTemplate('csgroup/system/config/switcher.phtml')->toHtml();
			$switcher .= '<style>.switcher p{ display: none; }</style>';
		} */
		$parent = '<div id="vendor_group_configurations_section">'.parent::_toHtml().'</div>';
		if(strlen($parent) <= 50) {
			$parent .= '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li><span>'.__('No Configurations are Available for Current Configuration Scope. Please Up the Configuration Scope by One Level.').'</span></li></ul></li></ul></div>';
			return $parent/* .$switcher */;
		}
		return /* $switcher. */$parent;
	}
	


	 public function initForm()
    {
        $this->_initObjects();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($this->getSectionCode());
        if ($section && $section->isVisible($this->getWebsiteCode(), $this->getStoreCode())) {
            foreach ($section->getChildren() as $group) {
            	if($group->getId() == 'vpayments'){	
                	$this->_initGroup($group, $section, $form);
            	}
            }
        }

        $this->setForm($form);
        return $this;
    }
	
    public function initFields(
    		\Magento\Framework\Data\Form\Element\Fieldset $fieldset,
    		\Magento\Config\Model\Config\Structure\Element\Group $group,
    		\Magento\Config\Model\Config\Structure\Element\Section $section,
    		$fieldPrefix = '',
    		$labelPrefix = ''
    ) {
    	if (!$this->_configDataObject) {
    		$this->_initObjects();
    	}
    
    	// Extends for config data
    	$extraConfigGroups = [];
    	//$objectManager = \Magento\Framework\Registry::getInstance();

    	foreach ($group->getChildren() as $element) {
    		if ($element instanceof \Magento\Config\Model\Config\Structure\Element\Group) {
    			$this->_initGroup($element, $section, $fieldset);
    		} else {
    			$path = $element->getConfigPath() ?: $element->getPath($fieldPrefix);
    		
    			 if ($this->_coreRegistry->registry('vendor_data') && is_object($this->_coreRegistry->registry('vendor_data')) && $this->_coreRegistry->registry('vendor_data')->getId()) {
    				$path =  $this->_coreRegistry->registry('vendor_data')->getId(). '/' . $path;
    			
    			} 
    			
    			if ($element->getSectionId() != $section->getId()) {
    				$groupPath = $element->getGroupPath();
    				if (!isset($extraConfigGroups[$groupPath])) {
    					$this->_configData = $this->_configDataObject->extendConfig(
    							$groupPath,
    							false,
    							$this->_configData
    					);
    					$extraConfigGroups[$groupPath] = true;
    				}
    			}
    			$this->_initElement($element, $fieldset, $path, $fieldPrefix, $labelPrefix);
    		}
    	}
    	return $this;
    }
     
    public function getConfigValue($path)
    {
    	if ($this->_coreRegistry->registry('vendor_data') && is_object($this->_coreRegistry->registry('vendor_data')) && $this->_coreRegistry->registry('vendor_data')->getId()) {
    		$paths =  $this->_coreRegistry->registry('vendor_data')->getId(). '/' . $path;
    		if($this->_scopeConfig->getValue($paths, $this->getScope(), $this->getScopeCode())!=Null){
    		 return $this->_scopeConfig->getValue($paths, $this->getScope(), $this->getScopeCode());
    	       }  
    	}
    	 
    	return $this->_scopeConfig->getValue($path, $this->getScope(), $this->getScopeCode());
    }
}
