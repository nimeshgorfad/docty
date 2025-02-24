// 

jQuery(document).ready(function(){
	
	// Verify OTP 
	jQuery(document).on('click','#docty_verify_otp',function(){
		var btnobj = jQuery("#docty_verify_otp");
		var login_verify_otp = jQuery("#login_verify_otp").val();
		var login_token = jQuery("#login_token").val();
		
		var login_id = jQuery("#login_id").val();
		var form_data = { "action":"docty_veryfy_otp_ajax","login_id":login_id, 'otp':login_verify_otp,'token':login_token };	
			
			  jQuery.ajax({
				
					url: es.ajaxurl,

					data: form_data,

					type: 'POST',

					dataType: "json",   

					beforeSend:function(){	

						jQuery(btnobj).attr("disabled", true);

						jQuery(".spinner_ifsp").addClass("active is-active");	

					},

					complete:function(){

						 jQuery(btnobj).attr("disabled", false);

					},

					success : function(response){
 
						if (response.success) {
							
							jQuery('.wrap_opt').hide();
							jQuery('.wrap_opt_v').hide();
							jQuery('.wrap_message').html('<h2> Authentication successful  </h2>');
							
							
						}else{
							alert( response.data );
						
						}

					},

					error : function(jqXHR){
 
						jQuery(btnobj).attr("disabled", false);

						jQuery(".spinner_ifsp").removeClass("active is-active");				

					}

				});
				
		
	});
	
	// Send OTP 
	jQuery(document).on('click','#docty_otp_edit',function(){
		jQuery('.wrap_opt').show();
		jQuery('.wrap_opt_v').hide();
		
		jQuery("#login_verify_opt").val('');
		jQuery("#login_token").val('');		
							
	});
	
	jQuery(document).on('click','#docty_otp,#docty_otp_resend',function(){
		var btnobj = jQuery("#docty_otp");
		var login_id = jQuery("#login_id").val();
		var form_data ={"action":"docty_otp_ajax","login_id":login_id};	
			
			req = jQuery.ajax({

					url: es.ajaxurl,

					data: form_data,

					type: 'POST',

					dataType: "json",   

					beforeSend:function(){	

						jQuery(btnobj).attr("disabled", true);

						jQuery(".spinner_ifsp").addClass("active is-active");	

					},

					complete:function(){

						 jQuery(btnobj).attr("disabled", false);

					},

					success : function(response){
 
						if (response.success) {
							
							jQuery('.wrap_opt').hide();
							jQuery('.wrap_opt_v').show();
		
							jQuery('#login_token').val( response.data.token );
							jQuery('#span_send_to').html( login_id );
							
						}else{
							alert( response.data );
						
						}

					},

					error : function(jqXHR){
 
						jQuery(btnobj).attr("disabled", false);

						jQuery(".spinner_ifsp").removeClass("active is-active");				

					}

				});
				
		
	});
	
	
	
	
});