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
 
namespace Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit;
 
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
 
class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('requested_quote_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Edit the Quote'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'quote_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab\Main'
                )->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'vendor_front',
            [
                'label' => __('Quoted Items'),
                'title' => __('Quoted Items'),
                'content' => $this->getLayout()->createBlock('Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab\QuoteDetails')->toHtml().$this->getLayout()
                ->createBlock('Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab\QuoteDetails')->setTemplate('Ced_RequestToQuote::quote/items.phtml')
                ->toHtml(),
            ]
        );
        
        $this->addTab(
            'chat_message',
            [
                'label' => __('Chat Section'),
                'title' => __('Chat Section'),
                'content' => $this->getLayout()->createBlock('Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab\Message')->toHtml().$this->getLayout()
                ->createBlock('Ced\RequestToQuote\Block\Adminhtml\Quotes\Edit\Tab\Message')->setTemplate('Ced_RequestToQuote::quote/message.phtml')
                ->toHtml(),
            ]
        );

        

        return parent::_beforeToHtml();
    }
}