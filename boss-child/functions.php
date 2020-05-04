<?php
/**
 * @package Boss Child Theme
 * The parent theme functions are located at /boss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since Boss Child Theme 1.0.0
 */
function boss_child_theme_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'boss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'boss' instances in all child theme files to 'boss_child_theme'.
  // load_theme_textdomain( 'boss_child_theme', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'boss_child_theme_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function boss_child_theme_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/custom.css' );

  /*
   * Scripts
   */
  wp_enqueue_script( 'boss-child-custom-script', get_stylesheet_directory_uri() . '/js/custom.js', 'jquery', '1.0.0', true );

  // Case study form script and style
  if ( is_page( 'case-study-form' ) ) {
    wp_enqueue_style( 'animate-css', get_stylesheet_directory_uri().'/css/animate.css' );
    wp_enqueue_script( 'case-form-script', get_stylesheet_directory_uri() . '/js/case-form.js', 'jquery', '1.0.0', true );
  }

  // jVectorMap
  if ( is_post_type_archive( 'case' ) ) {
    wp_enqueue_style( 'jvectormap-css', get_stylesheet_directory_uri().'/css/jquery-jvectormap-2.0.3.css' );
    wp_enqueue_script( 'jvectormap-script', get_stylesheet_directory_uri() . '/js/jquery-jvectormap-2.0.3.min.js', 'jquery', '1.0.0', false );
    wp_enqueue_script( 'jvectormap-worldmap-script', get_stylesheet_directory_uri() . '/js/jquery-jvectormap-world-mill.js', 'jquery', '1.0.0', false );
  }

  // Country phone prefix script for register page
  if ( is_page( 'register' ) ) {
    wp_enqueue_script( 'country-prefixes', get_stylesheet_directory_uri() . '/js/country-prefixes.js', 'jquery', '1.0.0', false );
  }

}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

// Add logged-out class to body
add_filter('body_class','bs_class_names');
function bs_class_names($classes) {
  if (! ( is_user_logged_in() ) ) {
    $classes[] = 'logged-out';
  }
  return $classes;
}

// Add is-forum class to forum pages body element
add_filter('body_class','bs_forum_pages');
function bs_forum_pages($classes) {
  if ( is_bbpress() || is_page( 1657 ) ) {
    $classes[] = 'is-forum';
  }
  return $classes;
}

// Redirect home to forum page for logged in users
add_action ( 'template_redirect', 'bs_redirect_homepage' );
function bs_redirect_homepage(){
  if ( is_user_logged_in() && is_front_page() ) {
    wp_redirect('/forum/') ;
    exit();
  }
}

// Redirect not logged users to home if trying to access a private page
add_action ( 'template_redirect', 'bs_redirect_visitors_to_homepage' );
function bs_redirect_visitors_to_homepage(){
  if ( !is_user_logged_in() && !is_front_page() && !is_page( array( 2425, 1387, 2422, 2419, 1690, 1653, 2507 ) ) && ! is_post_type_archive( 'bp_doc' ) && ! is_page( 'lostpassword' ) && ! is_page( 'resetpass' ) &&
  strpos($_SERVER['REQUEST_URI'], 'activate') === false  ) {
    wp_redirect( home_url() ) ;
    exit();
  }
}

// Redirect logged users from registration/login pages to forum
add_action('template_redirect','bs_redirect_logged_user');
function bs_redirect_logged_user(){
  $user = wp_get_current_user();
  if ( is_user_logged_in() && is_page( array( 2425, 1387 ) ) ) {
    if ( in_array( 'administrator', $user->roles ) ) {
      wp_redirect( home_url( '/wp-admin/' ) );
    } else {
      wp_redirect( home_url( '/forum/' ) );
    }
    exit();
  }
}

// Redirect to custom login page
add_action('init','bs_custom_login');
function bs_custom_login(){
  global $pagenow;
  if( 'wp-login.php' == $pagenow && ! is_user_logged_in() ) {
    wp_redirect( home_url( '/login/' ) );
    exit();
  }
}

