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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Block\Adminhtml\AllRma;

class Chat extends \Magento\Backend\Block\Template
{
    /**
     * @var \Ced\CsRma\Model\RmachatFactory
     */

    public $rmaChatFactory;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */

    public $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    private $adminHelper;

    /**
     * @param \Ced\CsRma\Model\RmachatFactory $rmaChatFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsRma\Model\RmachatFactory $rmaChatFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->rmaChatFactory = $rmaChatFactory;
        $this->_coreRegistry = $registry;
        $this->adminHelper = $adminHelper;
        $this->setTemplate('edit/chat.phtml');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'admin_rma_history',
            'Ced\CsRma\Block\Adminhtml\AllRma\History'
        );

        $this->addChild(
            'admin_rma_notification',
            'Ced\CsRma\Block\Adminhtml\AllRma\Notification'
        );
        return parent::_prepareLayout();
    }

    /**
     * Get submit url
     *
     * @return string|true
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('csrma/allrma/chat',['id'=>$this->getRequest()->getParam('id')]);
    }

    
    /**
     * Replace links in string
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }
}
