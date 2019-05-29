<?php
/*
Plugin Name:	Black Studio for OECD PISA for school
Plugin URI:		https://www.ecorecuperi.it
Description:	Custom functions.
Version:		1.0.0
Author:			Black Studio
Author URI:		https://www.blackstudio.it
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with This plugin. If not, see {URI to Plugin License}.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'wp_enqueue_scripts', 'bs_enqueue_files' );
function bs_enqueue_files() {

	// loads a CSS file in the head.
	wp_enqueue_style( 'bs-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );

	// loads JS files in the footer.
	wp_enqueue_script( 'bs-script', plugin_dir_url( __FILE__ ) . 'assets/js/bs-script.js', '', '1.0.0', true );
}

//* Show courses archive and single only to student and instructor role
function bs_show_courses_to_students_instructors() {

  global $wp_query;
	$user = wp_get_current_user();

  // redirect from 'course' CPT to home page
  if ( is_archive('sfwd-courses') || is_singular('sfwd-courses') ) :

		// only if not student or instructor role
		if ( in_array( 'student', (array) $user->roles ) ||
				 in_array( 'instructor', (array) $user->roles ) ||
			   current_user_can( 'administrator' )
			  ) return;

		$url   = get_bloginfo('url');
		wp_redirect( esc_url_raw( $url ), 301 );
		exit();

  endif;

}
add_action ( 'template_redirect', 'bs_show_courses_to_students_instructors', 1);

//* Hide WP dashboard by user role
function bs_disable_dashboard() {

	// if user is not logged in and in not admin area return
	if ( ! is_user_logged_in() && ! is_admin() )
		return;

	$user = wp_get_current_user();

	// we are in admin area, but if current user is not an administrator or an instructor redirect to home
	if ( ! in_array( 'instructor', (array) $user->roles ) &&
			 ! current_user_can( 'administrator' )
		 ) {

		wp_redirect( home_url() );
		exit;

	}

}
add_action('admin_init', 'bs_disable_dashboard');

//* Hide admin bar by user role
function bs_hide_admin_bar() {

	// if user is not logged in return
	if ( ! is_user_logged_in() )
		return;

	$user = wp_get_current_user();

	// if current user is not an administrator or an instructor hide admin bar
	if ( ! in_array( 'instructor', (array) $user->roles ) &&
			 ! current_user_can( 'administrator' )
		 ) {

		show_admin_bar(false);

	}

}
add_action('admin_init', 'bs_hide_admin_bar');

//* Add role class to body
function add_role_to_body($classes) {

	global $current_user;
	$user_role = array_shift($current_user->roles);

	$classes .= 'role-'. $user_role;
	return $classes;
	
}
add_filter('body_class','add_role_to_body');
add_filter('admin_body_class', 'add_role_to_body');
