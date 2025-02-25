<?php

// Add Admin Menu
function docty_add_admin_menu() {
    add_menu_page(
        'Docty Clinic',       // Page title
        'Docty Clinic',       // Menu title
        'manage_options',     // Capability
        'docty-clinic',       // Menu slug
        'docty_settings_page' // Callback function
    );

    // Add submenu
    add_submenu_page(
        'docty-clinic',
        'Docty Clinic Settings',
        'Settings',
        'manage_options',
        'docty-clinic-settings',
        'docty_settings_page'
    );
    
     add_submenu_page(
        'docty-clinic',  
        'Setup Booking Page',    
        'Setup Booking Page',    
        'manage_options',      
        'setup-booking-page',  
        'setup_booking_page_callback'  
    ); 
    
    add_submenu_page(
        'docty-clinic',  
        'Staff',    
        'Staff',    
        'manage_options',      
        'staff-page',  
        'docty_staff_page_callback'  
    );
    
}
add_action('admin_menu', 'docty_add_admin_menu');

function docty_staff_page_callback(){
      /*  $docty_token = get_option('docty_clinic_token');
     print_r($docty_token);    */
    // Delete page 
	if( isset( $_POST['nkg_delete_page'] ) ){
		
		$dr_id = $_POST['dr_id'];
		$page_slug = 'staff-'.$dr_id;
		$page = get_page_by_path($page_slug);
		
		if ($page) {
			wp_delete_post($page->ID, true); // true means force delete (bypasses trash)
			 
			echo '<div class="updated notice  is-dismissible"><p>Page deleted successfully!</p></div>';
		} else {
			 
			echo '<div class="notice notice-info is-dismissible"><p>Page not found.</p></div>';
		}
		
	}
	
	if( isset( $_POST['nkg_generate_page'] ) ){
		$dr_id = $_POST['dr_id'];
		$page_title = 'staff-'.$dr_id;
		$page_slug = 'staff-'.$dr_id;
		$template_name = 'doctor-profile-template.php'; // Adjust the path as needed
		$template_name = 'doctor-profile-page-template.php'; // Adjust the path as needed

		// Check if the page already exists
		$existing_page = get_page_by_path($page_slug);
		if (!$existing_page) {
			$page_id = wp_insert_post([
				'post_title'     => $page_title,
				'post_name'      => $page_slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'page_template'  => $template_name,
			]);

			if ($page_id) {
				update_post_meta($page_id,'staff_id',$dr_id);
				  
				echo '<div class="updated notice  is-dismissible"><p>Page created successfully!</p></div>';
			}
		} else {
			 
			echo '<div class="notice notice-info is-dismissible"><p>Page already exists.</p></div>';
		}
		
		
	}
	
	
    $DoctyApi = new DoctyApi;
    
    $param = array( 'roles_list' => array(1,3), 'status'=> 1 , 'adHoc'=> false  );
	
    $staffs = $DoctyApi->post_request('api/associate/my-staff',$param);
     //var_dump( $staffs );   
    ?>
    <div class="wrap">
        <h1>Docty Clinic Staff</h1>
        <div class="docty_wrap" style="width: 80%;" > 
			<h3>Available data to generate pages</h3> 
		
            <table class="table_staff widefat"  border="1">
				<thead>
					<tr>
						<th>Name </th>
						<th></th>
						 
						<th> Action </th> 
					 </tr>					  
				</thead> 
                <tbody>
                    <?php 
                        foreach($staffs as $doctor){
                            $id = $doctor['id'];
                            $picture = $doctor['picture'] ? $doctor['picture'] : DOCTY_CLINIC_PLUGIN_DIR.'images/default-profile.png';
                            $fullName = !empty($doctor['displayProfile']['name']) ? $doctor['displayProfile']['name'] : $doctor['fullName'];
                            $displayProfile = !empty($doctor['displayProfile']['designation']) ? $doctor['displayProfile']['designation'] :'Designation not available';
                            $experience = !empty($doctor['displayProfile']['experience']) ? $doctor['displayProfile']['experience'] :'Experience not available';
                            
							$page_url = site_url().'/staff-'.$id;
                            echo '<tr>';
							echo '<td> '.$fullName.' </td>';
							echo '<td> '. $page_url .' </td>';
							echo '<td> <a href="'.$page_url.'" class="button button-primary" > View </a> ';
							echo '<form method="post" style="display: inline;"  > <input type="hidden" name="dr_id" value="'.$id.'"  >  <button type="submit"  class="button button-primary"  name="nkg_delete_page" > Delete Page </button>  </form>';							
							echo '<form method="post" style="display: inline;" > <input type="hidden" name="dr_id" value="'.$id.'"  >  <button type="submit" class="button button-primary" name="nkg_generate_page" > Generate Page </button> </form>    </td>';
								
							echo '</tr>';
                        }
                    
                    ?>
                </tbody>
            </table>
        </div>
    </div>  
    <?php 
    
}