//* Add buttons in forum details area to sort topics and tag selector to filter topics
function bs_add_sort_btns( $forum_id ) {

  // Sort buttons
	echo '<a id="bs-sort-by-freshness" class="button" href="?sort=latest">' . __( 'Sort by Latest', 'bs-pisa' ) . '</a>';
	echo '<a id="bs-sort-by-posts" class="button" href="?sort=top">' . __( 'Sort by Top', 'bs-pisa' ) . '</a>';

  // Tag filter
  $query = bbp_get_all_child_ids( $forum_id, 'topic' ); // get topic related to current forum
  $tags_array = array(); // create empty tags array

  foreach ($query as $topic) { // loop query to populate tags array
    $topic_tags = get_the_terms( $topic, 'topic-tag' );
    foreach ($topic_tags as $tag) {
      $tags_array[$tag->slug] =  $tag->name;
    }
  }
  asort( $tags_array ); // sort alphabetically the tags array

  $select_markup = '<label for="tag-filter">Filter by tag: </label><select id="tag-filter"><option value="" data-redirect="' . get_permalink($forum_id) . '">No filter</option>'; // start creating select markup
  foreach ($tags_array as $key => $value) {
    $select_markup .= '<option value="' . $key . '" data-redirect="' . get_permalink($forum_id) . '?topictag=' . $key . '">' . $value . '</option>';
  }
  $select_markup .= '</select>';

  echo $select_markup;

}
add_action( 'bs_forum_details', 'bs_add_sort_btns', 10, 1 );

// Filter activity text if it's about a new poll
function bs_activity_topic_text_for_poll( $activity_text, $user_id, $topic_id, $forum_id ) {

  $poll_id = get_post_meta($topic_id, '_bbp_topic_poll_id', true);

  if ( $poll_id > 0 ) {

    $poll_question = get_the_title( $poll_id );

    $activity_text = str_replace( 'started the topic', 'started the poll <strong>' . $poll_question . '</strong> in the topic', $activity_text);

    return $activity_text;


  } else {

    return $activity_text;

  }


}
add_filter( 'bbp_activity_topic_create', 'bs_activity_topic_text_for_poll', 40, 4);

/*
 * Get the most recently replied-to topics, and their most recent reply
 * from https://www.daggerhart.com/bbpress-recent-replies-shortcode/
 */
function custom_bbpress_recent_replies_by_topic($atts){
  $short_array = shortcode_atts(array('show' => 5, 'forum' => false, 'include_empty_topics' => false), $atts);
  extract($short_array);

  // default values
  $post_types = array('reply');
  $meta_key = '_bbp_last_reply_id';

  // allow for topics with no replies
  if ( $include_empty_topics ) {
    $meta_key = '_bbp_last_active_id';
    $post_types[] = 'topic';
  }

  // get the 5 topics with the most recent replies
  $args = array(
    'posts_per_page' => $show,
    'post_type' => array('topic'),
    'post_status' => array('publish'),
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'meta_key' => $meta_key,
  );
  // allow for specific forum limit
  if ($forum){
    $args['post_parent'] = $forum;
  }

  $query = new WP_Query($args);
  $reply_ids = array();

  // get the reply post->IDs for these most-recently-replied-to-topics
  while($query->have_posts()){
    $query->the_post();
    if ($reply_post_id = get_post_meta(get_the_ID(), $meta_key, true)){
      $reply_ids[] = $reply_post_id;
    }
  }
  wp_reset_query();

  // get the actual replies themselves
  $args = array(
    'posts_per_page' => $show,
    'post_type' => $post_types,
    // 'post__in' => $reply_ids,
    'orderby' => 'date',
    'order' => 'DESC'
  );

  $query = new WP_Query($args);
  ob_start();
    // loop through results and output our rows
    while($query->have_posts()){
      $query->the_post();

      // custom function for a single reply row
      custom_bbpress_recent_reply_row_template( $query->current_post + 1 );
    }
    wp_reset_query();

  $output = '<ul class="latest-replies-list">' . ob_get_clean() . '</ul>';
  return $output;
}
add_shortcode('bbpress_recent_replies_by_topic', 'custom_bbpress_recent_replies_by_topic');
/*
 * Executed during our custom loop
 *  - this should be the only thing you need to edit
 */
