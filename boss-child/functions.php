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

// Redirect to dedicated home page for logged in users
add_action ( 'template_redirect', 'bs_redirect_homepage' );
function bs_redirect_homepage(){
  if ( is_user_logged_in() && is_front_page() ) {
    wp_redirect('/forum/') ;
    exit();
  }
}

/**
* Redirect buddypress and bbpress pages to registration page
*/
function bs_page_template_redirect_for_not_logged_in_users()
{
  //if not logged in and on a bp page except registration or activation
  if( ! is_user_logged_in() &&
    ( ( ! bp_is_blog_page() && ! bp_is_activation_page() && ! bp_is_register_page() ) || is_bbpress() )
  )
  {
    wp_redirect( home_url( '/register/' ) );
    exit();
  }
}
add_action( 'template_redirect', 'bs_page_template_redirect_for_not_logged_in_users' );

// Redirect to custom login page
// add_action('init','bs_custom_login');
// function bs_custom_login(){
//   global $pagenow;
//   if( 'wp-login.php' == $pagenow && ! is_user_logged_in() ) {
//     wp_redirect('/login/');
//     exit();
//   }
// }

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

// Minify sidenav if forum pages
function bs_minify_sidenav() {

  if ( ! is_admin() && ( is_bbpress() || is_page( 1657 ) ) ) {
    echo '<script>document.addEventListener( "DOMContentLoaded", function(){ document.querySelector("body").classList.remove("left-menu-open"); } );</script>';
  }

}
add_action( 'wp_footer', 'bs_minify_sidenav' );


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
  if ($include_empty_topics) {
    $meta_key = '_bbp_last_active_id';
    $post_types[] = 'topic';
  }

  // get the 5 topics with the most recent replie
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
    'post__in' => $reply_ids,
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
  $title = substr( $title, 0, 55); // trim title to specific number of characters (55 characters)
  $title = wp_trim_words( $title, 5, '...'); // trim title to specific number of words (5 words)...

  // get the excerpt
  $excerpt = get_the_excerpt();
  $excerpt = substr( $title, 0, 136); // trim excerpt to specific number of characters (55 characters)

  // determine if odd or even row
  $row_class = ($row_number % 2) ? 'odd' : 'even';
  ?>
    <li class="bbpress-recent-reply-row <?php print $row_class; ?>">
      <div class="recent-replies-avatar"><?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?></div>
      <!-- <div>Avatar linked to bbPress Profile:<a href="<?php //print esc_url( bbp_get_user_profile_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php //print get_avatar( get_the_author_meta( 'ID' ) ); ?></a></div> -->
      <div class="recent-replies-body">
        <div class="recent-replies-author"><?php the_author(); ?></div>
        <div class="recent-replies-title"><?php echo $title; ?></div>
        <div class="recent-replies-excerpt"><?php echo $excerpt; ?></div>
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
