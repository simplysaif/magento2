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
namespace Ced\Vbadges\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Addtab implements ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$block = $observer->getEvent()->getTabs();
		/* $tab->addTab ( 'vbadge', array (
				'label' => __ ( 'Vendor Badges' ),
				'content' => $tab->getLayout ()->createBlock ( 'Ced\Vbadges\Block\Adminhtml\Vendor\Entity\Edit\Tab\Main' )->toHtml () 
		) ); */
		$block->addTab ( 
		 	'images', 
		 	[
				'label' => __ ( 'Vendor Badges Images' ),
               	'title'     => __('Vendor Badges Images'),
				'content' => $block->getLayout ()->createBlock ( 'Ced\Vbadges\Block\Adminhtml\Images' )->toHtml ()
			]
		);
	}
}