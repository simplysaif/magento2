<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class Yahooredirecturl
    extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $redirectUrl = str_replace('https://', 'http://', $this->_getStore()->getUrl(''));
        $domain = parse_url($redirectUrl);
        $referer = isset($domain['host']) ? $domain['scheme'] . '://' . $domain['host'] . $domain['path'] : $redirectUrl;
        $html = "<input style='width: 100%;'  readonly id='sociallogin_yahoologin_redirecturl' class='input-text' value='" . $referer . "'>";
        return $html;
    }

}
