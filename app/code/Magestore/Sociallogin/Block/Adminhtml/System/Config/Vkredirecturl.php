<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class Vkredirecturl
    extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $redirectUrl = $this->_getStore()->getUrl('', array('_secure' => true));
        $domain = parse_url($redirectUrl);
        $referer = isset($domain['host']) ? $domain['host'] . $domain['path'] : $redirectUrl;
        $html = "<input style='width: 100%;'  readonly id='sociallogin_vklogin_redirecturl' class='input-text' value='" . $referer . "'>";
        return $html;
    }

}
