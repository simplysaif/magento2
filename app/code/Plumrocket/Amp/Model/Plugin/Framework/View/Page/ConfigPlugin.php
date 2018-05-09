<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\View\Page;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Asset\GroupedCollection;

use Plumrocket\Amp\Block\Page\Head\Og\AbstractOg as AbstractOg;

/**
 * Plugin for processing builtin cache
 */
class ConfigPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;


    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @return  void
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  string $result
     * @return string $result
     */
    public function afterGetIncludes(Config $subject, $result)
    {
        if (!$this->_dataHelper->isAmpRequest()){
            return $result;
        }

        return '';
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  \Magento\Framework\View\Asset\GroupedCollection $result
     * @return \Magento\Framework\View\Asset\GroupedCollection $result
     */
    public function afterGetAssetCollection(Config $subject, $result)
    {
        if (!$this->_dataHelper->isAmpRequest()){
            return $result;
        }

        foreach ($result->getGroups() as $group) {
            $type = $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE);
            if (!in_array($type, ['canonical', 'ico'])) {
                foreach ($group->getAll() as $identifier => $asset) {
                    $result->remove($identifier);
                }
            }

            if ($type == 'canonical') {
                $assetsCollection = $group->getAll();

                if (!count($assetsCollection)) {
                    $subject->addRemotePageAsset(
                        $this->_dataHelper->getCanonicalUrl(),
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                } else {
                    foreach ($assetsCollection as $identifier => $asset) {
                        if ($identifier != AbstractOg::DEFAULT_ASSET_NAME) {
                            $result->remove($identifier);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param  \Magento\Framework\View\Page\Config
     * @param  array $result
     * @param  string $elementType
     * @return array $result
     */
    public function aroundGetElementAttributes(Config $subject, \Closure $proceed, $elementType)
    {
        /**
         * Get result by original method
         */
        $result = $proceed($elementType);

        /**
         * Add attributes in tags by $elementType
         * (Only for amp request)
         */
        if ($this->_dataHelper->isAmpRequest()) {
            switch (strtolower($elementType)) {
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_HTML:
                    $result['amp'] = '';
                    break;
                case \Magento\Framework\View\Page\Config::ELEMENT_TYPE_BODY:
                    $result = array_diff_key($result, array_count_values(['itemtype', 'itemscope', 'itemprop']));
                    if ($this->_dataHelper->getRtlEnabled()) {
                        $rtlClass = ' dir-rtl';
                        if (!array_key_exists('class', $result)) {
                            $result['class'] = '';
                        }

                        $result['class'] = $result['class'] . $rtlClass;
                    }
                    break;
                default:
                    break;
            }

        }

        return $result;
    }

}
