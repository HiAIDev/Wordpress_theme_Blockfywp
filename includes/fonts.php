<?php

declare( strict_types=1 );

namespace Blockify\Theme;

use function apply_filters;
use function array_merge;
use function array_merge_recursive;
use function basename;
use function get_stylesheet_directory;
use function get_template_directory;
use function glob;
use function in_array;
use function is_admin;
use function str_replace;
use WP_Theme_JSON_Data_Gutenberg;
use function ucwords;

add_filter( 'theme_json_theme', NS . 'register_local_font_choices' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param WP_Theme_JSON_Data_Gutenberg $theme_json
 *
 * @return WP_Theme_JSON_Data_Gutenberg
 */
function register_local_font_choices( WP_Theme_JSON_Data_Gutenberg $theme_json ): WP_Theme_JSON_Data_Gutenberg {
	$default = $theme_json->get_data();

	$theme_json->update_with(
		array_merge_recursive(
			$default,
			[
				'settings' => [
					'typography' => [
						'fontFamilies' => array_merge(
							get_system_fonts(),
							is_admin() ? get_all_fonts() : get_selected_fonts()
						),
					],
				],
			]
		)
	);

	return $theme_json;
}

add_filter( 'theme_json_theme', NS . 'load_selected_fonts', 11 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param WP_Theme_JSON_Data_Gutenberg $theme_json
 *
 * @return WP_Theme_JSON_Data_Gutenberg
 */
function load_selected_fonts( WP_Theme_JSON_Data_Gutenberg $theme_json ): WP_Theme_JSON_Data_Gutenberg {
	$default = $theme_json->get_data();

	$theme_json->update_with(
		array_merge_recursive(
			$default,
			[
				'settings' => [
					'typography' => [],
				],
			]
		)
	);

	return $theme_json;
}

/**
 * Returns array of user selected font families.
 *
 * @since 0.0.2
 *
 * @return void
 */
function get_selected_fonts(): array {
	$selected_fonts = [];
	$font_families  = [];
	$global_styles  = wp_get_global_styles();

	$font_styles = [
		'heading' => $global_styles['blocks']['core/heading']['typography']['fontFamily'] ?? null,
		'body'    => $global_styles['typography']['fontFamily'] ?? null,
		'link'    => $global_styles['elements']['link']['typography']['fontFamily'] ?? null,
		'button'  => $global_styles['elements']['button']['typography']['fontFamily'] ?? $global_styles['blocks']['core/button']['typography']['fontFamily'] ?? null,
	];

	foreach ( $font_styles as $font_style ) {
		if ( $font_style ) {
			$font_families[] = $font_style;
		}
	}

	$font_families = array_unique( $font_families );

	foreach ( $font_families as $font_family ) {

		if ( str_contains( $font_family, 'var(--' ) ) {
			$explode_font = explode( '--', str_replace( ')', '', $font_family ) );
		} else {
			$explode_font = explode( '|', $font_family );
		}

		$slug = end( $explode_font );
		$name = ucwords( str_replace( '-', ' ', $slug ) );

		if ( in_array( $slug, [ 'sans-serif', 'serif', 'monospace' ] ) ) {
			continue;
		}

		$selected_fonts[] = [
			'fontFamily' => $name,
			'name'       => $name,
			'slug'       => $slug,
			'fontFace'   => [
				[
					'fontFamily'  => $name,
					'fontStyle'   => 'normal',
					'fontStretch' => 'normal',
					'fontDisplay' => 'swap',
					'fontWeight'  => '100 900',
					'src'         => [
						"file:./assets/fonts/$slug.woff2",
					],
				],
			],
		];
	}

	return $selected_fonts;
}


/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function get_all_fonts(): array {
	$font_families = [];
	$font_slugs    = [];
	$font_files    = [
		...glob( get_stylesheet_directory() . '/assets/fonts/*.woff2' ),
		...glob( get_template_directory() . '/assets/fonts/*.woff2' ),
	];

	foreach ( $font_files as $font_file ) {
		$slug = basename( $font_file, '.woff2' );

		if ( in_array( $slug, $font_slugs, true ) ) {
			continue;
		}

		$font_slugs[] = $slug;
		$name         = ucwords( str_replace( '-', ' ', $slug ) );

		$font_families[] = [
			'fontFamily' => $name,
			'name'       => $name,
			'slug'       => $slug,
			'fontFace'   => [
				[
					'fontFamily'  => $name,
					'fontStyle'   => 'normal',
					'fontStretch' => 'normal',
					'fontDisplay' => 'swap',
					'fontWeight'  => '100 900',
					'src'         => [
						"file:./assets/fonts/$slug.woff2",
					],
				],
			],
		];
	}

	return apply_filters( 'blockify_font_families', $font_families );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return array
 */
function get_system_fonts(): array {
	return [
		[
			'name'       => 'Sans Serif',
			'slug'       => 'sans-serif',
			'fontFamily' => '-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif',
		],
		[
			'name'       => 'Serif',
			'slug'       => 'serif',
			'fontFamily' => 'Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol',
		],
		[
			'name'       => 'Monospace',
			'slug'       => 'monospace',
			'fontFamily' => 'Menlo, Consolas, Monaco, Liberation Mono, Lucida Console, monospace',
		],
	];
}
