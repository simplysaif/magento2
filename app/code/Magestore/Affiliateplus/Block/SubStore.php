<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Affiliateplus\Block;

/**
 * @category Magestore
 * @package  Magestore_Affiliateplus
 * @module   Affiliateplus
 * @author   Magestore Developer
 */
class SubStore extends AbstractTemplate
{

    /**
     * @return \Magestore\Affiliateplus\Model\Session
     */
    protected function _getSession(){
        return $this->_sessionModel;
    }

    /**
     * Get Action Model
     * @return \Magestore\Affiliateplus\Model\Action
     */
    protected function _getActionModel(){
        return $this->_objectManager->create('Magestore\Affiliateplus\Model\Action');
    }

    /**
     * @var \Magestore\Affiliateplus\Helper\urlHelper
     */
    protected $_urlHelper;
 
    /**
     * @return mixed
     */
    protected function _getHelperUrl(){
        if(!$this->_urlHelper){
            $this->_urlHelper = $this->_objectManager->create('Magestore\Affiliateplus\Helper\Url');
        }
        return $this->_urlHelper;
    }

    /**
     *
     * @return \Magestore_Affiliateplus_Block_Referrer
     */
    public function _prepareLayout(){
        parent::_prepareLayout();
        return $this;
    }
    /**
     * @return mixed
     */
    public function getAccount(){
        return $this->_getSession()->getAccount();
    }


    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    public function setPrice($stringPrice){
        $price = (float) filter_var( $stringPrice, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        return $this->formatPrice($price);
    }


    /**
     * @author Gin (19/04/2017)
     * @return mixed
     */
    public function getAffiliateUrl(){
        $homeUrl = $this->getBaseUrl();
        return $this->_getHelperUrl()->addAccToSubstore($homeUrl);
    }
}
