<?php
/**
 * The sidebar containing the secondary widget area for the Case study form.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage Boss
 * @since Boss 1.0.0
 */
?>

<!-- left WordPress sidebar -->
<div id="secondary" class="widget-area case-study-form-widget-area" role="complementary">
	<?php if ( is_active_sidebar('case_study_form') ) : ?>
		<?php dynamic_sidebar( 'case_study_form' ); ?>
	<?php endif; ?>
</div><!-- #secondary -->
