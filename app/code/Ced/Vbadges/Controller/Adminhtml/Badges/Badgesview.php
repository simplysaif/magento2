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
 * @package     Ced_Vbadges
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Controller\Adminhtml\Badges;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Badgesview extends \Magento\Backend\App\Action {
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		parent::__construct ( $context );
		$this->resultPageFactory = $resultPageFactory;
	}
	/**
	 * Default customer account page
	 *
	 * @return void
	 */
	public function execute() {
		$resultPage = $this->resultPageFactory->create ();
		$resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Order Based Badges'));
        return $resultPage;
    }
}