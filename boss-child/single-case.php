<?php
/**
 * The Template for displaying all single case.
 *
 */
get_header(); ?>

<?php
// Function to display all questions related to a group field
function group_field( $group_key ) {
  // get the group field object
  $group_field = get_field_object( $group_key );

  // init return variable
  $output = '';

  // group title
  $output .= '<h2>' . $group_field[ 'label' ] . '</h2>';

  // display each question, it there's a reply
  foreach ( $group_field['sub_fields'] as $subfield ) {
    $reply = $group_field['value'][$subfield['name']];
    // if it's Control placeholder field, or tags field, skip it
    if ( 'Controls' != $subfield['label'] &&
         'Select at least 3 tags that describe your actions. We encourage you to add additional tags as needed.' != $subfield['label'] &&
         'Additional tags' != $subfield['label'] ) :
      $output .= '<div class="form-question ' . $subfield['key'] . '">
                   <h3>' . $subfield['label'] . '</h3>
                   <div class="reply">
                     ' . $group_field['value'][$subfield['name']] . '
                   </div>
                 </div><!-- end of form-question -->';
    endif;
  }

  echo $output;
}
?>

      <div class="page-full-width">
        <div id="primary" class="site-content single-case">

            <div id="content" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                  <h1 class="entry-title"><?php the_title() ?></h1>

                  <div class="content-container">
                      <div class="main-column">
                        <?php if ( has_post_thumbnail() ): ?>
                        <div class="featured-image">
                          <?php the_post_thumbnail( 'full' ); ?>
                        </div>
                        <?php endif; ?>

                        <?php
                        $excerpt = get_field( 'case_study_summary_short_and_simple_explanation' );
                        ?>
                        <div class="case-excerpt"><?php echo $excerpt ?></div>

                        <div class="questions">
                        <?php
                        group_field( 'field_5d2c2d47c6a69' );
                        group_field( 'field_5d2c2eb5c6a6f' );
                        group_field( 'field_5d2c3cd446d67' );
                        ?>
                        </div>

                        <?php
                        $other_rel_urls = get_field('materials_other_related_urls');
                        $images = get_field('materials_upload_images');
                        $files = get_field('materials_upload_supporting_files');
                         ?>
                        <div class="materials">
                          <?php
                          if ($other_rel_urls) {
                            echo '<div class="other-urls"><h2>Other related URL</h2><ul>';
                              foreach ($other_rel_urls as $url) {
                                echo '<li><a href="'. $url[url] .'">'. $url[url] .'</a></li>';
                              }
                            echo '</ul></div>';
                          }

                          if ($images) {
                            echo '<div class="images-gallery">';
                              foreach ($images as $image) {
                                echo wp_get_attachment_image($image, 'medium');
                              }
                            echo '</div>';
                          }

                          if ($files) {
                            echo '<div class="files-gallery"><h2>Supporting files</h2><ul>';
                              foreach ($files as $file) {
                                echo '<li><a href="'. $file .'">'. $file .'</a></li>';
                              }
                            echo '</ul></div>';
                          } ?>
                        </div>

                        <?php
                        $video1 = get_field( 'materials_video_url_1' );
              					$video2 = get_field( 'materials_video_url_2' );
              					$video3 = get_field( 'materials_video_url_3' );
              					?>
              					<div class="video_wrap">
              					<?php
              						if ( $video1 ) { echo '<h2>'. __( 'Project Pitch', 'bs_pisa' ) .'</h3><div class="single_vid">'.wp_oembed_get( $video1 ).'</div>'; }
              						if ( $video2 || $video3 ) { echo '<h2>'. __( 'Supporting Videos', 'bs_pisa' ) .'</h3>'; }
              						if ( $video2 ) { echo '<div class="single_vid">'.wp_oembed_get( $video2 ).'</div>'; }
              						if ( $video3 ) { echo '<div class="single_vid">'.wp_oembed_get( $video3 ).'</div>'; }
              					?>
              					</div>

                      </div><!-- end of left-col -->

                      <div class="side-column">

                        <div class="case-country">
                          <?php
                          $terms = get_the_terms( get_the_ID(), 'country' );
                          $country = $terms[0]->name;
                          $ISO = get_field( 'iso_code', $terms[0] );
                           ?>
                          <img src='/wp-content/themes/boss-child/images/flags/<?php echo $ISO ?>.png' alt='<?php echo $country ?> flag' />
                          <p><?php echo $country ?></p>
                        </div>

                        <div class="school-details">
                          <p><strong>School name:</strong> <?php the_field( 'your_school_details_school_name' ) ?> </p>
                          <p><strong>School type:</strong> <?php the_field( 'your_school_details_school_type' ) ?> </p>
                          <p><strong>Where are most of students of 15 years old:</strong> <?php the_field( 'your_school_details_where_are_most_of_your_students_of_15_years_old' ) ?> </p>
                          <p><strong>At which year the school participate in PISA-based Test for Schools:</strong> <?php the_field( 'your_school_details_at_which_year_did_your_school_participate_in_pisa-based_test_for_schools' ) ?> </p>
                        </div>

                        <div class="description-tags">
                          <h2>Description tags</h2>
                          <?php echo get_the_term_list( get_the_ID(), 'description-tag' ) ?>
                        </div>

                        <?php if( get_field( 'post-assessment_learning_additional_tags' ) ) : ?>
                        <div class="description-tags">
                          <h2>Additional Description tags</h2>
                          <?php the_field( 'post-assessment_learning_additional_tags' ) ?>
                        </div>
                        <?php endif; ?>

                        <div class="case-author">
                          <h2>Case provided by:</h2>
                          <?php $author_id = get_the_author_meta( 'ID' ); ?>
                          <a class="author-badge" href="<?php echo bp_core_get_userlink( $author_id, false, true ) ?>profile/" >
              						<?php echo bp_get_displayed_user_avatar( array('item_id' => $author_id, 'type'=>'thumb') ) ?>
                          <?php echo get_the_author(); ?>
              						</a>
                          <a href="<?php echo bp_custom_get_send_private_message_link( $author_id ) ?>" class="button"><?php echo __( 'Send message', 'bs_pisa' ) ?><i class="fa fa-envelope" aria-hidden="true"></i></a>
                          <a href="<?php echo bp_core_get_userlink( $author_id, false, true ) ?>" class="button"><?php echo __( 'View profile', 'bs_pisa' ) ?><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                        </div>

                        <div class="case-published">
                          <p><strong>Date published:</strong> <?php echo get_the_date(); ?></p>
                        </div>


                      </div><!-- end of right-col -->
                  </div><!-- end of content-container -->

                </article><!-- #post-## -->

                <?php
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                ?>

            <?php endwhile; // end of the loop. ?>

            </div><!-- #main -->
        </div><!-- #primary -->

    </div>

    <script>
    (function() {
      // Get all the <h2> headings
      const headings = document.querySelectorAll('.main-column .questions h2')

      Array.prototype.forEach.call(headings, heading => {
        // Give each <h2> a toggle button child
        // with the SVG plus/minus icon
        heading.innerHTML = `
          <button aria-expanded="false">
            ${heading.textContent}
            <svg aria-hidden="true" focusable="false" viewBox="0 0 10 10">
              <rect class="vert" height="8" width="2" y="1" x="4"/>
              <rect height="2" width="8" y="4" x="1"/>
            </svg>
          </button>
        `

        // Function to create a node list
        // of the content between this <h2> and the next
        const getContent = (elem) => {
          let elems = []
          while (elem.nextElementSibling && elem.nextElementSibling.tagName !== 'H2') {
            elems.push(elem.nextElementSibling)
            elem = elem.nextElementSibling
          }

          // Delete the old versions of the content nodes
          elems.forEach((node) => {
            node.parentNode.removeChild(node)
          })

          return elems
        }

        // Assign the contents to be expanded/collapsed (array)
        let contents = getContent(heading)

        // Create a wrapper element for `contents` and hide it
        let wrapper = document.createElement('div')
        wrapper.hidden = true

        // Add each element of `contents` to `wrapper`
        contents.forEach(node => {
          wrapper.appendChild(node)
        })

        // Add the wrapped content back into the DOM
        // after the heading
        heading.parentNode.insertBefore(wrapper, heading.nextElementSibling)

        // Assign the button
        let btn = heading.querySelector('button')

        btn.onclick = () => {
          // Cast the state as a boolean
          let expanded = btn.getAttribute('aria-expanded') === 'true' || false

          // Switch the state
          btn.setAttribute('aria-expanded', !expanded)
          // Switch the content's visibility
          wrapper.hidden = expanded
        }
      })
    })()
    </script>

<?php get_footer();
