<?php
// Add options page
if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

}

// Reset validation erros on form "save as a draft"
add_action('acf/validate_save_post', 'bs_clear_all_errors', 10, 0);
function bs_clear_all_errors() {

  if ( ! isset( $_POST['field_5d2d9016a2abe'] ) || $_POST['field_5d2d9016a2abe'] == 'draft' ) {
    acf_reset_validation_errors();
  }

}

// manipulate the case study AFTER it has been saved
add_action('acf/save_post', 'bs_acf_save_post', 11);
function bs_acf_save_post( $post_id ) {

  // check if post is Case
	if ( get_post_type( $post_id ) != 'case' ) {
		return;
	}

  // Get School Details field group
  $postassessment_learning = get_field( 'post-assessment_learning', $post_id );
  // Create title
  $post_title = __( 'Untitled case study', 'bs_pisa' );
  if ( ! $postassessment_learning['please_suggest_a_title_for_your_case_study'] == '' || ! empty( $postassessment_learning['please_suggest_a_title_for_your_case_study'] ) ) {
    $post_title = $postassessment_learning['please_suggest_a_title_for_your_case_study'];
  }


  // Update post title
  $content = array(
		'ID' => $post_id,
		'post_title' => $post_title,
		'post_content' => '',
	);

  // if not in admin dashboard, update status based on Select status hidden field
  // Get Select status value
  $status = get_field( 'select_status', $post_id );
  if ( ! is_admin() ) {

    $content['post_status'] = $status;

  }

  wp_update_post($content);

	// set the features image
	$material = get_field( 'materials', $post_id );
	$upload_images = $material['upload_images'];

	if ( !empty( $upload_images ) ) {
		set_post_thumbnail( $post_id, $upload_images[0]['ID'] );
	}

  // redirect if form is submitted
  if( 'pending' == $status && ! is_admin() ) {
		wp_safe_redirect( get_the_permalink( 2320 ) );
		die;
	}

}
