<?php

declare( strict_types=1 );

namespace Blockify\Theme;

use function add_filter;
use function str_replace;

add_filter( 'render_block', NS . 'render_post_author_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.1
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_post_author_block( string $content, array $block ): string {

	if ( 'core/post-author' === $block['blockName'] ) {
		$content = str_replace(
			[ '<p ', '</p>' ],
			[ '<span ', '</span>' ],
			$content
		);
	}

	return $content;
}

