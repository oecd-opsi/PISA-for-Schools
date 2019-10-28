<?php

/**
 * Forums Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_forums_loop' ); ?>


<ul id="forums-list-<?php bbp_forum_id(); ?>" class="bbp-forums">

	<li class="bbp-header">

		<ul class="forum-titles">
			<li class="bbp-forum-info"><?php _e( 'Category', 'bbpress' ); ?></li>
			<li class="bbp-forum-topic-count"><?php _e( 'Topics', 'bbpress' ); ?></li>
			<li class="bbp-forum-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'bbpress' ) : _e( 'Posts', 'bbpress' ); ?></li>
			<li class="bbp-forum-freshness"><?php _e( 'Latest posts', 'bbpress' ); ?></li>
		</ul>

	</li><!-- .bbp-header -->
	<p class="subheader">Click the forum headings below to join the discussions.</p>

	<li class="bbp-body">

		<div class="forums-wrapper">

		<?php while ( bbp_forums() ) : bbp_the_forum(); ?>

			<?php bbp_get_template_part( 'loop', 'single-forum' ); ?>

		<?php endwhile; ?>

		</div>

		<div class="main-forum-sidebar">
		<?php
			// display recent replies
			echo do_shortcode('[bbpress_recent_replies_by_topic show=5 include_empty_topics=true]');

			// display Forum widget area
			if ( is_active_sidebar( 'forum_top' ) ) :

				echo '<div id="forum-top-widget-area" class="widget-area" role="complementary">';
					dynamic_sidebar( 'forum_top' );
				echo '</div><!-- #primary-sidebar -->';

			endif;
		?>
		</div>

	</li><!-- .bbp-body -->

	<li class="bbp-footer">

		<div class="tr">
			<p class="td colspan4">&nbsp;</p>
		</div><!-- .tr -->

	</li><!-- .bbp-footer -->

</ul><!-- .forums-directory -->

<?php do_action( 'bbp_template_after_forums_loop' ); ?>
