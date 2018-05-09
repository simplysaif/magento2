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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Model;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\MessageInterface;

class EmailSender extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /*Add attachment while send email*/
	public function addAttachment($attachment,$mimetype,$filename)
    {
        if ($this->message->hasAttachments == true) {
            return;
        }
        $this->message->createAttachment(
            $attachment,
            $mimetype,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $filename
        );
        return $this;
    }

     /**
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     */
    public function setFrom($from)
    {   
        $this->message->clearRecipients();
        $result = $this->_senderResolver->resolve($from);
        $this->message->clearFrom();
        $this->message->setFrom($result['email'], $result['name']);
      
        return $this;
    }
    
    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $types = [
            TemplateTypesInterface::TYPE_TEXT => MessageInterface::TYPE_TEXT,
            TemplateTypesInterface::TYPE_HTML => MessageInterface::TYPE_HTML,
        ];
        $body = $template->processTemplate();
        $this->message->setMessageType($types[$template->getType()])
            ->setBody($body);
        $this->message->clearSubject();
        $this->message->setSubject($template->getSubject());

        return $this;
    }
}