function custom_bbpress_recent_reply_row_template( $row_number ){

  // get the reply title
  $title = get_the_title();
  // $title = substr( $title, 0, 55); // trim title to specific number of characters (55 characters)
  // $title = wp_trim_words( $title, 5, '...'); // trim title to specific number of words (5 words)...

  // get the excerpt
  $excerpt = get_the_excerpt();
  $excerpt = substr( $excerpt, 0, 80); // trim excerpt to specific number of characters (136 characters)

  // get belonging forum
  $parent = array_reverse( get_post_ancestors( get_the_ID()) );
  $first_parent = get_page( $parent[0] );
  $parent_forum_ID = apply_filters('the_ID', $first_parent->ID);
  $parent_forum_title = bbp_get_forum_title( $parent_forum_ID );
  $parent_forum_url = bbp_get_forum_permalink( $parent_forum_ID );

  // determine if odd or even row
  $row_class = ($row_number % 2) ? 'odd' : 'even';
  ?>
    <li class="bbpress-recent-reply-row <?php print $row_class; ?>">
      <!-- <div class="recent-replies-avatar"><?php // echo get_avatar( get_the_author_meta( 'ID' ) ); ?></div> -->
      <div class="recent-replies-body">
        <div class="recent-replies-author"><?php the_author(); ?></div>
        <div class="recent-replies-title"><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></div>
        <div class="recent-replies-excerpt"><?php echo $excerpt; ?>...</div>
        <div class="recent-replies-forum"><a href="<?php echo $parent_forum_url ?>">In <?php echo $parent_forum_title ?></a></div>
      </div>
      <!-- <div>Link To Reply: <a href="<?php //the_permalink(); ?>">view reply</a></div> -->
      <!-- <div>Link To Topic#Reply: <a href="<?php //print get_permalink( get_post_meta( get_the_ID(), '_bbp_topic_id', true) ); ?>#post-<?php //the_ID(); ?>">view reply</a></div> -->
      <!-- <div>Link To Topic/page/#/#Reply: <a href="<?php //bbp_reply_url( get_the_ID() ); ?>">view reply paged</a></div> -->
      <div class="recent-replies-time-diff"><?php print human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></div>
    </li>
  <?php

  // Refs
  // http://codex.wordpress.org/Template_Tags#Post_tags
  // http://codex.wordpress.org/Function_Reference/get_avatar
  // http://codex.wordpress.org/Function_Reference/human_time_diff
  // (template tags for bbpress)
  // https://bbpress.trac.wordpress.org/browser/trunk/src/includes/users/template.php
  // https://bbpress.trac.wordpress.org/browser/trunk/src/includes/replies/template.php
}
// allow shortcodes to run in widgets
add_filter( 'widget_text', 'do_shortcode');
// don't auto-wrap shortcode that appears on a line of it's own
add_filter( 'widget_text', 'shortcode_unautop');

