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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Model\ResourceModel\Request;

/**
* Collection of Request
*/
class RequestCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection 
{
    protected $_request;

    protected $_objectManager;

	/**
	* Request Collection Resource Constructor
	* @return void
	*/
    protected function _construct()
    {
        $this->_init('Ced\CsRma\Model\Request', 'Ced\CsRma\Model\ResourceModel\Request');
    }

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
        $this->_request = $request;
        $this->_objectManager = $objectManager;
    }

    protected function _renderFiltersBefore()
    {
        $orderId = $this->_request->getParam('order_id',false);
        $url = $this->_objectManager->get('\Magento\Framework\App\Response\RedirectInterface')->getRefererUrl();
        $index = strpos($url,'/sales/order/view/order_id/');
        if($index !== false)
        {
            $baseUrl = $this->_objectManager->get('\Magento\Backend\Model\Url')->getBaseUrl();
            $request_uri = trim(substr($url,strlen($baseUrl)),'/');
            $request_uri = explode('/',$request_uri);
            $orderId = false;
            foreach ($request_uri as $key => $value) {
               if($value == 'order_id'){
                    $orderId = $request_uri[$key+1];
                    break;
               }
            }
            if($orderId)
            {
                $salesModel = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
                if($salesModel && $salesModel->getId())
                    $this->addFieldToFilter('order_id',$salesModel->getIncrementId());
            }
        }
    }
}