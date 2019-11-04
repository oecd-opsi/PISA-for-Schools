<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<li class="bbp-forum-info">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>

		<?php do_action( 'bbp_theme_before_forum_title' ); ?>

		<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>

		<?php do_action( 'bbp_theme_before_forum_description' ); ?>

		<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>

		<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

		<?php
		if ( 1781 === bbp_get_forum_id() ) {
			// bbp_list_forums( array(
			// 	'separator' => '',
			// 	'link_before' => '<li class="bbp-forum"><img src="' . get_stylesheet_directory_uri().'/images/flags/'.$sub_forum->ID.'.png' . '" alt="" />',
			// 	'show_topic_count' => false,
      //   'show_reply_count' => false,
			// ) );
			// Define used variables
	    $output = $sub_forums = $topic_count = $reply_count = $counts = '';
	    $i = 0;
	    $count = array();

	    // Parse arguments against default values
	    $r = bbp_parse_args( $args, array(
	        'before'            => '<ul class="bbp-forums-list">',
	        'after'             => '</ul>',
	        'link_before'       => '<li class="bbp-forum">',
	        'link_after'        => '</li>',
	        'count_before'      => ' (',
	        'count_after'       => ')',
	        'count_sep'         => ', ',
	        'separator'         => '',
	        'forum_id'          => '',
	        'show_topic_count'  => false,
	        'show_reply_count'  => false,
	    ), 'list_forums' );

	    // Loop through forums and create a list
	    $sub_forums = bbp_forum_get_subforums( $r['forum_id'] );
	    if ( !empty( $sub_forums ) ) {

        // Total count (for separator)
        $total_subs = count( $sub_forums );
        foreach ( $sub_forums as $sub_forum ) {
          $i++; // Separator count

          // Get forum details
          $count     = array();
          $show_sep  = $total_subs > $i ? $r['separator'] : '';
          $permalink = bbp_get_forum_permalink( $sub_forum->ID );
          $title     = bbp_get_forum_title( $sub_forum->ID );

          // Show topic count
          if ( !empty( $r['show_topic_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
              $count['topic'] = bbp_get_forum_topic_count( $sub_forum->ID );
          }

          // Show reply count
          if ( !empty( $r['show_reply_count'] ) && !bbp_is_forum_category( $sub_forum->ID ) ) {
              $count['reply'] = bbp_get_forum_reply_count( $sub_forum->ID );
          }

          // Counts to show
          if ( !empty( $count ) ) {
              $counts = $r['count_before'] . implode( $r['count_sep'], $count ) . $r['count_after'];
          }

          // Build this sub forums link
          $output .= '<li class="bbp-forum"><img src="' . get_stylesheet_directory_uri().'/images/flags/'.get_field('country_iso_code',$sub_forum->ID).'.png' . '" alt="" />' . '<a href="' . esc_url( $permalink ) . '" class="bbp-forum-link">' . $title . $counts . '</a>' . $show_sep . $r['link_after'];
        }

        // Output the list
        echo apply_filters( 'bbp_list_forums', $r['before'] . $output . $r['after'], $r );
	    }
		} else {
			bbp_list_forums(array(
				'show_topic_count' => false,
        'show_reply_count' => false,
			));
		}
		?>

		<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</li>

	<li class="bbp-forum-topic-count"><?php bbp_forum_topic_count(); ?></li>

	<li class="bbp-forum-reply-count"><?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
