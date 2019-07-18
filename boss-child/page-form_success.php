<?php
/**
 * Template Name: Case study form - Successfull submission Template
 *
 * Description: Use this page template for a page with the Case study form.
 */
acf_form_head();
get_header();
?>

<?php if ( is_active_sidebar('sidebar') ) : ?>
    <div class="page-right-sidebar">
<?php else : ?>
    <div class="page-full-width">
<?php endif; ?>

        <div id="primary" class="site-content form-related-page">


            <div id="content" role="main">

                <!-- Display form -->
                <?php while ( have_posts() ) : the_post(); ?>

                    <h1><?php the_title(); ?></h1>

                    <?php if ( is_active_sidebar('case_study_form') ) :
                      get_sidebar('case_study_form');
                    endif; ?>

                    <div class="form-notice-success">
                    <?php
                    the_content();
                     ?>
                    </div>

                <?php endwhile; // end of the loop. ?>

            </div><!-- #content -->
        </div><!-- #primary -->

    </div>
<?php get_footer(); ?>
