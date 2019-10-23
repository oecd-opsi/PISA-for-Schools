<?php

/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Boss
 */

?>

<div id="bbpress-forums">

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

				<div class="bbp-forum-breadcrumb"><?php bbp_breadcrumb(); ?></div>

        <div class="bbp-topic-details">
            <div class="table-cell">
                <?php bbp_topic_tag_list( bbp_get_topic_id(), array(
									'sep' => ' ',
								)); ?>
            </div>
            <?php buddyboss_bbp_single_topic_description(array('before'=>'<div class="bbp-forum-data">', 'after'=>'</div>')); ?>
        </div>


		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

			<?php bbp_get_template_part( 'loop',       'replies' ); ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

		<?php endif; ?>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div>
