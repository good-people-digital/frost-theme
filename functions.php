<?php
/**
 * This file adds functions to the Frost WordPress theme.
 *
 * @package Frost
 * @author  WP Engine
 * @license GNU General Public License v2 or later
 * @link    https://frostwp.com/
 */

if ( ! function_exists( 'frost_setup' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function frost_setup() {

		// Make theme available for translation.
		load_theme_textdomain( 'frost', get_template_directory() . '/languages' );

		// Enqueue editor styles and fonts.
		add_editor_style(
			array(
				'./style.css',
			)
		);

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

	}
}
add_action( 'after_setup_theme', 'frost_setup' );

// Enqueue style sheet.
add_action( 'wp_enqueue_scripts', 'frost_enqueue_style_sheet' );
function frost_enqueue_style_sheet() {

	wp_enqueue_style( 'frost', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );

}

/**
 * Register block styles.
 *
 * @since 0.9.2
 */
function frost_register_block_styles() {

	$block_styles = array(
		'core/columns' => array(
			'columns-reverse' => __( 'Reverse', 'frost' ),
		),
		'core/group' => array(
			'shadow-light' => __( 'Shadow', 'frost' ),
			'shadow-solid' => __( 'Solid', 'frost' ),
		),
		'core/image' => array(
			'shadow-light' => __( 'Shadow', 'frost' ),
			'shadow-solid' => __( 'Solid', 'frost' ),
		),
		'core/list' => array(
			'no-disc' => __( 'No Disc', 'frost' ),
		),
		'core/navigation-link' => array(
			'outline' => __( 'Outline', 'frost' ),
		),
		'core/quote' => array(
			'shadow-light' => __( 'Shadow', 'frost' ),
			'shadow-solid' => __( 'Solid', 'frost' ),
		),
		'core/social-links' => array(
			'outline' => __( 'Outline', 'frost' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', 'frost_register_block_styles' );

/**
 * Register block pattern categories.
 *
 * @since 1.0.4
 */
function frost_register_block_pattern_categories() {

	register_block_pattern_category(
		'page',
		array(
			'label'       => __( 'Page', 'frost' ),
			'description' => __( 'Create a full page with multiple patterns that are grouped together.', 'frost' ),
		)
	);
	register_block_pattern_category(
		'pricing',
		array(
			'label'       => __( 'Pricing', 'frost' ),
			'description' => __( 'Compare features for your digital products or service plans.', 'frost' ),
		)
	);

}

add_action( 'init', 'frost_register_block_pattern_categories' );

function get_related_articles_by_category($atts) {
    $atts = shortcode_atts(array(), $atts);

    $current_post_id = get_the_ID();
    $categories = get_the_category($current_post_id);

    if ($categories) {
        $category_ids = array();
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }

        $args = array(
            'posts_per_page' => 4,
            'category__in' => $category_ids,
            'post__not_in' => array($current_post_id),
            'orderby' => 'date',
            'order' => 'DESC' // Sort in descending order (newest first)
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $output .= '<h2 class="wp-block-heading has-text-align-left has-contrast-color has-text-color has-max-48-font-size">See other articles</h2>';
            $output .= '<ul class="wp-block-latest-posts__list has-link-color has-text-color has-contrast-color wp-block-latest-posts">';

            while ($query->have_posts()) {
                $query->the_post();
                $output .= '<li><a class="wp-block-latest-posts__post-title" href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }

            $output .= '</ul>';

            // Get the first category of the current post
            $first_category = reset($categories);
            $category_link = get_category_link($first_category);

            $output .= '<p class="more-articles has-contrast-color has-text-color has-link-color"><a href="' . $category_link . '">See all articles</a></p>';

            wp_reset_postdata();

            return $output;
        }
    }

    return 'No related articles found.';
}
add_shortcode('related_articles', 'get_related_articles_by_category');