// Register Settings
function docty_register_settings() {
    register_setting('docty-settings-group', 'docty_clinic_id');
}
add_action('admin_init', 'docty_register_settings');

// Settings Page
function docty_settings_page() {
    
    if( isset( $_POST['retry_connection'] ) ){
        
        update_option('docty_clinic_token','');
        
    }
    
    $docty_token = get_option('docty_clinic_token');
    $docty_user = get_option('docty_clinic_user');
        
    ?>
    <div class="wrap">
        <h1>Docty Clinic Settings</h1>
        <div class="docty_wrap" > 
        
        <?php 
		/* echo '<pre>';
		print_r( $docty_user );
		echo '</pre>'; */
		
        if( empty( $docty_token ) ){
            
            ?>
            <table class="form-table">
                <tr>
                    <td> <h3> Login  </h3>  </td>
                </tr>
                <tr class="wrap_opt">                       
                    <td>                           
                        <input type="text" id="login_id" value="" placeholder="Enter email or mobile "   > 
                    </td>
                </tr>
                <tr class="wrap_opt" >
                    <td> <button id="docty_otp"  class="button button-primary"  > Get OTP </button>    </td>
                </tr>
                
                <tr class="wrap_opt_v" >                       
                    <td>      
                         <p>   Sent to <span id="span_send_to"  > </span>  <a id="docty_otp_edit" > Edit </a> </p> 
                        <input type="text" id="login_verify_otp" value="" placeholder="Enter OTP"   > 
                        <input type="hidden" id="login_token" value=""   > 
                    </td>
                </tr>
                <tr class="wrap_opt_v">
                    <td> <button id="docty_verify_otp"  class="button button-primary"  > Continue </button>  <button id="docty_otp_resend"  class="button button-primary"  > Resend </button>    </td>
                </tr> 
                <tr class="">
                    <td class="wrap_message" >   </td>
                </tr>
                
                
            </table>
            <?php 
            
        }else{
           ?>
           <table class="form-table">
                <tr class="">
                    <td class="wrap_message" >  <h2> API Connected Successfully!  </h2>  </td>
                </tr> 
				<tr class="">
                    <td>  <img src="<?php echo $docty_user['user']['picture']; ?>" height="100px" width="100px" >  </td>
                </tr>
				<tr class="">
                    <td  >  <h3> Name : <?php echo $docty_user['user']['fullName']; ?>  </h3>  </td>
                </tr>
				<tr class="">
                    <td  > 
						<p> <b> Token </b> :   </p> 
						<input type="text"  value="<?php echo $docty_user['token']; ?>" readonly  style="width:100%"> 
						</td>
                </tr>
                
                <tr class="">
                    <td  > <form method="post" >  <button  type="submit" name="retry_connection" class="button button-primary"   > Retry connection  </button>  </form> </td>
                </tr>
                
           </table>
           <?php  
            
        }
        
        ?>
        
        <?php /* ?>
        
        <form method="post" action="options.php">
            <?php settings_fields('docty-settings-group'); ?>
            <?php do_settings_sections('docty-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Clinic ID</th>
                    <td><input type="text" name="docty_clinic_id" value="<?php echo esc_attr(get_option('docty_clinic_id')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php */ ?>
        </div>
    </div>
    <?php
}


