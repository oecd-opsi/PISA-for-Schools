<?php
// Reset validation on form submit
add_action('acf/validate_save_post', 'bs_clear_all_errors', 10, 0);
function bs_clear_all_errors() {
  acf_reset_validation_errors();
}

// manipulate the case study AFTER it has been saved
// add_action('acf/save_post', 'bs_acf_save_post', 11);
function bs_acf_save_post( $post_id ) {

  // check if post is Case
	if ( get_post_type( $post_id ) != 'case' ) {
		return;
	}

  // Get School Details field group
  $school_details = get_field( 'your_school_details', $post_id );
  // Create title
  $post_title = __( 'Untitled case study', 'bs_pisa' );
  if ( ! $school_details['school_name'] == '' &&
       ! empty( $school_details['school_name'] ) &&
       ! $school_details['country'] == '' &&
       ! empty( $school_details['country'] ) {
    $post_title = $school_details['school_name'] . ' - ' . $school_details['country'];
  }

  // Update post title
  $content = array(
		'ID' => $post_id,
		'post_title' => $post_title,
		'post_content' => ''
	);

	// if( isset( $_POST['csf_action'] ) && $_POST['csf_action'] == 'submit' &&!is_admin() ) {
	// 	$content['post_status'] = 'pending';
	// }

	wp_update_post($content);

	// // set the features image
	// $material = get_field( 'materials_&_short_explanation', $post_id );
	// $photo_and_video = $material['photo_and_video'];
	// $upload_images = $photo_and_video['upload_images'];
  //
	// if ( !empty( $upload_images ) ) {
	// 	set_post_thumbnail( $post_id, $upload_images[0]['ID'] );
	// }

}
