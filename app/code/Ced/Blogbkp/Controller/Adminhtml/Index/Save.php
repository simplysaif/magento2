<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Blog
 * @author   	CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Ced\Blog\Setup;
use Magento\Framework\Setup\InstallDataInterface;
use \Magento\Framework\Exception\AlreadyExistsException;

class Save extends \Magento\Backend\App\Action
{
	/**
	 * @var execute
	 */
	public function execute()
    {
    	$data = $this->getRequest()->getParams();
        if ($data)
        {	//edit section
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$postData = $this->getRequest()->getPost();
			if($postData['attribute_id'])
			{
				$model = $this->_objectManager->create('Magento\Customer\Model\Attribute')->load($postData['attribute_id']);
				$model->setData('attribute_code',$postData['attribute_code']);
				$model->setData('frontend_label',$postData['frontend_label']);
				$model->setData('backend_type',$postData['backend_type']);
				$model->setData('note',$postData['visibility']);
				
				if(isset($options))
				$model->setData('frontend_class',json_encode($options));
				
				$model->save();
				$this->_redirect('blog/index/index');
				$this->messageManager->addSuccess(__('Updated Successfully'));
			}
			else 
			{	//new save
				$model = $this->_objectManager->create('Ced\Blog\Setup\BlogSetup');
				if(isset($options))
				{
				$model->addAttribute(
						'blog',
						$data['attribute_code'],
						[
						'label'            => $data['frontend_label'],
						'type'				=> $data['backend_type'],
						'required'         => false,
						'visible_on_front' => true,
						'note'				=>$postData['visibility'],						
						'frontend_class'		=> 	json_encode($options)
    				]
				);
				}
				else
				{
					$model->addAttribute(
							'blog',
							$data['attribute_code'],
							[
							'label'            => $data['frontend_label'],
							'type'				=> $data['backend_type'],
							'required'         => false,
							'visible_on_front' => true,
							'note'				=>$postData['visibility'],
							//'frontend_class'		=> 	json_encode($options)
							]
					);
				}		
				$this->_redirect('blog/index/index');
				$this->messageManager->addSuccess(__('Attribute Added Successfully'));
				
			}
        
      }
	}
}