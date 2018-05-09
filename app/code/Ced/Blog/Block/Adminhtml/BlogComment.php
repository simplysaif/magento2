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
 * @package   Ced_Blog
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Blog\Block\Adminhtml;

class BlogComment extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'comment/view.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_getAddButtonOptions();
    }

    /**
     * Prepare button and gridCreate Grid , edit/add grid row and installer in Magento2
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\Blog\Block\Adminhtml\BlogComment\Grid', 'grid.view.grid')
        );

        return parent::_prepareLayout();
    }

    protected function _getAddButtonOptions()
    {
        $splitButtonOptions = [
            'label' => __(' New Comment '),
            'class' => 'primary',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
        $this->buttonList->add('add', $splitButtonOptions);

    }

    /**
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            'blog/*/new'
        );
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