function setup_booking_page_callback() {
    // Save the selected page if the form is submitted
    if (isset($_POST['save_booking_page'])) {
        if (isset($_POST['booking_page'])) {
            update_option('booking_page', sanitize_text_field($_POST['booking_page']));
            echo '<div class="updated"><p>Booking page saved successfully!</p></div>';
        }
    }

    // Get the saved page ID
    $saved_page_id = get_option('booking_page', '');

    ?>
    <div class="wrap">
    
    
        <h1>Setup Booking Page</h1>
        <div class="docty_wrap" > 
        <form method="post">
            <table class="form-table docty_table" >
                <tbody>
                    <tr>
                          
                         <td> <b> Doctors List Shortcode ( Copy and paste this shortcode in the page as HTML code:)  </b>   
                         </td>    
                    </tr>
                    <tr>                       
                        <td>      
                            <input type="text" id="booking_shortcode" value="[docty_doctors_list]" readonly  > 
                        </td>
                    </tr>  
                    <tr>
                         
                         <td> <b>  Select a Page for View Book Appointment page :   </b>   
                         </td>    
                    </tr>
                    
                     <tr>
                            
                            <td>       
                            <select id="booking_page" name="booking_page">
                        <?php
                        $pages = get_pages();
                        foreach ($pages as $page) {
                            $selected = ($saved_page_id == $page->ID) ? 'selected' : '';
                            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                        }
                        ?>
                        </select>  </td>
                    </tr> 

                    <tr>
                         
                         <td> <b>  Copy and paste this shortcode in the selected page as HTML code  </b>   
                         </td>    
                    </tr>
                    <tr>
                        
                        <td>      
                            <input type="text"  value="[docty_doctor_view]" readonly  > 
                        </td>
                    </tr>  
                    
                    
                    <tr>
                        
                         <td><input type="submit" name="save_booking_page" class="button button-primary" value="Save Page">   
                         </td>    
                    </tr>
                    
                </tbody>
            
            </table>
         
        </form>
        </div>
    </div>
    <?php
}


// OTP Ajax 

add_action('wp_ajax_docty_veryfy_otp_ajax', 'docty_veryfy_otp_ajax'); 

function docty_veryfy_otp_ajax() {
    
    $login_id = isset($_POST['login_id']) ? sanitize_text_field($_POST['login_id']) : '';
    $otp = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']) : '';
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
	if( !empty( $otp  ) ){
		
		$DoctyApi = new DoctyApi;
        
		$data = array(
                'login_id' => $login_id ,
                'otp' => $otp ,
                'token' => $token ,
                'loginMode' => 2 ,
                'password' => '' 
            ); 
            
          
    
		$res = $DoctyApi->otp_post_request('api/app/user/verify-otp-for-login',$data);
           
		if (!empty($res['status'])) {
            $docty_token = $res['token'];
            
            update_option('docty_clinic_token', $docty_token );            
            update_option('docty_clinic_user', $res );
            
			wp_send_json_success( $res );
            
            
		} else {
			wp_send_json_error('INVALID OTP');
		}
		
	}else{
		wp_send_json_error("Please enter a OTP.");
	}
	
}

add_action('wp_ajax_docty_otp_ajax', 'docty_otp_ajax'); 

function docty_otp_ajax() {
    
    $login_id = isset($_POST['login_id']) ? sanitize_text_field($_POST['login_id']) : '';
	if( !empty( $login_id  ) ){
		
		$DoctyApi = new DoctyApi;
		$data = array("loginId" => $login_id);
		$res = $DoctyApi->otp_post_request('api/app/user/send-otp-for-login',$data);
       // var_dump(	$res );
		
            
		if (!empty($res['status'])) {
			wp_send_json_success( $res );
		} else {
			wp_send_json_error($res['errors']);
		}
		
	}else{
		wp_send_json_error("Please enter a login id.");
	}
	
}
