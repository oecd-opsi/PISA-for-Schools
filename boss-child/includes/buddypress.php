<?php
function bp_custom_get_send_private_message_link($to_id, $subject=false, $message=false) {

	//if user is not logged, do not prepare the link
	// if ( !is_user_logged_in() )
	// return false;

	$compose_url= bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?';
	if($to_id) {
		$compose_url.=( 'r=' . bp_core_get_username( $to_id ) );
	}
	if($subject) {
		$compose_url.=( '&subject='.$subject );
	}
	if($message) {
		$compose_url.=( '&content='.$message );
	}

	return wp_nonce_url( $compose_url ) ;
}
