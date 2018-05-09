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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Ced\CsMarketplace\Setup\CsMarketplaceSetupFactory;
use Ced\CsMultistepreg\Model\Steps;
use Ced\CsMultistepreg\Model\StepsFactory;
use Ced\CsMarketplace\Model\Vendor\Attribute;
use \Magento\Framework\App\State;


class InstallData implements InstallDataInterface{
	
	private $csmarketplaceSetupFactory;
	private $stepsFactory;
	public $_objectManager;
	private $state;

	/**
	 * Init
	 *
	 * @param CsMarketplaceSetupFactory $csmarketplaceSetupFactory
	 */
	public function __construct(CsMarketplaceSetupFactory $csmarketplaceSetupFactory,\Magento\Framework\ObjectManagerInterface $objectManager,StepsFactory $stepsFactory, State $state)
	{
		$this->csmarketplaceSetupFactory = $csmarketplaceSetupFactory;
		$this->_objectManager= $objectManager;
		$this->stepsFactory = $stepsFactory;
		$this->state = $state;
		$state->setAreaCode('frontend');
	}
	
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
	
		/**
		 *
		 *
		 * @var CsMarketplaceSetup $csmarketplaceSetup
		 */
		$csmarketplaceSetup = $this->csmarketplaceSetupFactory->create(['setup' => $setup]);
		$setup->startSetup();
		$csmarketplaceSetup->installEntities();
		$csmarketplaceSetup->addAttribute('csmarketplace_vendor', 'multistep_done', array(
				'group'			=> 'General Information',
				'visible'      	=> false,
				'position'      => 6,
				'type'          => 'int',
				'source' 		=> 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'label'         => 'Multistep Done',
				'input'         => 'text',
				'required'      => false,
				'default'       => 0,
				'user_defined'  => false,
		));
		$tableName = $setup->getTable('ced_csmarketplace_vendor_form_attribute');
		$connection = $setup->getConnection();
			$connection
					->addColumn(
					$tableName,
					'registration_step_no',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					array('unsigned' => true, 'nullable' => false, 'default' => ''),
					'Registration Step Number'
			);


		/* inserting steps info */
		$steps = array('Personal Info','Business Info');
		$stepCount = 1;
		foreach ($steps as $key=>$value) {
			$this->stepsFactory
				 ->create()
				 ->setStepNumber($stepCount++)
				 ->setStepLabel($value)
				 ->save();
		}

		
		$vendorTypeId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getEntityTypeId();

		$stepOne = ['name','email','gender'];
		$stepTwo = ['shop_url','company_name','contact_number'];
		
		$stepAttributeCodes = ['name','email','shop_url','company_name','contact_number'];
		$attributeCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')->getCollection();
		$attributeCollection->addFilterToMap('attribute_code','vform.attribute_code');
		$attributeCollection->addFieldToFilter('attribute_code',array('in'=>$stepAttributeCodes));
		foreach ($attributeCollection as $attribute) {
			

			$stepAttribute = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')->load($attribute->getAttributeId());
			try{
				if(in_array($attribute->getAttributeCode(),$stepOne)){
					$stepAttribute->setStoreId(0)->setRegistrationStepNo(1)->save();
				}elseif(in_array($attribute->getAttributeCode(),$stepTwo )){
					$stepAttribute->setStoreId(0)->setRegistrationStepNo(2)->save();
				}

			}catch(Exception $e){
				echo $e->getMessage();
			}
		}
	}
}