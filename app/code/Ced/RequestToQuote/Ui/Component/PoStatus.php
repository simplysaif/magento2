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
 * @category  Ced
 * @package   Ced_RequestToQuote
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\RequestToQuote\Ui\Component;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\UrlInterface;

class PoStatus extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria,UrlInterface $urlBuilder, array $components = [], array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                /*print_r($item['status']);die("dsgfweh");*/
                if (isset($item['status'])) {
                    
                    switch ($item['status']) {

                        case '0':
                            $item[$this->getData('name')] = 'Pending';
                            break;
                        case '1':
                            $item[$this->getData('name')] = 'Confirmed';
                            break;
                        case '2':
                            $item[$this->getData('name')] ='Declined';
                            break;
                        case '3':
                            $item[$this->getData('name')] = 'Ordered';
                            break;
                        
                       /* default:
                            $item[$this->getData('name')] = 'Pending';
                            break;*/
                    }
                }
            }
        }
        return $dataSource;
    }
}