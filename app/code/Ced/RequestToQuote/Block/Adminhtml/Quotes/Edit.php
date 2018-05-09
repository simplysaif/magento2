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

namespace Ced\RequestToQuote\Block\Adminhtml\Quotes;
 
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
 
class Edit extends Container
{
   /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Ced\RequestToQuote\Model\Quote $quote,
        array $data = []
    ) {
    	$this->quote = $quote;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
    	$quote_id = $this->getRequest()->getParam('id');
    	$status = $this->quote->load($quote_id)->getStatus();


        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_quotes';
        $this->_blockGroup = 'Ced_RequestToQuote';
 
        parent::_construct();
        
	    $this->buttonList->add(
	        'saveandcontinue',
	        [
	            'label' => __('Save the Quote'),
	            'class' => 'save primary',
	            'data_attribute' => [
	                'mage-init' => [
	                    'button' => [
	                        'event' => 'saveAndContinueEdit',
	                        'target' => '#edit_form'
	                    ]
	                ],

	            ]
	        ],
	        -100
	    );
	        
	     if($status == '3' || $status == '1' || $status == '6'){

	        	$this->buttonList->update('save', 'label', __('Create PO'));
	        }

	    else{
	    	$this->buttonList->remove('save');
	    }
        $this->buttonList->update('delete', 'label', __('Delete'));
    }
 
   /**
	 * Retrieve text for header element depending on loaded post
	 *
	 * @return \Magento\Framework\Phrase
	 */
    public function getHeaderText()
    {
        $params = $this->requestInterface->getParams();
        if(isset($params))
        {
			return __("Edit Quote '%1'", $this->escapeHtml ($this->requestInterface->getParam('id')));
		}
	}
}