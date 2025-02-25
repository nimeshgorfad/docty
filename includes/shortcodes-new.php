<?php
/* 
* Doctors list Shortcode
*/
function docty_doctors_list_shortcode($atts){
		
    $content = "";
	//$data = fetch_clinic_data_from_api();
	
	$DoctyApi = new DoctyApi;
    
    $param = array( 'roles_list' => array(1,3), 'status'=> 1 , 'adHoc'=> false  );
	
    $data = $DoctyApi->post_request('api/associate/my-staff',$param);
	
   // $content = print_r($data,true);
	$booking_page_path = get_option('booking_page');
	
	$booking_page = get_permalink( get_page( $booking_page_path) );
	if( !empty($data)  ){
		$doctors = $data;
		$content .= ' <div id="doctor-carousel" class="owl-carousel owl-theme">';
		
		foreach($doctors as $doctor){
			
			$content .= '<div class="item">';
			$picture = $doctor['picture'] ? $doctor['picture'] : DOCTY_CLINIC_PLUGIN_DIR.'images/default-profile.png';
			$fullName = !empty($doctor['displayProfile']['name']) ? $doctor['displayProfile']['name'] : $doctor['fullName'];
			$displayProfile = !empty($doctor['displayProfile']['designation']) ? $doctor['displayProfile']['designation'] :'Designation not available';
			$experience = !empty($doctor['displayProfile']['experience']) ? $doctor['displayProfile']['experience'] :'Experience not available';
			$doctor_id = $doctor['id'];
			//$content .= $doctor['fullName'];
			//$content .= $doctor['picture'];
            $doc_page = get_permalink( get_page_by_path('staff-'.$doctor_id) ) ;

					
			 $content .= '<div class="doctor-card ng-star-inserted">';
			 
				 $content .= '<div class="card-header">';
					$content .= '<div class="doctor-img "> <img src="'.$picture .'"> </div>';
					$content .= '<div class="doctor-info" tabindex="0"> <h3>  ' . $fullName . ' </h3> <p> '.$experience.' </p> 	</div>';
				 $content .= '</div>';
				 
				 
				  $content .= '<div class="card-body">';
				  $content .= '';
				 $content .= '</div>'; 
				 
				 $content .= '<div class="card-footer">';
				 $content .= '<a href="'.$doc_page.'" > <button class="book-button" >Book Appointment</button> </a>';
				 $content .= '</div>';
				 
			 $content .= '</div>';
			 
			 
			 /* $content .= '<div class="doctor-card ng-star-inserted"> 
						
			 
                        <img src="'.$picture .'">
                        <h3> ' . $fullName . '</h3>
                        <p> '.$displayProfile.'</p>
                        <p> '.$experience.' </p>
                        <button class="book-button" data-doctor-id="${doctor.id}">Book Appointment</button>
                    </div>';  */
					
					
			$content .= '</div>';
			
		}
		
		$content .= '</div>';
		
	}else{
		$content .= '<div> <p> Data is not available </p> </div>'; 
		
	}
	
   return $content;
}

add_shortcode('docty_doctors_list', 'docty_doctors_list_shortcode');

function  docty_doctor_view_shortcode($atts){
	$content = '';
	
	if(isset($_GET['dr']) && !empty($_GET['dr']) ){
		
		$src = DOCTY_IFRAME_URL.''.$_GET['dr'].'/clinic';
		$content = '<iframe src="'.$src.'" height="500" width="100%"  ></iframe>';	
		
	}else{
		$content = '<p> Please select doctor  </p>';
	}
	
	
	return $content;
}

add_shortcode('docty_doctor_view', 'docty_doctor_view_shortcode');



?>