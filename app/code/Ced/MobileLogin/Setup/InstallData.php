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
 * @package     Ced_MobileLogin
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\MobileLogin\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

	private $_eavSetupFactory;
    private $_attributeRepository;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\AttributeRepository $attributeRepository
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_attributeRepository = $attributeRepository;
    }

	/**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install( ModuleDataSetupInterface $setup, ModuleContextInterface $context )
    {

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        // add customer_attribute to customer
        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'mobile');
        $eavSetup->addAttribute(
        \Magento\Customer\Model\Customer::ENTITY, 'mobile', [
            'type' => 'varchar',
            'label' => 'Mobile',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'system' => 0,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'sort_order' => 3,
            'position' => 3,
        ]
    );

    // allow customer_attribute attribute to be saved in the specific areas
    $attribute = $this->_attributeRepository->get('customer', 'mobile');
    $setup->getConnection()
    ->insertOnDuplicate(
        $setup->getTable('customer_form_attribute'),
        [
            ['form_code' => 'adminhtml_customer', 'attribute_id' => $attribute->getId()],
            ['form_code' => 'customer_account_create', 'attribute_id' => $attribute->getId()],
            ['form_code' => 'customer_account_edit', 'attribute_id' => $attribute->getId()],
        ]
    );
    }
}