<?php
/**
 * Plugin Name: Blockify Slider
 * Plugin URI:  https://blockifywp.com/blocks/slider
 * Description: Lightweight, customizable slider block for WordPress.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/
 * Version:     0.0.1
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 */

declare( strict_types=1 );

namespace Blockify\Slider;

use DOMElement;
use function add_action;
use function add_filter;
use function register_block_type;

const NS = __NAMESPACE__ . '\\';
const DS = DIRECTORY_SEPARATOR;

add_action( 'after_setup_theme', NS . 'register' );
/**
 * Registers the block.
 *
 * @since 0.0.1
 *
 * @since 1.0.0
 *
 * @return void
 */
function register() {
	register_block_type( __DIR__ . '/build' );
}

add_filter( 'render_block_blockify/slider', NS . 'render_slider_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_slider_block( string $content, array $block ): string {
	$dom = dom( $content );

	/**
	 * @var $div DOMElement
	 */
	$div = $dom->firstChild;

	$div->setAttribute( 'data-per-view', isset( $block['attrs']['perView'] ) ? (string) $block['attrs']['perView'] : '3' );

	$content = $dom->saveHTML();

	return $content;
}


use function defined;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function mb_convert_encoding;
use DOMDocument;

/**
 * Returns a formatted DOMDocument object from a given string.
 *
 * @since 0.0.2
 *
 * @param string $html
 *
 * @return string
 */
function dom( string $html ): DOMDocument {
	$dom = new DOMDocument();

	if ( ! $html ) {
		return $dom;
	}

	$libxml_previous_state   = libxml_use_internal_errors( true );
	$dom->preserveWhiteSpace = true;

	if ( defined( 'LIBXML_HTML_NOIMPLIED' ) && defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
	} else if ( defined( 'LIBXML_HTML_NOIMPLIED' ) ) {
		$options = LIBXML_HTML_NOIMPLIED;
	} else if ( defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options = LIBXML_HTML_NODEFDTD;
	} else {
		$options = 0;
	}

	$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), $options );

	$dom->formatOutput = true;

	libxml_clear_errors();
	libxml_use_internal_errors( $libxml_previous_state );

	return $dom;
}
