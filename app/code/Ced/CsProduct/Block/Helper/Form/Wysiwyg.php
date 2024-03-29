<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog textarea attribute WYSIWYG button
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Ced\CsProduct\Block\Helper\Form;

class Wysiwyg extends \Magento\Framework\Data\Form\Element\Textarea
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * Catalog data
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager = null;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    protected $_httpRequest;
    protected $_objectManager;

    /**
     * Wysiwyg constructor.
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Helper\Data $backendData,
    		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_layout = $layout;
        $this->_moduleManager = $moduleManager;
        $this->_backendData = $backendData;
        $this->_objectManager = $objectManager;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
    	$this->_httpRequest = $this->_objectManager->get('Magento\Framework\App\Request\Http');
    	$module_name = $this->_httpRequest->getModuleName();
    	$url = $this->_backendData->getUrl('catalog/product/wysiwyg');
    	if($module_name == 'csproduct'){
    		$url = $this->_objectManager->get('Magento\Framework\UrlInterface')->getUrl('csproduct/vproducts/wysiwyg');
    	}
    	 
        $config = $this->_wysiwygConfig->getConfig();
        $config = json_encode($config->getData());

        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $disabled = $this->getDisabled() || $this->getReadonly();
            $html .= $this->_layout->createBlock(
                'Magento\Backend\Block\Widget\Button',
                '',
                [
                    'data' => [
                        'label' => __('WYSIWYG Editor'),
                        'type' => 'button',
                        'disabled' => $disabled,
                        'class' => 'action-wysiwyg',
                         'onclick' => 'catalogWysiwygEditor.open(\'' . $url . '\', \'' . $this->getHtmlId() . '\')',
                    ]
                ]
            )->toHtml();
            $html .= <<<HTML
<script>
require([
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function(jQuery){

var config = $config,
    editor;

jQuery.extend(config, {
    settings: {
        theme_advanced_buttons1 : 'bold,italic,|,justifyleft,justifycenter,justifyright,|,' +
            'fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
        theme_advanced_buttons2: null,
        theme_advanced_buttons3: null,
        theme_advanced_buttons4: null,
        theme_advanced_statusbar_location: null
    },
    files_browser_window_url: false
});

editor = new tinyMceWysiwygSetup(
    '{$this->getHtmlId()}',
    config
);

editor.turnOn();

jQuery('#{$this->getHtmlId()}')
    .addClass('wysiwyg-editor')
    .data(
        'wysiwygEditor',
        editor
    );
});
</script>
HTML;
        }
        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsWysiwygEnabled()
    {
        if ($this->_moduleManager->isEnabled('Magento_Cms')) {
            return (bool)($this->_wysiwygConfig->isEnabled() && $this->getEntityAttribute()->getIsWysiwygEnabled());
        }

        return false;
    }
}
