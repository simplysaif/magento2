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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsTableRateShipping\Controller\Rates;

class Delete extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) {
            return; 
        }
        $id = $this->getRequest()->getParam('id');
        try 
        {
            $model = $this->_objectManager->get('Ced\CsTableRateShipping\Model\Tablerate')->load($id);
            $model->delete();
            $this->messageManager->addSuccessMessage(__('One Rate has been deleted.'));
            $this->_redirect('*/*/index');
        }
        catch(\Exception $e)
        {
          echo $e->getMessage();
          $this->_redirect('*/*/index');
          return;
        }
               
    }
}