// Add Case study form widget area
function bs_case_form_widgets_init() {

	register_sidebar( array(
		'name'          => 'Case study form',
		'id'            => 'case_study_form',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widgettitle widget-title">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'bs_case_form_widgets_init' );

require_once('includes/acf-functions.php');
require_once('includes/buddypress.php');

// Shortcode to get Country term ISO code
function country_term_iso() {

  global $post;

  $terms = get_the_terms( $post->ID, 'country' );

  return $ISO = get_field( 'iso_code', $terms[0] );

}
add_shortcode( 'country-iso', 'country_term_iso' );

// // Shortcode to display Case Studies map
function case_map_func() {

  // init output
  $output = '<div id="world-map" style="height: 400px;"></div>';

  // get Country terms and build array
  $terms = get_terms( array(
    'taxonomy'    => 'country',
    'hide_empty'  => false,
  ) );

  $output .= '<script>var countriesData = {';

  foreach ($terms as $term) {
    $iso = get_field( 'iso_code', $term );
    $count = $term->count;
    if ( $iso && $count > 0 )
      $output .= '"' . $iso . '": ' . $count . ',';
  }

  $output .= '};';

  // jvectorMap creation
  $output .= "$('#world-map').vectorMap({
    map: 'world_mill',
    backgroundColor: '#042C41',
    regionStyle: {
      initial: {
        fill: 'white',
        'fill-opacity': .95,
      },
      hover: {
        'fill-opacity': 1
      },
      selected: {
        fill: 'yellow'
      },
      selectedHover: {}
    },
    series: {
      regions: [{
        values: countriesData,
        scale: ['#c6da50', '#00ae88'],
        normalizeFunction: 'linear'
      }]
    },
    onRegionTipShow: function(e, el, code) {
      if (countriesData[code] > 0) {
        el.html('<div class=\"map-tips\">' + el.html() + ' (N. of cases: ' + countriesData[code] + ')</div>');
      } else {
        el.html('<div class=\"map-tips\">' + el.html() + ' (No cases submitted)</div>');
      }
    }
  });";

  $output .= '</script>';

  return $output;

}
add_shortcode( 'case-map', 'case_map_func');

// Return true if user can edit form
function can_edit_acf_form( $post_id = 0, $user_id = 0, $allowed_statuses = array( 'draft', 'pending', 'archive', 'reviewed' ) ) {

	if ( intval( $user_id ) == 0 && get_current_user_id() > 0 ) {

		$user_id = get_current_user_id();

	}

	if ( intval( $post_id ) == 0 ) {

		global $post;

		if ( !empty( $post ) ) {
			$post_id = $post->ID;
		}

	}


	if ( intval( $post_id ) > 0 && intval( $user_id ) > 0 ) {

		$post_author = get_post_field( 'post_author', $post_id );
		$post_status = get_post_field( 'post_status', $post_id );

		if ( intval( $post_author ) == $user_id && ( in_array( $post_status, $allowed_statuses ) || $allowed_statuses[0] == 'any' ) ) {
			return true;
		}

	}

	return false;

}

// Profile link shortcode
function profilelink_func ( $atts ) {

	$a = shortcode_atts( array(
        'user_id' => '',
        'text' => '',
        'class' => '',
    ), $atts );

	if ( intval( $a['user_id'] ) > 0 ) {
		return '<a href="'. bp_core_get_user_domain( $user_id ) .'case-studies/" class="'. $a['class'] .'" >'. $a['text'] .'</a>';
	} else {
		$user = wp_get_current_user();
		return '<a href="'. bp_core_get_user_domain( $user->ID ) .'case-studies/" class="'. $a['class'] .'" >'. $a['text'] .'</a>';
	}
}
add_shortcode( 'profilelink', 'profilelink_func' );

// Chck if user can delete case study
function can_delete_cs( $post_id = 0, $user_id = 0 ) {

  // if no user ID is declared, get current user
  if ( intval( $user_id ) == 0 && get_current_user_id() > 0 ) {
		$user_id = get_current_user_id();
	}

  // if no post ID is declared, get current post
	if ( intval( $post_id ) == 0 ) {

		global $post;

		if ( !empty( $post ) ) {
			$post_id = $post->ID;
		}

	}

	if ( intval( $post_id ) > 0 && intval( $user_id ) > 0 ) {

		$post_author = get_post_field( 'post_author', $post_id );
		$post_status = get_post_field( 'post_status', $post_id );

		if ( intval( $post_author ) == $user_id ) {
			if ( $post_status == 'draft' ) {
				return 'delete';
			}
			if ( $post_status == 'pending' || $post_status == 'publish' || $post_status == 'reviewed' ) {
				return 'request';
			}
		}

	}

	return false;

}

// Add Case studies custom post statuses
function bs_case_studies_custom_post_statuses() {

  register_post_status( 'pending_deletion', array(
		'label'                     => _x( 'Pending Deletion', 'bs_pisa' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Pending Deletion <span class="count">(%s)</span>', 'Pending Deletion <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'reviewed', array(
		'label'                     => _x( 'Reviewed – Not Currently Published', 'bs_pisa' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Reviewed <span class="count">(%s)</span>', 'Reviewed <span class="count">(%s)</span>' ),
	) );

}
add_action( 'init', 'bs_case_studies_custom_post_statuses' );
add_action('admin_footer-post-new.php', 'opsi_append_post_status_list');
add_action('admin_footer-post.php', 'opsi_append_post_status_list');
function opsi_append_post_status_list(){
  global $post;
  $complete = '';
  $label = '';
  if($post->post_type == 'case'){

  $complete = '';
      if($post->post_status == 'pending_deletion'){
           $complete = ' selected=\'selected\'';
           $label = '<span id="post-status-display"> '. __('Pending Deletion', 'opsi') .'</span>';
      }
  echo '
  <script>
  jQuery(document).ready(function($){
     $("select#post_status").append("<option value=\'pending_deletion\' '.$complete.'>'. __('Pending Deletion', 'opsi') .'</option>");
     $(".misc-pub-section label").append("'.$label.'");
  });
  </script>
  ';

  $complete = '';
      if($post->post_status == 'reviewed'){
           $complete = ' selected=\'selected\'';
           $label = '<span id="post-status-display"> '. __('Reviewed', 'opsi') .'</span>';
      }
  echo '
  <script>
  jQuery(document).ready(function($){
     $("select#post_status").append("<option value=\'reviewed\' '.$complete.'>'. __('Reviewed', 'opsi') .'</option>");
     $(".misc-pub-section label").append("'.$label.'");
  });
  </script>
  ';

  }
}

function opsi_custom_status_add_in_quick_edit() {
	echo "<script>
	jQuery(document).ready( function() {
		jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"pending_deletion\">". __('Pending Deletion', 'opsi') ."</option>' );
		jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"reviewed\">". __('Reviewed', 'opsi') ."</option>' );
	});
	</script>";
}
add_action('admin_footer-edit.php','opsi_custom_status_add_in_quick_edit');

function opsi_display_archive_state( $states ) {
   global $post;
   $arg = get_query_var( 'post_status' );

   if($arg == 'pending_deletion'){
    if($post->post_status == 'pending_deletion'){
      echo  ' - '.__('Pending Deletion', 'opsi');
    }
   }
   if($arg == 'reviewed'){
    if($post->post_status == 'reviewed'){
      echo  ' - '.__('Reviewed', 'opsi');
    }
   }
  return $states;
}
add_filter( 'display_post_states', 'opsi_display_archive_state' );

// Admin notice about pending users and cases
function bs_pisa_admin_notice() {

  // Get pending Cases
  $args = array(
		'fields'		=> 'ids',
		'post_type'		=> 'case',
		'post_status'	=> 'pending',
		'posts_per_page' => -1,
	);

	$query = new WP_Query( $args );
	if ( $query->post_count > 0 ) {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><a href="<?php admin_url(); ?>edit.php?post_status=pending&post_type=case"><strong><?php echo sprintf( __( 'There are %d pending Case Studies', 'bs_pisa' ), $query->post_count ); ?></strong></a></p>
    </div>
    <?php
	}


	// Get the total number of users for the current query. I use (int) only for sanitize.
	$users_count = count( get_users( array( 'fields' => array( 'ID' ), 'role' => 'pending' ) ) );
	// Echo a string and the value
	if ( $users_count > 0 ) {
	?>
	<div class="notice notice-warning is-dismissible">
        <p><a href="<?php admin_url(); ?>users.php?role=pending"><strong><?php echo sprintf( __( 'There are %d Pending Users', 'bs_pisa' ), $users_count ); ?></strong></a></p>
    </div>
	<?php
	}

  // Get the total number of users for the current query. I use (int) only for sanitize.
	$users_count = count( get_users( array( 'fields' => array( 'ID' ), 'role' => 'pending' ) ) );
	// Echo a string and the value
	if ( $users_count > 0 ) {
	?>
	<div class="notice notice-warning is-dismissible">
        <p><a href="<?php admin_url(); ?>users.php?role=pending"><strong><?php echo sprintf( __( 'There are %d Pending Users', 'opsi' ), $users_count ); ?></strong></a></p>
    </div>
	<?php
	}

}
add_action( 'admin_notices', 'bs_pisa_admin_notice', 100 );

// Force login for case study form
add_action( 'template_redirect', 'case_study_form_template_redirect' );
function case_study_form_template_redirect() {
	if( is_page( 2198 ) && !is_user_logged_in() ) {
		wp_redirect( wp_login_url( get_permalink( get_page_by_path( 2198 ) ) ) );
		die;
	}
}

// Send an email to user when a case study is published
add_action( 'pending_to_publish', 'bs_case_study_on_publish_post', 10, 1 );
function bs_case_study_on_publish_post( $post ) {

	if ( 'case' === get_post_type() ) { // check the custom post type

		$cstitle   	= get_the_title( $post->ID );
		$cslink   	= get_the_permalink( $post->ID );

		$author_mail 		= get_the_author_meta( 'user_email', $post->post_author );
		$author_fname 		= get_the_author_meta( 'first_name', $post->post_author );
		$author_lname 		= get_the_author_meta( 'last_name', $post->post_author );
		$author_fullname 	= $author_fname.' '. $author_lname;

    $subject = get_field( 'author_published_notification_subject', 'option' );
    $mail_content = get_field( 'author_published_notification_mail', 'option' );
		$body    = str_replace( array( '%authorname%', '%casestudylink%', '%casestudytitle%' ), array( $author_fullname, $cslink, $cstitle ), $mail_content );

		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail( $author_mail, $subject, $body, $headers );


		// create notification for buddypress
		bp_notifications_add_notification( array(
			'user_id'           => $post->post_author,
			'item_id'           => $post->ID,
			'component_name'    => 'innovations',
			'component_action'  => 'innovations_notification_action',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		) );

	}
}

// Autopopulate Submission date field on Case studies submission
add_action( 'draft_to_pending', 'bs_submission_date');
function bs_submission_date( $post ) {

  // check if post is a Case Study
  if ( ! 'case' == $post->post_type )
    return;

  // get current date and populate Submission field
  $now = date( 'd/m/Y' );
  update_field( 'submission_date', $now, $post->ID );

}

// Automatically add user to their country group
function add_user_to_country_group( $user_id, $role, $old_roles ) {

  if( !$user_id ) return false;
  // get country field value
  $country = xprofile_get_field_data( 15, $user_id );
  // slugify country name
  $country_slug = strtolower( str_replace( " ", "-", $country ) );
  // get group ID by slug
  $group_id = BP_Groups_Group::group_exists( $country_slug );
  // add user to group
  if ( $group_id ) {
    groups_join_group( $group_id, $user_id );
  }

}
add_action( 'set_user_role', 'add_user_to_country_group', 30, 3 );

// give to ambassador the keymaster forum role
// function make_ambassador_keymaster() {
//   $user = wp_get_current_user();
//   $roles_array = array( 'ambassador' );
//   if( array_intersect( $roles_array, $user->roles ) ) {
//   // bbp_add_forums_roles();
//     bbp_set_user_role( $user->ID, 'bbp_keymaster' );
//   }
// }
// add_action( 'init', 'make_ambassador_keymaster' );

// Enable visual editor in bbPress
function bbp_enable_visual_editor( $args = array() ) {
  if ( !is_page( 'register' )) {
    $args['tinymce'] = true;
    $args['quicktags'] = false;
  }
  return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

// Enable mention autocomplete in bbPress
function buddydev_enable_mention_autosuggestions_on_compose( $load, $mentions_enabled ) {
	if ( ! $mentions_enabled ) {
		return $load; //activity mention is  not enabled, so no need to bother
	}
	//modify this condition to suit yours
	if( is_user_logged_in() ) {
		$load = true;
	}

	return $load;
}
add_filter( 'bp_activity_maybe_load_mentions_scripts', 'buddydev_enable_mention_autosuggestions_on_compose', 10, 2 );

// Disallow user to edit role profile field after submission
function bs_hide_profile_edit( $retval ) {
	// remove field from edit tab
	if(  bp_is_profile_edit() ) {
		$retval['exclude_fields'] = '6'; // ID's separated by comma
	}
	// allow field on registration page
	if ( bp_is_register_page() ) {
		$retval['include_fields'] = '6'; // ID's separated by comma
	}

	// hide the filed on profile view tab
	// if ( $data = bp_get_profile_field_data( 'field=6' ) ) :
	// 	$retval['exclude_fields'] = '6'; // ID's separated by comma
	// endif;

	return $retval;
}
add_filter( 'bp_after_has_profile_parse_args', 'bs_hide_profile_edit' );

// Add country and school name as users admin columns
function bs_modify_user_table( $columns ) {
	return array_merge( $columns,
            array(
							'school' => __('School'),
							'country' => __('Country'),
						 )
				  );
}
add_filter( 'manage_users_columns', 'bs_modify_user_table' );

function bs_modify_user_table_row( $value, $column_name, $user_id ) {
  switch ($column_name) {
    case 'school' :
      return bp_get_profile_field_data (
        array(
          'field' => 10,
          'user_id' => $user_id,
        )
      );
      break;
    case 'country' :
      return bp_get_profile_field_data (
        array(
          'field' => 15,
          'user_id' => $user_id,
        )
      );
      break;
    default:
  }
  return $value;
}
add_filter( 'manage_users_custom_column', 'bs_modify_user_table_row', 10, 3 );

// Add country and school name as users admin columns in Manage signups page
function bs_modify_user_columns($column_headers) {

  $column_headers['school'] = __('School');
  $column_headers['country'] = __('Country');

  return $column_headers;
}
add_action('manage_users_page_bp-signups_columns','bs_modify_user_columns');

function bs_signup_custom_column( $str, $column_name, $signup_object ) {
  switch ($column_name) {
    case 'school' :
      return $signup_object->meta['field_10'];
      break;
    case 'country' :
      return $signup_object->meta['field_15'];
      break;
    default:
  }
  return $str;
}
add_filter( 'bp_members_signup_custom_column', 'bs_signup_custom_column', 1, 3 );

// Disable activation email
// function disable_activation_email() {
//   return false;
// }
// add_filter( 'bp_core_signup_send_activation_key', 'disable_activation_email' );

// Disable tinyMCE in registration page
function bs_remove_rich_text_registration_fields( $field_id = null ) {
    if ( ! $field_id ) {
      $field_id = bp_get_the_profile_field_id( '5' );
    }
    $field = xprofile_get_field( $field_id );
    if ( $field ) {
      $enabled = false;
    }
}
add_filter( 'bp_xprofile_is_richtext_enabled_for_field', 'bs_remove_rich_text_registration_fields' );

// Add reply button after post in forum
add_filter( 'bbp_get_reply_content', 'bs_reply_button', 91 );
function bs_reply_button( $content ) {
  echo $content . '<div class="bs-reply-btn-wrapper"><a href="#new-post" class="button">Reply</a></div>';
}

// Case study status shortcode
function bs_casestudystatus( $output = '', $atts, $instance ) {


	$atts = shortcode_atts( array(
		'id'         => ( ( isset( $_GET['edit'] ) && intval( $_GET['edit'] ) > 0 ) ? intval( $_GET['edit'] ) : 0 ),
	), $atts, 'case-study-status' );


	if ( $atts['id'] == 0 ) {
		return '<h3 class="status_header">'. __( 'Status', 'bs_pisa' ) .'</h3>'.__( 'Not yet saved', 'bs_pisa' );
	}
	if ( isset( $_GET['edit'] ) && intval( $_GET['edit'] ) > 0 && !can_edit_acf_form( intval( $_GET['edit'] ) ) ) {
		return;
	}

	$post_status = get_post_status_object ( get_post_status( $atts['id'] ) );

	$status = $post_status->label;

	if ( !$post_status->name ) {
		return '<h3 class="status_header">'. __( 'Status', 'bs_pisa' ) .'</h3>'.__( 'Not yet submitted', 'bs_pisa' );
	}

	$last_save = get_the_modified_date( get_option('date_format') .', '. get_option('time_format'). ' a', $atts['id'] );

	if ( $post_status ) {
		$return = '<h3 class="status_header">'. __( 'Status', 'bs_pisa' ) .'</h3><p>'.$status .'<br />'. __( 'Last saved:', 'bs_pisa' ) .' '. $last_save .'</p>';
    if( $post_status = 'draft') {
      $return .= '<a href="/?post_type=case&p=' . $atts['id'] . '">Preview this case study</a>';
    }
    return $return;
	}

	return;
}
add_shortcode( 'case-study-status', 'bs_casestudystatus', 100, 3 );

// Notify approved user
add_action ( 'set_user_role', 'bs_notify_pending_user', 10, 3 );
function bs_notify_pending_user( $user_id, $role, $old_roles ) {

	if ( !in_array( 'pending', $old_roles ) ) {
		return;
	}

	if ( $role && $role != 'pending' ) {

		// notify the user
		$subject = get_field( 'user_approved_notification_subject', 'option' );
		$body    = get_field( 'user_approved_notification_mail', 'option' );
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( get_the_author_meta( 'user_email', $user_id ), $subject, $body, $headers );

	}

}

// Add text on the top of Docs page
function bs_docs_before_doc_header() {
  echo '<p class="pisa-library-intro">This library contains short video case studies, webinar recordings, best practices and analysis. If you have content that may be of use to other members, please add it here by clicking on Create New Doc.</p>';
}
add_action( 'bp_docs_before_doc_header', 'bs_docs_before_doc_header' );

// Show most recent replies first in Topic view
function change_reply_order() {
  $args['order'] = 'DESC';
  return $args;
}
add_filter('bbp_before_has_replies_parse_args', 'change_reply_order');

// Remove user activation
add_filter( 'bp_registration_needs_activation', '__return_false' );

// Author avatar shortcode
function author_avatar() {
  global $post;
  return bp_core_fetch_avatar( array(
    'item_id'   => $post->post_author,
    'object'    => 'user',
    'width'     => 50,
    'height'    => 50,
  ) );
}
add_shortcode( 'author-avatar', 'author_avatar' );
