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
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\RequestToQuote\Model\System\Message;

class Alert implements \Magento\Framework\Notification\MessageInterface
{

/**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    protected $_objectManager;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_objectManager = $objectInterface;
    }
    /**
     *  Retrieve unique message identity
     */
    public function getIdentity()
    {
        
        return md5('RequestToQuote_Alert');
    }
    /**
     * Return true to show your message, false to hide it
     */ 
    public function isDisplayed()
    {
        
     	$pendingQuote = $this->_objectManager->create("Ced\RequestToQuote\Model\Quote")->getCollection()->addFieldToFilter('status',\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PENDING)->getData();
    	if(count($pendingQuote))
           return true;
        else
            return false;
    }
  /**
   *   Retrieve message text
   */
    public function getText()
    {
    	$pendingQuote = $this->_objectManager->create("Ced\RequestToQuote\Model\Quote")->getCollection()->addFieldToFilter('status',\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PENDING)->getSize();
    	$html = "";
    	$html.= $pendingQuote." Quote requests are Pending.".'<a href="'.$this->_urlBuilder->getUrl('requesttoquote/quotes/view').'">'.__(' View More').'</a>'.'<br>';
    	return $html;
    }
    
    /**
     * Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}