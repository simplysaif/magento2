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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Framework\Controller;

use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Plugin for changing result before caching
 */
class ResultInterfacePlugin
{
    /**
     * Response object
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\ResponseInterface $httpResponse,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->response = $httpResponse;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Add amp parameter for each url
     * @param  \Magento\Framework\UrlInterface $subject
     * @param  string
     * @return string
     */
    public function beforeRenderResult(
        \Magento\Framework\Controller\ResultInterface $subject,
        ResponseHttp $response
    ) {
        if ($this->_dataHelper->isAmpRequest()) {
            /**
             * Get layout
             */
            $layout = $subject->getLayout();

            if ($layout && $ampJsBlock = $layout->getBlock('ampjs')) {
                $output = $layout->getOutput();

                /**
                 * Removing unnecessary elements
                 */
                foreach ($ampJsBlock->getAmpScripts() as $elementName) {
                    if ($elementName == 'amp-form') {
                        continue;
                    }
                    
                    if ($elementName == 'amp-youtube' && strpos($output, 'youtube.com/embed') !== false) {
                        continue;
                    }

                    if (strpos($output, $elementName) === false) {
                        $ampJsBlock->removeJs($elementName);
                    }
                }

                /**
                 * Removing elements of amp-form
                 */
                if (strpos($output, '<form') === false) {
                    $ampJsBlock->removeJs('amp-form');
                }
            }
        }

        return null;
    }

    /**
     * @param \Magento\Framework\Controller\ResultInterface $subject
     * @param callable $proceed
     * @param ResponseHttp $response
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRenderResult(
        \Magento\Framework\Controller\ResultInterface $subject,
        \Closure $proceed,
        ResponseHttp $response
    ) {
        $result = $proceed($response);

        if ($this->_dataHelper->isAmpRequest()) {
            /* remove bad html */
            $html = $response->getBody();
            $html = $this->_replaceHtml($html);
            $response->setBody($html);
        }

        return $result;
    }


    /**
     * Replaced disallowed code on page
     * @param  string $html
     * @return string
     */
    protected function _replaceHtml($html)
    {
        $html = str_ireplace(
            array('<video','/video>','<audio','/audio>','<ui','/ui>'),
            array('<amp-video','/amp-video>','<amp-audio','/amp-audio>','<ul','/ul>'),
            $html
        );

        $html = preg_replace(
            '/<iframe.+?youtube\.com\/embed\/(.*?)(?:\?|").+?<\/iframe>/s',
            '<amp-youtube data-videoid="$1" layout="responsive" width="480" height="270"></amp-youtube>',
            $html);

        $html = preg_replace(
            '/\s+(?:style|align|hspace|vspace|itemprop|itemscope|itemtype|dataurl|onclick|border|vocab|typeof|container|usemap|cellpadding|cellspacing|nowrap)\s*=\s*(?:"[^"]*"|\'[^\']*\')/i',
            '',
            $html); // do not remove "content", "id", "property", "title"

        //Remove collspan and rowspan from all tags, except <td>
        $html = preg_replace(
            array(
                '#(\<(?!td\s|th\s|\/td\s|\/th\s))([^<]*)(?:rowspan="\d+")(.*>)#isU',
                '#(\<(?!td\s|th\s|\/td\s|\/th\s))([^<]*)(?:colspan="\d+")(.*>)#isU',
            ),
            '$1$2 $3',
            $html
        );

        $html = preg_replace(
            '/\s+target(?:\s*=\s*"\s*"|\s*=\s*\'\s*\'|\s*(?!=))/i',
            '',
            $html);

        $html = preg_replace(
            '/(<span[^>]+)(content|property)=(?:"[^"]*"|\'[^\']*\')/',
            '$1',
            $html);

        $html = preg_replace('/<font.*?>(.*?)<\/font>/', '$1', $html);
        $html = preg_replace('/<link[^>]+"http:\/\/purl.org[^"]*"[^\/]*\/>/', '', $html);

        $html =  str_replace(
            array('<link  href="In stock">'),
            array(''),
            $html
        );

        $html = preg_replace('#"(javascript:\s*[^"]*)"#isU', '#nohref', $html);

        $html = preg_replace(
            array(
                '#<script((?!ampproject|application\/ld\+json|application\/json).)*>.*</script>#isU',
                '#<style((?!amp-).)*>.*<\/style>#isU',
                //'#<form.*>.*<\/form>#isU', //need to be for search
                '#<map.*>.*<\/map>#isU',
                '#<link\s+href="https?:\/\/schema\.org\/[a-zA-Z0-9_\-\/\?\&]*"\s?\/?>#isU',
                '#(?:<col\s+[^>]*(width=(?:"[^"]*"|\'[^\']*\'))[^>]*>)#isU'
            ),
            '', $html);

        $html = preg_replace(
            array(
                '#(<a\s+[^>]*)(alt=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(type=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(property=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(width=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<a\s+[^>]*)(height=(?:"[^"]*"|\'[^\']*\'))([^>]*>)#isU',
                '#(<script\s+[^>]*)(defer\s+)([^>]*>)#isU',
                '#(<select.*?)(onchange=".*?")(.*?>)#is', //not need modificator U
            ),
            '$1$3', $html);

        /* Old replace */
        /*
        $html = preg_replace('/<img(.*?)\/?>/', '<amp-img$1></amp-img>', $html);
        $html = preg_replace('#<amp-img((?(?!width).)*)>\s*</amp-img>#isU', '<amp-img$1 height="100" width="290" ></amp-img>',$html);
        */

        /* Replace width value with data-width-amp value */
        $html = preg_replace('#(<img\s+[^>]*)(?:width=(?:"\w+"|\'\w+\'))([^>]*)(?:data-width-amp="(\w+)")([^>]*>)#isU', '$1 width="$3" $2 $4', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:height=(?:"\w+"|\'\w+\'))([^>]*)(?:data-height-amp="(\w+)")([^>]*>)#isU', '$1 height="$3" $2 $4', $html);

        /* replace data-width-amp with width */
        $html = preg_replace('#(<img\s+[^>]*)(?:data-width-amp="(\w+)")([^>]*\/?>)#isU', '$1 width="$2" $3', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:data-height-amp="(\w+)")([^>]*\/?>)#isU', '$1 height="$2" $3', $html);

        /* Add height & width if not exists */
        $html = preg_replace('#(?:<img\s+)((?(?!height=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img height="100" $1 />', $html);
        $html = preg_replace('#(?:<img\s+)((?(?!width=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img width="290" $1 />', $html);

        $html = preg_replace('#<img\s+([^>]*)(?:data-src="([^"]*)")([^>]*)\/?>#isU', '<img src="$2" $1 $3/>', $html);

        /* Replace img to amp-img */
        $html = preg_replace('#(?:<img\s+)(.*?)(?:\/>|>)#is', '<amp-img $1></amp-img>', $html);

        /* Replace iframe to amp-iframe */
        $additionalAttributes = ' layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"';
        $placeholder = '<div class="amp-iframe-placeholder" placeholder><span>Loading</span></div>';

        $subPattern = '(\s+(?:src|width|height)=["\'](?:[^"\']*)["\'])';
        $pattern = '#(?:<iframe\s+)'
            . $subPattern
            . '.*'
            . $subPattern
            . '.*'
            . $subPattern
            . '(?:>\s*<\/iframe>)#isU';

        $replacement = '<amp-iframe  $1 $2 $3 '
            . $additionalAttributes
            . '>'
            . $placeholder
            . '</amp-iframe>';

        $html = preg_replace($pattern, $replacement, $html);

        return $html;
    }

}