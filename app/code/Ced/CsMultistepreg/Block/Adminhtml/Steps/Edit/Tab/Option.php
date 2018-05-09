// <?php

// namespace Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Tab;
// class Option extends \Magento\Backend\Block\Widget\Tabs
// 			  //implements \Magento\Backend\Block\Widget\Tab\TabInterface
// {
// 	/**
// 	 * Initialize block
// 	 */
// 	public function __construct()
// 	{
// 		$this->setTemplate('options/array.phtml');
// 	}

// 	/**
// 	 * Render HTML
// 	 *
// 	 * @param Varien_Data_Form_Element_Abstract $element
// 	 * @return string
// 	 */
// 	public function render(Varien_Data_Form_Element_Abstract $element)
// 	{
// 		$this->setElement($element);
// 		return $this->toHtml();
// 	}

// 	/**
// 	 * Return Multi Step Collection
// 	 */
// 	public function getsteps()
// 	{
// 		$options = array('1','2','3');
// 		return $options;//Mage::getModel('csmultistepreg/multisteps')->getCollection();
// 	}
	
// 	public function getTabLabel($tab){
// 		return 'Tab';
// 	}
	

// }