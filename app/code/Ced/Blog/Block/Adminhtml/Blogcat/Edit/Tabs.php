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
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Blog\Block\Adminhtml\Blogcat\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('id');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));

    }
    /*  */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {

        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->_objectManager = $objectManager;
    }
    protected function _beforeToHtml()
    {
        $this->addTab(
            'SEO', array(
                'label'     => __('Search Engine Optimization'),
                'title'     => __('Search Engine Optimization'),
                'content'   => $this->getLayout()->createBlock('Ced\Blog\Block\Adminhtml\Blogcat\Edit\Tab\Settings')->toHtml(),
            )
        );
        $this->addTab(
            'related_post', array(
                'label'     => __('Related Post'),
                'url' => $this->getUrl('blog/category/postGrid', ['_current' => true]),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }
}
