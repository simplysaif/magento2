<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

use Magestore\Sociallogin\Model\Sociallogin;

class Twitterredirecturl
    extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $redirectUrl = $this->_getStore()->getUrl('sociallogin/twlogin/user', array('_secure' => true));
        $array = parse_url($redirectUrl);
 
        if (isset($array['query']) && $array['query']) {
            $redirectUrl = str_replace('?' . $array['query'], '', $redirectUrl);
        }

        $html = "<input style='width: 100%;' readonly id='sociallogin_twitterlogin_redirecturl' class='input-text' value='" . $redirectUrl . "'>";
        return $html;
    }

}
