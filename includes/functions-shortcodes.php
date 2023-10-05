<?php

/**
 * Exclude the current post on single page
 *
 * 
 */
function exclude_current_post_shortcode( $atts ) {
    global $post;
    $current_post_id = $post->ID;

    $atts = shortcode_atts( array(
        'posts_per_page' => 5,
        'post_type'      => 'post',
    ), $atts, 'exclude_current_post' );

    $query_args = array(
        'post_type'      => $atts['post_type'],
        'posts_per_page' => absint( $atts['posts_per_page'] ),
        'post__not_in'   => array( $current_post_id ),
    );

    $query = new WP_Query( $query_args );

    if ( $query->have_posts() ) {
        ob_start();?>
<ul class="query-except-current-post">
       <?php while ( $query->have_posts() ) {
            $query->the_post();
            ?>
            <li class="custom-post-item">
                <?php if ( has_post_thumbnail() ) : ?>
                    <figure class="custom-post-thumbnail">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
										</figure>
                <?php endif; ?>

                <div class="custom-post-details">
                    <div class="custom-post-date">
                        <?php echo get_the_date('m.Y'); ?>
                    </div>
                    <h4 class="custom-post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h4>
                </div>
								</li>
            <?php
        }?>
				</ul><?php

        wp_reset_postdata();

        return ob_get_clean();
    }
}
add_shortcode( 'exclude_current_post', 'exclude_current_post_shortcode' );
//You can choose de number of posts and also the type of post [exclude_current_post posts_per_page="3" post_type="post"]
