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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Renderer;
//Ced\CsMultistepreg\Block\Adminhtml\Steps\Edit\Renderer\Options
class Options extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
       \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
 {
	/**
	 * Initialize block
	 */
	public function __construct(\Magento\Backend\Block\Template\Context $context)
	{
		$this->setTemplate('options/array.phtml');
		return parent::__construct($context);
		
	}

	/**
	 * Render HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
		public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
       {
               $this->_element = $element;
               $html = $this->toHtml();
               return $html;
       }

	/**
	 * Return Multi Step Collection
	 */
	public function getsteps()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$stepsObject = $objectManager->create('Ced\CsMultistepreg\Model\Steps');
		return $stepsObject->getCollection();
	}
	

	public function getTabLabel()
	{
		return 'Step Details';
	}
	
	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return 'Step Details';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canShowTab()
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isHidden()
	{
		return false;
	}
	
	public function __($text){
		return $text;
	}

	
}