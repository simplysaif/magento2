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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\RequestToQuote\Block\Saved;
/**
 * Class Auctionlist
 * @package Ced\Auction\Block\Auctionlist
 */
class Quotes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\Auction\Model\Auctionproduct
     */
    protected $_gridFactory;

    /**
     * Auctionlist constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ced\Auction\Model\Auctionproduct $gridFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /*
         * Loading collection from ced_auction_productlist table
         */
    }

}
