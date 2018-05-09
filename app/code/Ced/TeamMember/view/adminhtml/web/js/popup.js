function callFancyBox(memberid,memberemail) {
 	
	var base_url = BASE_URL;
	var split = base_url.split("tmember"); 
	var path = split[0]+'tmember/member/send/'; 
     
	 console.log(path);
 	require(['jquery','Magento_Ui/js/modal/modal'], function ($,modal) {
		 var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Send Message To TeamMember',
                buttons: [{
                    text: $.mage.__('Send'),
                    //class: 'hidden',
                    click: function () {
                    
                    	jQuery.ajax({
                    		type:'POST',
                    		url: path,
                    		data:{
                    			'memberid': memberid,
                    			'message':document.getElementById('ced-messages').value,
                    			'memberemail':memberemail,
                    			
                    			},
                    		success:function( data, textStatus, jQxhr){
                    	
                    		}
                    	});
                                        
                        this.closeModal();
                    }
                }]
            };
		      
            var popup = modal(options, $('#ced-message-content'));

            $('#ced-message-content').modal('openModal');

        }); 
}