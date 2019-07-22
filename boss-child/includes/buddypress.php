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

/************** ADD Case Study SubTab  START ************/

function nitro_get_user_posts_count($user_id,$args ) {
  $args['author'] 		= $user_id;
  $args['fields'] 		= 'ids';
  $args['posts_per_page'] = -1;
  $ps = get_posts($args);
  return count($ps);
}

add_action( 'bp_setup_nav', 'profile_tab_innovations' );
function profile_tab_innovations() {

	global $bp;

	$user_id = bp_displayed_user_id();
	$current_user_id = bp_loggedin_user_id();

	$count_args_owner = array(
		'post_type' => array ( 'case' ),
		'post_status' => array( 'any', 'archive', 'pending_deletion', 'reviewed' )
	);
	$count_args_guest = array(
		'post_type' => array ( 'case' ),
		'post_status' => array( 'publish' )
	);

	$all_posts = nitro_get_user_posts_count( $user_id, $count_args_owner );
	$published_posts = nitro_get_user_posts_count( $user_id, $count_args_guest );

	$count_inno = 0;

	if ( $user_id == $current_user_id ) {
		$count_inno = $all_posts;
	} else {
		$count_inno = $published_posts;
	}

	$innonum = '';

	if ( $count_inno == 0 ) {
		$innonum = ' <span class="no-count">'. $count_inno .'</span>';
	} else {
		$innonum = ' <span class="count">'. $count_inno .'</span>';
	}

  bp_core_new_nav_item( array(
    'name' => __( 'Case studies', 'bs_pisa' ).$innonum,
    'slug' => 'case-studies',
    'screen_function' => 'bs_my_case_studies_screen',
    'position' => 70,
    'parent_url'      => bp_loggedin_user_domain() . '/case-studies/',
    'parent_slug'     => $bp->profile->slug,
    'default_subnav_slug' => 'case-studies'
  ) );
}


function bs_my_case_studies_screen(){
    global $bp;
    add_action( 'bp_template_title', 'bs_my_case_studies_screen_title' );
    add_action( 'bp_template_content', 'bs_my_case_studies_screen_content' );
	// add_filter( 'bp_get_template_part', 'bp_innovations_template_part_filter', 10, 3 );
    bp_core_load_template( array ( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) ) );
}

function bp_innovations_template_part_filter( $templates, $slug, $name ) {

	if ( 'members/single/activity' != $slug ) {
		return $templates;
	}
	return bp_get_template_part( 'members/single/plugins' );
    // bp_core_load_template( array ( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) ) );
}

function bs_my_case_studies_screen_title() {
  // global $bp;
	// echo __( 'Innovations', 'bs_pisa' );
	return;
}

function bs_my_case_studies_screen_content() {

	global $bp;

	/**
	 * Fires before the display of the member activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_member_activity_post_form' );

	// if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) )
	// 	bp_get_template_part( 'activity/post-form' );

	/**
	 * Fires after the display of the member activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_activity_post_form' );

  echo bp_get_author_case_studies_list();

}

// build author Innovations / Case Studies list
function bp_get_author_case_studies_list( $user_id = 0 ) {

	if ( $user_id == 0 ) {
    $user_id = bp_displayed_user_id();
  }
  if ( ! $user_id ) {
    return false;
  }

  $current_user_id = bp_loggedin_user_id();

	if ( $user_id == $current_user_id ) {
		return bp_case_studies_list_owner();
	} else {
		return bp_case_studies_list_guest();
	}

}

function bp_case_studies_list_owner() {

	// WP_Query arguments
	$args = array(
		'post_type'		=> array( 'case' ),
		'post_status'	=> array( 'any', 'pending_deletion', 'archive', 'reviewed' ),
		'author'		=> bp_loggedin_user_id(),
		'posts_per_page'=> -1,
	);

	// The Query
	$query = new WP_Query( $args );

	$out  = '';

	ob_start();

	// The Loop
	if ( $query->have_posts() ) {

		?>
		<div class="">
			<table class="">
				<thead>
					<th><?php echo __( 'Title', 'bs_pisa' ); ?></th>
					<th><?php echo __( 'Status', 'bs_pisa' ); ?></th>
					<th class="text-center" colspan="3"><?php echo __( 'Actions', 'bs_pisa' ); ?></th>
				</thead>
				<tbody>

		<?php

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_status_obj = get_post_status_object( get_post_status( get_the_ID() ) );
			?>

			<tr <?php if ( get_post_status( get_the_ID() ) == 'archive' ) { echo ' class="warning archive"'; } ?> >

				<td>
					<?php
					$post_url = get_preview_post_link(get_the_ID());
					$post_url = str_replace( '&preview=true', '', $post_url );
					$post_url = str_replace( '?preview=true', '', $post_url );
					?>
					<a href="<?php echo $post_url ?>">
						<?php the_title(); ?>
					</a>
				</td>

				<td>
					<?php
					if ( get_post_status( get_the_ID() ) == 'pending' ) {
						echo __( 'Submitted (pending review)', 'bs_pisa' );
					} else {
						echo $post_status_obj->label;
					}
					?>
				</td>

				<td>
					<a href="<?php the_permalink(); ?>" title="<?php echo __( 'view', 'bs_pisa' ); ?>">
						<i class="fas fa-search"></i>
					</a>
				</td>

				<td>
					<a href="<?php echo get_the_permalink( 2198 ).'?edit='. get_the_ID(); ?>" title="<?php echo __( 'edit', 'bs_pisa' ); ?>">
						<i class="fas fa-pen-square"></i>
					</a>
				</td>

				<td>
				<?php
				$get_post_status = get_post_status();
				if ( can_delete_cs( get_the_ID(), bp_loggedin_user_id() ) ) : ?>
					<a href="<?php echo get_the_permalink( 2198 ).'?delete='. get_the_ID(); ?>" title="<?php echo __( 'remove', 'bs_pisa' ); ?>" class="danger">
						<i class="fas fa-trash"></i>
					</a>
				<?php endif; ?>
				</td>

			</tr>

			<?php

		}

		?>
				</tbody>
			</table>
		</div>
		<?php

	} else {

		?>
		<div id="message" class="info">
			<p><?php echo __( 'Sorry, there was no entries found.', 'bs_pisa' ); ?></p>
		</div>
		<?php
	}

	// Restore original Post Data
	wp_reset_postdata();

	$out = ob_get_clean();

	return $out;

}

function bp_case_studies_list_guest() {

	// WP_Query arguments
	$args = array(
		'post_type'		=> array( 'case' ),
		'post_status'	=> array( 'publish' ),
		'author'		=> bp_displayed_user_id(),
		'posts_per_page'=> -1
	);

	// The Query
	$query = new WP_Query( $args );

	$out  = '';

	ob_start();

	// The Loop
	if ( $query->have_posts() ) {

		?>
			<ul>

		<?php

		while ( $query->have_posts() ) {
			$query->the_post();
			?>

			<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

			<?php

		}

		?>
			</ul>
		<?php

	} else {

		?>
		<div id="message" class="info">
			<p><?php echo __( 'Sorry, there was no entries found.', 'bs_pisa' ); ?></p>
		</div>
		<?php
	}

	// Restore original Post Data
	wp_reset_postdata();

	$out = ob_get_clean();

	return $out;

}

/************** ADD Case Study SubTab  END ************/
