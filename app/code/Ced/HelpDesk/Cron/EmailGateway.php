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
namespace Ced\HelpDesk\Cron;

class EmailGateway
{
    public $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $ob)
    {
        $this->_objectManager = $ob;
    }
    /*
     * Create Ticket By Fetch email from email gateway
     * */
    public function createTicket()
    {
        if (!($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/enable_gateway')))
              return;
        $protocol = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/protocol');
        $gateway =  $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/email_gateway');
        $port = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/port');
        $loginId = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/login_id');
        $password = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/gateway/password');
        $date= $this->_objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime')->date();
        if ($protocol == 'imap'){
            $imapPath = '{imap.'.$gateway.'.com:'.$port.'/'.$protocol.'/ssl/novalidate-cert/norsh}INBOX';
        }elseif ($protocol){
            $imapPath = '{pop.'.$gateway.'.com:'.$port.'/'.$protocol.'/ssl/novalidate-cert/norsh}INBOX';
        }
        $inbox = imap_open($imapPath,$loginId,$password) or die('Cannot connect to '.$gateway.': ' . imap_last_error());
        $emails = imap_search($inbox,'UNSEEN');
        $adminModel = $this->_objectManager->create('Magento\User\Model\User')->load(1);
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $adminId = $adminModel->getUserId();
        $adminName = $adminModel->getUsername();
        if (!empty($emails)) {
            foreach ($emails as $mail){
                $headerInfo = json_decode(json_encode(imap_headerinfo($inbox,$mail)),true);
                $message = quoted_printable_decode(imap_fetchbody($inbox,$mail,1));
                $customer_name = $headerInfo['from'][0]['personal'];
                $mailbox = $headerInfo['from'][0]['mailbox'];
                $host = $headerInfo['from'][0]['host'];
                $customer_email = $mailbox.'@'.$host;
                $subject = $headerInfo['subject'];
                $senderMailBox = $headerInfo['sender'][0]['mailbox'];
                $customer_id = $customerModel->getCollection()->addFieldToFilter('email',$customer_email)->getFirstItem()->getId();
                if (!empty($customer_id)){
                    $customerId = $customer_id;
                }else{
                    $customerId = 'guest';
                }
                $ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket');
                $ticketCount = $ticketModel->getCollection()->count();
                if ($ticketCount > 0){
                    $ticketId = $ticketModel->getCollection()->getLastItem()->getTicketId();
                    $ticketId = $ticketId+1;
                }else{
                    $ticketId = 100000001;
                }
                $replyData = explode(':',$subject);
                if(!empty($replyData)){
                    foreach ($replyData as $key => $val){
                        if(strpos($val,'Ticket'))
                        {   $ticketIdIndex = $key+1;
                            break;
                        }else{
                            $ticketIdIndex = null;
                        }
                    }
                }
                $structure = imap_fetchstructure($inbox, $mail);
                $attachments = [];
                if(isset($structure->parts) && count($structure->parts))
                {
                    for($i = 0; $i < count($structure->parts); $i++)
                    {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );
                        if($structure->parts[$i]->ifdparameters)
                        {
                            foreach($structure->parts[$i]->dparameters as $object)
                            {
                                if(strtolower($object->attribute) == 'filename')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                    if(empty($customer_Id))
                                    {
                                        $customer_Id = 'guest';
                                    }

                                }
                            }
                        }
                        if($structure->parts[$i]->ifparameters)
                        {
                            foreach($structure->parts[$i]->parameters as $object)
                            {
                                if(strtolower($object->attribute) == 'name')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                    if(empty($customer_Id))
                                    {
                                        $customer_Id = 'guest';
                                    }
                                }
                            }
                        }
                        if($attachments[$i]['is_attachment'])
                        {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $mail, $i+1);
                            if($structure->parts[$i]->encoding == 3)
                            {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                if(empty($customer_Id))
                                {
                                    $customer_Id = 'guest';
                                }
                            }
                            elseif($structure->parts[$i]->encoding == 4)
                            {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                if(empty($customer_Id))
                                {
                                    $customer_Id = 'guest';
                                }
                            }
                        }
                    }
                }
                $files = [];
                foreach($attachments as $attachment)
                {
                    if($attachment['is_attachment'] == 1 )
                    {
                        if(empty($filename))
                            $filename = $attachment['filename'];
                        $filesystem = $this->_objectManager
                            ->create('\Magento\Framework\Filesystem');
                        $id = $ticketId;
                        $path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $abs_path = $path->getAbsolutePath('images/helpdesk/'.$customerId.'/'.$id.'/'.$date.'/');
                        $io = $this->_objectManager->create('\Magento\Framework\Filesystem\Io\File');
                        $io->mkdir($abs_path, 0777);
                        $filename = $attachment['name'];
                        $files[] = $date.'/'.$filename;
                        if(empty($filename)) $filename = $attachment['filename'];
                        if(empty($filename)) $filename = time() . ".dat";
                        $fp = fopen($abs_path. "/" . $filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                    }
                }
                if (!empty($senderMailBox) && !strpos($senderMailBox,'noreply') && !($replyData[0] == 'Re')){
                    $ticketModel->setTicketId($ticketId)
                        ->setMessage($message)
                        ->setDepartment('admin')
                        ->setAgent($adminId)
                        ->setAgentName($adminName)
                        ->setSubject($subject)
                        ->setOrder('N/A')
                        ->setCustomerId($customerId)
                        ->setCustomerName($customer_name)
                        ->setCustomerEmail($customer_email)
                        ->setPriority('Normal')
                        ->setStoreView(1)
                        ->setNumMsg(1)
                        ->setStatus('New')
                        ->setCreatedTime($date);
                    $ticketModel->save();
                    $messageModel = $this->_objectManager->create('Ced\HelpDesk\Model\Message');
                    $messageModel->setMessage($message)
                        ->setFrom($customer_name)
                        ->setTo($adminName)
                        ->setTicketId($ticketId)
                        ->setCreated($date)
                        ->setType('reply');
                    if (!empty($files) && is_array($files)){
                        $messageModel->setAttachment(implode(',',$files));
                    }
                    $messageModel->save();
                }
                if ($replyData[0] == 'Re' && !empty($ticketIdIndex)){
                    $messageModel = $this->_objectManager->create('Ced\HelpDesk\Model\Message');
                    $messageModel->setMessage($message)
                        ->setFrom($customer_name)
                        ->setTo($headerInfo['reply_to'][0]['personal'])
                        ->setTicketId($replyData[$ticketIdIndex])
                        ->setCreated($date)
                        ->setType('reply');
                    if (!empty($files) && is_array($files)){
                        $messageModel->setAttachment(implode(',',$files));
                    }
                    $messageModel->save();
                }
            }
        }
        imap_expunge($inbox);
        imap_close($inbox);
    }
}