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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\Barcode\Block\System\Config\Form\Field;

/**
 * Backend system config array field renderer
 */
class Details extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_country;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
        
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('field', ['label' => __('Field'), 'width' => _('200')]);
        $this->addColumn('print', ['label' => __('Print')]);
        $this->addColumn('position', ['label' => __('position')]);
        $this->_addAfter = false;
      
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
     public function renderCellTemplate($columnName)
    {
        if ($columnName == 'print') {
         
            $options = $this->_objectManager->create('Magento\Config\Model\Config\Source\Yesno')->toOptionArray();
         	$element = $this->_elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }
        
        if ($columnName == 'field') {
        	
        	$options = $this->_objectManager->create('Ced\Barcode\Model\DescriptionOption')->toOptionArray();
        	$element = $this->_elementFactory->create('select');
        	$element->setForm(
        			$this->getForm()
        	)->setName(
        			$this->_getCellInputElementName($columnName)
        	)->setHtmlId(
        			$this->_getCellInputElementId('<%- _id %>', $columnName)
        	)->setValues(
        			$options
        	);
        	return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    } 
}
