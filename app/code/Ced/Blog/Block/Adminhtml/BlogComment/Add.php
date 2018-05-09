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

namespace Ced\Blog\Block\Adminhtml\BlogComment;

class Add extends \Magento\Backend\Block\Widget\Form\Container
{

    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ced_Blog';
        $this->_controller = 'adminhtml_blogComment';
        $this->_mode = 'add';
        $this->buttonList->update('save', 'label', __('Save Comment'));
        $this->buttonList->update('save', 'id', 'save_button');
        $this->buttonList->update('reset', 'id', 'reset_button');
        $this->_formScripts[] = '
            require(["prototype"], function(){
                toggleParentVis("add_review_form");
                toggleVis("save_button");
                toggleVis("reset_button");
            });
        ';

        $this->_formInitScripts[] = '
            require(["jquery","prototype"], function(jQuery){
            window.review = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,
                    gridRowClick : function(data, click) {     
                        if(Event.findElement(click,\'TR\').title){
                            review.productInfoUrl = Event.findElement(click,\'TR\').title;
                            review.loadProductData();
                            review.showForm();
                            review.formHidden = false;
                        }
                    },
                    loadProductData : function() {
                        jQuery.ajax({
                            type: "POST",
                            url: review.productInfoUrl,
                            data: {
                                form_key: FORM_KEY
                            },
                            showLoader: true,
                            success: review.reqSuccess,
                            error: review.reqFailure
                        });
                    },
                    showForm : function() {
                        toggleParentVis("add_review_form");
                        toggleVis("grid_id");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },
                    updateRating: function() {
                        elements = [$("select_stores"), $("rating_detail").getElementsBySelector("input[type=\'radio\']")].flatten();
                        $(\'save_button\').disabled = true;
                        var params = Form.serializeElements(elements);
                        if (!params.isAjax) {
                            params.isAjax = "true";
                        }
                        if (!params.form_key) {
                            params.form_key = FORM_KEY;
                        }
                        new Ajax.Updater("rating_detail", "' .
            $this->getUrl(
                'review/product/ratingItems'
            ) .
            '", {parameters:params, evalScripts: true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                    },
                    reqSuccess :function(response) {
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("post_id").value = response.id;
                            $("post_title").innerHTML = \'<a href="' .
            $this->getUrl(
                'blog/post/edit'
            ) .
            'post_id/\' + response.id + \'" target="_blank">\' + response.title + \'</a><input type="hidden" name="post_title" value="\'+response.title+\'">\';
                        } else if ( response.message ) {
                            alert(response.message);
                        }
                    }
                }
            }();

           /*  Event.observe(window, \'load\', function(){

                 if ($("select_stores")) {

                     Event.observe($("select_stores"), \'change\', review.updateRating);
                 }
            }); */
            });
           //]]>
        ';
    }



    /**
     * Get add new review header text
     *
     * @return \Magento\Framework\Phrase
     */

    public function getHeaderText()
    {

        return __('New Comment');
    }
}
