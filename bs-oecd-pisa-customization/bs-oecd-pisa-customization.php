<?php
/*
Plugin Name:	Black Studio for OECD PISA for school
Plugin URI:		https://oecdpisaforschools.org
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
	wp_enqueue_style( 'bs-style', plugin_dir_url( __FILE__ ) . 'assets/css/bs-style.css' );

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
// add_action ( 'template_redirect', 'bs_show_courses_to_students_instructors', 1);

//* Hide WP dashboard by user role
function bs_disable_dashboard() {

	// if user is not logged in and in not admin area return
	if ( ! is_user_logged_in() && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
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
function bs_add_role_to_body($classes) {

	global $current_user;
	$user_role = $current_user->roles[0];

	$classes .= ' role-'. $user_role;
	return $classes;

}
add_filter('body_class','bs_add_role_to_body');
add_filter('admin_body_class', 'bs_add_role_to_body');

//* Forum banner
function bs_display_forum_banner() {

	// check if is forums related pages
	if ( ! is_bbpress() && ! is_page( 1657 ) || bp_is_home() )
		return;

	// get closed banner ids
	$closed_banner_comma_list = '';
	if( isset( $_COOKIE['closedBanner'] ) ) {

		$closed_banner_comma_list = str_replace( array( '[', '\"', ']' ), '', $_COOKIE['closedBanner'] );

	}

	// create target array
	$target = array( 'everyone' );

		// check if user is new (registered less than 15 days ago)
		$now = time();
		$current_user_data = get_userdata( get_current_user_id() );
		$date_diff = $now - strtotime( $current_user_data->user_registered );
		$date_diff_day = round( $date_diff / ( 60 * 60 * 24 ) );
		if ( $date_diff_day <= 15 ) {
			$target[] = 'new';
		}

		// check if user is very active on forum (more than 50 topic and reply)
		$topic_reply_sum = bbp_get_user_reply_count_raw( get_current_user_id() ) + bbp_get_user_topic_count_raw( get_current_user_id() );
		if ( $topic_reply_sum > 50 ) {
			$target[] = 'top';
		}

	$target_comma_list = rtrim(implode(',', $target), ',');

	$args = array(
    'id' 						=> 1979,
		'closedbanner'	=> $closed_banner_comma_list,
		'target'				=> $target_comma_list,
	);
	echo render_view( $args );

}
add_action( 'buddyboss_inside_wrapper', 'bs_display_forum_banner', 40 );

// Add widget area on side of forum pages
function bs_forum_side_widgets_init() {

	register_sidebar( array(
		'name'          => 'Forum side',
		'id'            => 'forum_top',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widgettitle widget-title">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'bs_forum_side_widgets_init' );
