<?php

function docty_embed_content($atts) {
    // Extract attributes from the shortcode
    $attributes = shortcode_atts(array(
        'url' => 'https://web.docty.life/', // Default URL if none provided
    ), $atts);

    // Unique ID for the container
    $div_id = uniqid('docty_content_');

    // Generate the HTML code for the container and script
    $content = '<div id="' . $div_id . '" style="width:100%; height:600px;">Loading...</div>';
    $content .= '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            fetch("' . esc_url($attributes['url']) . '")
            .then(response => response.text())
            .then(data => {
                document.getElementById("' . $div_id . '").innerHTML = data;
            })
            .catch(error => {
                document.getElementById("' . $div_id . '").innerHTML = "Failed to load content.";
            });
        });
    </script>';

    return $content;
}
add_shortcode('docty_embed_content', 'docty_embed_content');



// Display Staff Shortcode
function display_clinic_staff() {
    $data = fetch_clinic_data_from_api();
    $staff = $data['staff'] ?? [];

    ob_start();
    if (!empty($staff)) {
        echo '<h2>Clinic Staff</h2><ul>';
        foreach ($staff as $member) {
            echo '<li>';
            if (!empty($member['picture'])) {
                echo '<img src="' . esc_url($member['picture']) . '" alt="' . esc_attr($member['fullName']) . '" />';
            }
            echo '<strong>' . esc_html($member['fullName']) . '</strong>';
            if (!empty($member['displayProfile']['designation'])) {
                echo '<p>' . esc_html($member['displayProfile']['designation']) . '</p>';
            }
            if (!empty($member['overview'])) {
                echo '<p>' . $member['overview'] . '</p>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No staff available.</p>';
    }
    return ob_get_clean();
}
add_shortcode('clinic_staff', 'display_clinic_staff');

// Display Locations Shortcode
function display_clinic_locations() {
    $data = fetch_clinic_data_from_api();
    $locations = $data['locations'] ?? [];

    ob_start();
    if (!empty($locations)) {
        echo '<h2>Clinic Locations</h2><ul>';
        foreach ($locations as $location) {
            echo '<li>';
            if (!empty($location['media'])) {
                echo '<img src="' . esc_url($location['media']) . '" alt="' . esc_attr($location['title']) . '" />';
            }
            echo '<strong>' . esc_html($location['title']) . '</strong>';
            echo '<p>Address: ' . esc_html($location['address']) . '</p>';
            echo '<p>Phone: ' . esc_html($location['phone']) . '</p>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No locations available.</p>';
    }
    return ob_get_clean();
}
add_shortcode('clinic_locations', 'display_clinic_locations');

// Display Specialties Shortcode
function display_clinic_specialities() {
    $data = fetch_clinic_data_from_api();
    $specialities = $data['specialities'] ?? [];

    ob_start();
    if (!empty($specialities)) {
        echo '<h2>Specialties</h2><ul>';
        foreach ($specialities as $speciality) {
            echo '<li>';
            if (!empty($speciality['symbol'])) {
                echo '<img src="' . esc_url($speciality['symbol']) . '" alt="' . esc_attr($speciality['title']) . '" />';
            }
            echo '<strong>' . esc_html($speciality['title']) . '</strong>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No specialties available.</p>';
    }
    return ob_get_clean();
}
add_shortcode('clinic_specialities', 'display_clinic_specialities');

// Display Reviews Shortcode
function display_clinic_reviews() {
    $data = fetch_clinic_data_from_api();
    $reviews = $data['reviews'] ?? [];

    ob_start();
    if (!empty($reviews)) {
        echo '<h2>Reviews</h2><ul>';
        foreach ($reviews as $review) {
            echo '<li>';
            echo '<strong>Rating: ' . esc_html($review['ratings']) . '/5</strong>';
            echo '<p>' . esc_html($review['review']) . '</p>';
            if (!empty($review['reviewer']['fullName'])) {
                echo '<p>Reviewer: ' . esc_html($review['reviewer']['fullName']) . '</p>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No reviews available.</p>';
    }
    return ob_get_clean();
}
add_shortcode('clinic_reviews', 'display_clinic_reviews');
