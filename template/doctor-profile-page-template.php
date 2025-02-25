<?php
/*
Template Name: Doctor Profile Page Template
*/


get_header();

$staff_id = (int) get_post_meta(get_the_ID(),'staff_id',true);
 
$api_url = "https://dev-backend.docty.life/api/public-api/doctor-profile/" . $staff_id;
    // Fetch API Response
$response = wp_remote_get($api_url);

if (is_wp_error($response)) {
    echo "<p>Error fetching data. Please try again later.</p>";
    get_footer();
    exit;
}

$doctor = json_decode(wp_remote_retrieve_body($response), true);
$profile = $doctor['profile']; 
// Parse API Response   

$picture = $profile['picture'] ? $profile['picture'] : DOCTY_CLINIC_PLUGIN_DIR.'images/default-profile.png';
$fullName = !empty($profile['displayProfile']['name']) ? $profile['displayProfile']['name'] : $profile['fullName'];
$displayProfile = !empty($profile['displayProfile']['designation']) ? $profile['displayProfile']['designation'] :'Designation not available';
$experience = !empty($profile['displayProfile']['experience']) ? $profile['displayProfile']['experience'] :'Experience not available';
$designation = !empty($profile['displayProfile']['designation']) ? $profile['displayProfile']['designation'] :'';
$doctor_id = $profile['id'];

$rating = isset(  $doctor['rating_summary']['rating']  ) ?  $doctor['rating_summary']['rating'] : 0; 
$reviews =  isset( $doctor['rating_summary']['reviews'] ) ? $doctor['rating_summary']['reviews'] : 0; 
$reviews_arr = $doctor['reviews'];
$locations = $doctor['locations'];
$treatments = $doctor['treatments'];
$practices = $doctor['practices'];

?> 
<div class="container">
        
        <div class="two-col">
            <div class="left-col">
                 
                <div class="card">
                    <div class="doctor-header">
                        <img src="<?php echo $picture; ?>" alt="Doctor Image">
                        <div class="doctor-info">
                            <h2> <?php echo $fullName; ?>  (<?php echo $experience; ?>)</h2>
                            <p> <?php echo $designation; ?> </p>
                            <span class="rating">⭐ <?php echo $rating; ?> (<?php echo $reviews; ?> Reviews)</span>
                        </div>
                    </div>
                    
                        <?php
                        if ( !empty(reviews_arr)) : 
                        ?>
                    <div class="review-card"> 
                        <?php
                        foreach ($reviews_arr as $review) : ?>
                            <div class="review">
                                <strong><?php echo $review['ratings']; ?></strong> <span class="stars ssss"><?php echo str_repeat('★', $review['ratings']); ?></span>
                                <p>“<?php echo $review['review']; ?>”</p>
                                <small><strong><?php echo $review['reviewer']['fullName']; ?></strong></small>
                            </div>
                        <?php 
                        endforeach;   
                        ?>
                        </div>
                        <?php
                            endif;
                        ?>
                    
                    <div class="reasons">
                        <h3>Popular Reasons for Visit</h3>
                        <ul>
                            <?php 
                            foreach ($treatments as $key => $treatment) {
                                echo ' <li><span class="checkmark">✔</span> '.$treatment.'</li>'; 
                            }
                            ?>
                            
                        </ul>
                    </div>
                </div>


            </div>
            <div class="right-col">
                <h2>Book an Appointment</h2>              
                
                <?php 
                
                $src = DOCTY_IFRAME_CHECKUT_URL.''.$doctor_id.'/clinic';
                echo '<iframe src="'.$src.'"  class="booking_iframe" ></iframe>';	 

                /* ?> 
                <p>Select preferred consultation medium</p>
                <div class="consultation-medium">
                    <div class="medium-option active">Schedule Clinic Visit<br>₹ 8000</div>
                    <div class="medium-option">Video Consultation<br>₹ 5000</div>
                </div>
                <h3>Select Clinic for in-person visit at Hospital</h3>
                <div class="hospital-list">
                    <div class="hospital">
                        <div>
                            <strong>Kailash Heart Hospital</strong><br>Lajpat Nagar, Delhi
                        </div>
                        <span>45 min away</span>
                    </div>
                    <div class="hospital">
                        <div>
                            <strong>Fortis Hospital</strong><br>Sector 3, Noida
                        </div>
                        <span>2 hrs away</span>
                    </div>
                </div>
                <h3>Select Consultation Time</h3>
                <div class="consultation-time">
                    <button>Today</button>
                    <button>Tomorrow</button>
                    <button>Select Date</button>
                    <div class="time-options">
                        <div class="time-slot">3:00 PM - 3:20 PM</div>
                        <div class="time-slot">3:20 PM - 3:40 PM</div>
                        <div class="time-slot">3:40 PM - 4:00 PM</div>
                        <div class="time-slot">4:00 PM - 4:20 PM</div>
                    </div>
                </div>
                <?php */ ?>
            </div>
        </div>
</div>
<!-- Experience Section -->
<div class="container">
    <h2>Practice Experience</h2>
    <?php foreach ($practices as $practice) : ?>
        <div class="experience-card">
            <img src="<?php echo $practice['logo']; ?>" alt="Hospital Logo">
            <div class="experience-info">
                <h3><?php echo $practice['institute']; ?></h3>
                <p><?php echo $practice['field']; ?></p>
                <p><?php echo $practice['from'] ? date('M Y', strtotime($practice['from'])) : 'N/A'; ?> - <?php echo $practice['to'] ? date('M Y', strtotime($practice['to'])) : 'Present'; ?></p>
                <p><?php echo $practice['overview']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
    
</div>

<!-- Clinic & Location Section -->
<div class="container">
    <h2>Clinics & Locations</h2>

                            
        <?php foreach ($locations as $location) : ?>
            <div class="clinic-card">
                <div class="clinic-info">
                    <h3><?php echo $location['title']; ?></h3>
                    <p><?php echo $location['city']; ?></p>
                    <p><?php echo $location['address']; ?></p>
                </div>

                <iframe src="https://maps.google.com/maps?q=<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed" ></iframe>

 
            </div>
        <?php endforeach; ?>
         
</div>
<!-- Reviews Section -->

<div class="container">

    <h2>Reviews (<?php echo count($reviews_arr); ?>)</h2>
    <?php foreach ($reviews_arr as $review) : ?>
        <div class="review-card">
            <img src="<?php echo $review['reviewer']['picture']; ?>" alt="User Avatar" class="review-avatar">
            <div class="review-info">
                <h3><?php echo $review['reviewer']['fullName']; ?></h3>
                <p class="stars"><?php echo str_repeat('★', $review['ratings']); ?> <?php echo $review['ratings']; ?> | <?php echo $review['daysAgo']; ?> days ago</p>
                <p><?php echo $review['review']; ?></p>
                <p class="recommend">✔ Yes, Recommend for Appointment</p>
                <p class="reply">↩ Reply</p>
            </div>
        </div>
    <?php endforeach; ?>
     
 </div>
                
<?php 
/*
echo '<pre>';
var_dump($staff_id );
print_r($doctor);
echo '</pre>';*/


get_footer();
?>