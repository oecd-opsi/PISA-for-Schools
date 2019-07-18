<?php
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
  $school_details = get_field( 'your_school_details', $post_id );
  // Create title
  $post_title = __( 'Untitled case study', 'bs_pisa' );
  if ( ! $school_details['school_name'] == '' || ! empty( $school_details['school_name'] ) ) {
    $post_title = $school_details['school_name'];
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

// redirect to the proper page after form submission
// add_action('acf/submit_form', 'bs_case_study_form_redirect_after_submit', 0, 2);
function bs_case_study_form_redirect_after_submit( $form, $post_id ) {

	// if( $_POST['field_5d2d9016a2abe'] == 'pending' ) {
	// 	wp_safe_redirect( get_the_permalink( 2320 ) );
	// 	die;
	// }

	// if( $_POST['csf_action'] == 'save' ) {
  //
	// 	$case_study_form_page = $_SERVER['REQUEST_URI'];
  //
	// 	$step = ( isset( $_POST['form_step'] ) && intval( $_POST['form_step'] ) > 0 ? intval( $_POST['form_step'] ) : 0 );
  //
	// 	wp_safe_redirect( get_the_permalink( $case_study_form_page ).'?edit='.$post_id.'&updated=true#step-'.$step );
	// 	die;
	// }

	// if( $_POST['csf_action'] == 'saveandpreview' ) {
  //
	// 	wp_safe_redirect( get_the_permalink( $post_id ) );
	// 	die;
	// }

}
