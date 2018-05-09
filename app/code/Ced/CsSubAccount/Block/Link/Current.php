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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Link;
use Magento\Customer\Model\Session;
/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|bool                       getCurrent()
 * @method \Magento\Framework\View\Element\Html\Link\Current setCurrent(bool $value)
 */
class Current extends \Ced\CsMarketplace\Block\Link\Current
{
    /**
     * Default path
     *
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    protected $_defaultPath;

    protected $_session;

    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_defaultPath = $defaultPath;
        $this->_session = $customerSession;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {

        if(strlen($this->getPath()) > 0 && $this->getPath() != '#'){
            return $this->getUrl($this->getPath());
        }
        return '#';
    }

    /**
     * Get current mca
     *
     * @return string
     */
    private function getMca()
    {
        $routeParts = [
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
        ];

        $parts = [];
        foreach ($routeParts as $key => $value) {
            if (!empty($value) && $value != $this->_defaultPath->getPart($key)) {
                $parts[] = $value;
            }
        }
        return implode('/', $parts);
    }
    

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        $layout = $this->getLayout();
        $containerChilds = $layout->getChildNames($this->getNameInLayout());
        $level = (int) $this->getLevel();
        if(count($containerChilds) > 0){
            foreach ($containerChilds as $containerChild) {
                foreach ($layout->getChildBlocks($containerChild) as $child) {
                    if($child->getCurrent() || $child->getUrl($child->getPath()) == $child->getUrl($child->getMca())) {
                        return true;
                    }
                }
            }
        } else {
             return ($level == 1) && ($this->getCurrent() || $this->getUrl($this->getPath()) == $this->getUrl($this->getMca()));
        }
       
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {   
        $subVendor = $this->_session->getSubVendorData();
        if(empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'cssubaccount_profile'){ 
             return;
        }elseif(!empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'cssubaccount') {
           return;
        }elseif(!empty($subVendor) && $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())) == 'vendor_profile'){
            return;
        }
        

        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        
        $highlight = '';
        if ($this->isCurrent()) {
            $highlight = ' active';
        }
        if (0) {
            $html = '<li id="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())).'" class="nav item1">';
            $html .= '<i class="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())).'"></i>';
            $html .= '<span><strong style="margin-left: 3px;">'
                . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()))
                . '</strong></span>';
                
            $childHtml = '';
            $childHtml = $this->getChildHtml();
            if(strlen($childHtml) > 0) {
                $html .= '<span class="fa arrow"></span>';
            }
            
            if(strlen($childHtml) > 0) {
                $html .= $childHtml;
                $html .= '<div class="largeview-submenu">';
                $html .= $childHtml;
                $html .= '</div>';
            }
            
            $html .= '</a>';
            $html .= '</li>';
        } else {
            /*if(!empty($subVendor)){
                $resources = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($subVendor['id'])->getRole();
                if($resources !== 'all'){
                    $role = explode(',', $resources);
                    if($this->getPath() !== '#'){
                        if(!in_array($this->getPath(), $role))
                        {
                            
                            return;
                        }
                    }
                    else
                    {
                        if(!in_array($this->getPath().$this->getName(), $role))
                        {
                            
                            return;
                        }
                    }
                }
                
            }*/

            if(!empty($subVendor)){
                $resources = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($subVendor['id'])->getRole();
                if($resources !== 'all'){
                    /*if(strpos($this->getPath(), '#') === false){
                        $role = explode(',', $resources);
                        //echo $this->getPath()."</br>";
                        if(!in_array($this->getPath(), $role))
                        {
                            echo 'path='.$this->getPath().'---';
                            //print_r($role);
                            //return;
                        }
                    }*/

                    $stringpath = $this->escapeHtml((string) new \Magento\Framework\Phrase($this->getPath()));
                    if($stringpath == "#")
                    {
                        $stringpath = $this->escapeHtml((string) new \Magento\Framework\Phrase($this->getPath())).$this->escapeHtml((string) new \Magento\Framework\Phrase($this->getName()));
                    }
                    else
                    {
                        $stringpath = $this->escapeHtml((string) new \Magento\Framework\Phrase($this->getPath()));
                        $stringpath =explode("/",$stringpath);
                        if(!empty($stringpath))
                        {
                            if(!isset($stringpath[1])|| !$stringpath[1])
                                $stringpath[1] ="index";
                            
                            if(!isset($stringpath[2])|| !$stringpath[2])
                                $stringpath[2] ="index";
                            
                            $stringpath = $stringpath[0]."/".$stringpath[1]."/".$stringpath[2]; 
                        }
                    }
                        $role = explode(',', $resources);
                        /*if(strpos($resources, $stringpath) === false)
                        {
                            return;
                        }*/
                        //echo $this->getName();
                        if(in_array($stringpath, $role) || ($this->getName() === 'vendor_dashboard'))
                        {
                            $html = '<li id="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())).'" class="nav item"><a class="' . $highlight . '" href="' . $this->escapeHtml($this->getHref()) . '"';
                            $html .= $this->getTitle()
                                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                                : '';
                            $html .= '>';
                            $html .= '<i class="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())).'"></i>';

                            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                                $html .= '<span><strong style="margin-left: 3px;">';
                            } else {
                                $html .= '<span style="margin-left: 3px;">';
                            }

                            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

                            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                                $html .= '</strong></span>';
                            } else {
                                $html .= '</span>';
                            }

                            $childHtml = '';
                            $childHtml = $this->getChildHtml();
                            
                            if(strlen($childHtml) > 0) {
                                $html .= '<span class="fa arrow"></span>';
                            }
                            
                            $html .= '</a>';

                            if(strlen($childHtml) > 0) {
                                //print_r('aaaaaaa'.$childHtml.'bbbbb');die;
                                $html .= $childHtml;
                                $html .= '<div class="largeview-submenu">';
                                $html .= $childHtml;
                                $html .= '</div>';
                            }
                            $html .= '</li>';
                            return $html;
                        }
                        else{
                            return;
                        }
                        


                   // }


                }
            }
//echo $this->getName()."</br>";
            $html = '<li id="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getName())).'" class="nav item"><a class="' . $highlight . '" href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= '>';
            $html .= '<i class="'.$this->escapeHtml((string)new \Magento\Framework\Phrase($this->getFontAwesome())).'"></i>';

            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                $html .= '<span><strong style="margin-left: 3px;">';
            } else {
                $html .= '<span style="margin-left: 3px;">';
            }

            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

            if ($this->getIsHighlighted() || strlen($highlight) > 0) {
                $html .= '</strong></span>';
            } else {
                $html .= '</span>';
            }

            $childHtml = '';
            $childHtml = $this->getChildHtml();
            
            if(strlen($childHtml) > 0) {
                $html .= '<span class="fa arrow"></span>';
            }
            
            $html .= '</a>';

            if(strlen($childHtml) > 0) {
                //print_r('aaaaaaa'.$childHtml.'bbbbb');die;
                $html .= $childHtml;
                $html .= '<div class="largeview-submenu">';
                $html .= $childHtml;
                $html .= '</div>';
            }
            $html .= '</li>';
        }

        return $html;
    }
}
