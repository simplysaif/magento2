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
 * @package     Ced_CsMembership
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMembership\Block\Adminhtml\Form\Field;
class Category extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_defaultRenderer;
    protected $_actionRenderer;
    protected $_priceTypeRenderer;
    private $_arrayRowsCache;

    protected function _getDefaultRenderer()
    {

        if (!$this->_actionRenderer) {
            $this->_actionRenderer = $this->getLayout()->createBlock(
                    'Ced\CsMembership\Block\Adminhtml\Form\Field\Defaultcategory',
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_actionRenderer->setExtraParams('style="width:90px"');
        }
        return $this->_actionRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('category', array(
            'label' => __('Category'),
            'renderer' => $this->_getDefaultRenderer(),
        ));

        
        $this->addColumn('price', array(
            'label' => __('Price'),
            'style' => 'width:53px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add More');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        //Zend_Debug::dump($row, '_prepareArrayRow', true);
        $category = $this->escapeHtml($row->getCategory());
        $method = $this->escapeHtml($row->getMethod());
        $options = [];
        $options['option_' . $this->_getDefaultRenderer()->calcOptionHash($category)]
        = 'selected="selected"';
        $options['option_' . $this->_getDefaultRenderer()->calcOptionHash($method)]
        = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
        return;
    }

}
