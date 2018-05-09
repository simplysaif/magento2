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
 * @package     Ced_Vbadges
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Controller\Adminhtml\Badges;;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Ced\Vbadges\Model\ResourceModel\Badges\CollectionFactory;

class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $badgesIds = $collection->getAllIds();

        $status = (int) $this->getRequest()->getParam('status');
        
        try {
            $badgesupdated = 0;
            foreach ( $badgesIds as $badges) {
                $model = $this->_objectManager->get ( 'Ced\Vbadges\Model\Badges' )->load ($badges);
                $model->setData ( 'badge_status', $status );
                $model->save ();
                $badgesupdated++;
            }

            $this->messageManager->addSuccess ( __ ( 'Badges updated successsfully.' ) );
  
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }
        return $this->_redirect('*/badges/badgesview');
    }
}
