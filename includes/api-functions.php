<?php


function fetch_external_content($url) {
    $response = wp_remote_get($url);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        return 'Failed to load content.';
    }

    return wp_remote_retrieve_body($response);
}


// Fetch Data from API
function fetch_clinic_data_from_api() {
    $clinic_id = get_option('docty_clinic_id');
    if (empty($clinic_id)) {
        return [];
    }

    $cached_data = get_transient($transient_key);

    $transient_key = 'clinic_data_' . $clinic_id;
    if ($cached_data) {
        return $cached_data;
    }

   // $url = 'https://backend.docty.life/api/public-api/clinic-profile/' . $clinic_id . '/?include=staff,locations,specialities,reviews';
  //  $url = DOCTY_API_URL.'api/public-api/clinic-profile/' . $clinic_id . '/?include=staff,locations,specialities,reviews';
    $url = DOCTY_API_URL.'api/public-api/clinic-profile/' . $clinic_id . '/?include=staff';
  
    // https://dev-backend.docty.life/api/public-api/clinic-profile/196/?include=staff,locations,specialities,reviews
    $response = wp_remote_get($url);
 
    if (is_wp_error($response)) {
        return [];
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    set_transient($transient_key, $data, 12 * HOUR_IN_SECONDS); // Cache for 12 hours
    return $data;
}


class DoctyApi{
    
    public $response_message;
    public $response_code;
    
    public function otp_post_request($end_point,$data = array()){

		$api_url = DOCTY_API_URL.$end_point;
		
	 
		$body = json_encode($data);  
		$response_hooks = wp_remote_post($api_url, array(
			'timeout'    => 120,
			'body'    => $body,
			'sslverify' => FALSE,
			'headers' => array(
				'content-type' => 'application/json', 
			),
		) );
		$this->response_code = wp_remote_retrieve_response_code($response_hooks);
		$response_data  = json_decode(wp_remote_retrieve_body($response_hooks),true);	

		if($this->response_code!== 200){
			$this->response_message = isset($response_data["status"])? $response_data : "Data not found from api.";
			return $response_data;
		} 		
		return $response_data;
	}
    
    public function post_request($end_point,$data = array()){

		$api_url = DOCTY_API_URL.$end_point;
		
        $docty_token = get_option('docty_clinic_token');
          
        
        $param = array(
			'timeout'    => 120,
			//'body'    => $body,
			'sslverify' => FALSE,
			'headers' => array(
				'content-type' => 'application/json', 
				'auth_token' => $docty_token, 
			),
		);
        
        if( !empty($data) ){
            
            $body = json_encode($data);  
            $param['body'] = $body;
        }
        
        
		$response_hooks = wp_remote_post($api_url,  $param );
		$this->response_code = wp_remote_retrieve_response_code($response_hooks);
		$response_data  = json_decode(wp_remote_retrieve_body($response_hooks),true);	
        
       /*  var_dump( $response_hooks );
        var_dump( $response_data ); */
        
		if($this->response_code!== 200){
			$this->response_message = isset($response_data["status"])? $response_data : "Data not found from api.";
			return $response_data;
		} 		
		return $response_data;
	}
    
    
    
}

