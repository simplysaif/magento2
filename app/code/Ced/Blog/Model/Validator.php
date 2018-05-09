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
namespace Ced\Blog\Model;

use Ced\Blog\Model\Blogcat as Category;

use Magento\Framework\App\RequestInterface;

class Validator
{

    /**
     * Validate product data
     * @param Product $product
     * @param RequestInterface $request
     * @param \Magento\Framework\DataObject $response
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */

    public function validate(Category $product, RequestInterface $request, \Magento\Framework\DataObject $response)
    {
        $result = $product->validate($request);
        if(is_array($result)) {
            $response->setError(true);
            $response->setMessage($result['url_key']);
        }
    }

}
