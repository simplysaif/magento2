<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class Amazonredirecturl extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $redirectUrl = $this->_getStore()->getUrl('', array('_secure' => true));
        $domain = parse_url($redirectUrl);
        $referer = isset($domain['host']) ? $domain['scheme'] . '://' . $domain['host'] : $redirectUrl;

        $html = "<input style='width: 100%;'  readonly id='sociallogin_amlogin_redirecturl' class='input-text' value='" . $referer . "'>";
        return $html;
    }

}