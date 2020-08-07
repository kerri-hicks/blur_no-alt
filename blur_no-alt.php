<?php

/*
Plugin Name: Blur No-Alt
Plugin URI: http://dlibwwwcit.services.brown.edu
Description: Blur images in the WP editor interface if they don't have alt text
Author: Kerri Hicks, Brown University Library
*/

function blur_admin_theme_style() {
    wp_enqueue_style('blur-admin-theme', plugins_url('blur_no-alt.css', __FILE__)) ;
}
add_action('admin_enqueue_scripts', 'blur_admin_theme_style') ;

?>
