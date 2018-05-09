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
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\Blog\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**

 * @codeCoverageIgnore
 */

class InstallData implements InstallDataInterface
{

    /**
     * CsMarketplace setup factory
     * @var CsMarketplaceSetupFactory
     */

    private $blogSetupFactory;

    /**
     * Init
     * @param CsMarketplaceSetupFactory $csmarketplaceSetupFactory
     */

    public function __construct(BlogSetupFactory $blogSetupFactory)
    {
        $this->blogSetupFactory = $blogSetupFactory;

    }


    /**
     * {@inheritdoc}
     */

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $blogSetup = $this->blogSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();
        $blogSetup->installEntities();
        $setup->endSetup();

    }

}

