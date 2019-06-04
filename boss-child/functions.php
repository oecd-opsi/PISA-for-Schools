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

// Redirect to dedicated home page for logged in users
add_action ( 'template_redirect', 'bs_redirect_homepage' );
function bs_redirect_homepage(){
  if ( is_user_logged_in() && is_front_page() ) {
    wp_redirect('/logged-in-users-home/') ;
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

//* Add buttons in forum details area to sort topics
function bs_add_sort_btns() {

	echo '<a id="bs-sort-by-freshness" class="button" href="?sort=latest">' . __( 'Sort by Latest', 'bs-pisa' ) . '</a>';
	echo '<a id="bs-sort-by-posts" class="button" href="?sort=top">' . __( 'Sort by Top', 'bs-pisa' ) . '</a>';

}
add_action( 'bs_forum_details', 'bs_add_sort_btns' );
