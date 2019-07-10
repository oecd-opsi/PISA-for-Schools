<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Boss
 */

?>

<?php
// Sort query if a GET parameter is set
if ( isset( $_GET['sort'] ) ) {

  $sort_type = $_GET['sort'];

	if ( 'top' == $sort_type ) {

		function bs_posts_topic_order( $args ) {
      $args['orderby'] = bbp_show_lead_topic() ? bbp_get_topic_reply_count() : bbp_get_topic_post_count();
      $args['order']='ASC';
      return $args;
		}
		add_filter('bbp_before_has_topics_parse_args','bs_posts_topic_order');

	}
}

// Filter query by topic tag if a parameter is set
if ( isset( $_GET['topictag'] ) ) {

	function bs_filter_topics_by_tag( $args ) {

		$tag = $_GET['topictag'];

		$args['tax_query'] = array(
	      array(
	        'taxonomy' => 'topic-tag',
	        'field' => 'slug',
	        'terms' => $tag
	      )
	    );

		return $args;

	}
	add_filter('bbp_before_has_topics_parse_args','bs_filter_topics_by_tag');

}
?>

<div id="bbpress-forums">

	<?php do_action( 'bbp_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

      <div class="bbp-forum-breadcrumb"><?php bbp_breadcrumb(); ?></div>

        <div class="bbp-forum-details">
            <div class="table-cell">
                <?php bbp_forum_subscription_link(); ?>
								<?php do_action( 'bs_forum_details', bbp_get_forum_id() ); ?>
            </div>
            <?php buddyboss_bbp_single_forum_description(array('before'=>'<div class="bbp-forum-data">', 'after'=>'</div>')); ?>
        </div>

		<?php if ( bbp_has_forums() ) : ?>

			<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php elseif ( !bbp_is_forum_category() ) : ?>

			<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_forum' ); ?>

</div>
