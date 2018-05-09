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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

 
namespace Ced\CsMarketplace\Block\Vshops\ListBlock;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    public function getPagerHtml()
	{
		$pagerBlock = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');

		if ($pagerBlock instanceof DataObject) {

			/* @var $pagerBlock Mage_Page_Block_Html_Pager */
			$pagerBlock->setAvailableLimit($this->getAvailableLimit());

			$pagerBlock->setUseContainer(false)
			->setShowPerPage(false)
			->setShowAmounts(false)
			->setCurPage(3)
			->setLimitVarName($this->getLimitVarName())
			->setPageVarName($this->getPageVarName())
			->setLimit($this->getLimit())
			->setCollection($this->getCollection());
			return $pagerBlock->toHtml();
		}
		return '';
	}
}
