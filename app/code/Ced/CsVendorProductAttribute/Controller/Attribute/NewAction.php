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
  * @package   Ced_CsVendorProductAttribute
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsVendorProductAttribute\Controller\Attribute;



class NewAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * NewAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
      public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Registry');
        if(!$registry->registry('vendorPanel'))
            $registry->register('vendorPanel', 1);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
