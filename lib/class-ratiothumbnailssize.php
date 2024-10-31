<?php
/**
 * Ratio Thumbnails Size
 *
 * @package    Ratio Thumbnails Size
 * @subpackage RatioThumbnailsSize Main Functions
/*
	Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$ratiothumbnailssize = new RatioThumbnailsSize();

/** ==================================================
 * Main Functions
 */
class RatioThumbnailsSize {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'change_generate_thumbnails_size' ), 10, 2 );
	}

	/** ==================================================
	 * Filters the image sizes automatically generated when uploading an image.
	 *
	 * @param array $sizes  sizes.
	 * @param array $metadata  metadata.
	 * @return array $sizes  sizes.
	 * @since 1.00
	 */
	public function change_generate_thumbnails_size( $sizes, $metadata ) {

		$ratiothumbnailssize_settings = get_option( 'ratiothumbnailssize', 'digicame' );

		switch ( $ratiothumbnailssize_settings ) {
			case 'silver':
				$ratio = 1 / 1.414;
				break;
			case 'golden':
				$ratio = 1 / 1.618;
				break;
			case 'ogp_fb_tw':
				$ratio = 1 / 1.91;
				break;
			case 'film':
				$ratio = 2 / 3;
				break;
			case 'digicame':
				$ratio = 3 / 4;
				break;
			case 'hd':
				$ratio = 9 / 16;
				break;
			case 'org':
				if ( $metadata['width'] >= $metadata['height'] ) {
					$ratio = $metadata['height'] / $metadata['width'];
				} else {
					$ratio = $metadata['width'] / $metadata['height'];
				}
				break;
		}

		$ratiothumbnailssize_disable_settings = get_option( 'ratiothumbnailssize_disable', array() );
		foreach ( $sizes as $key1 => $values ) {
			if ( ! in_array( $key1, $ratiothumbnailssize_disable_settings ) ) {
				if ( $metadata['width'] >= $metadata['height'] ) {
					$sizes[ $key1 ]['height'] = intval( $sizes[ $key1 ]['width'] * $ratio );
				} else {
					$sizes[ $key1 ]['width'] = intval( $sizes[ $key1 ]['height'] * $ratio );
				}
				$sizes[ $key1 ]['crop'] = 1;
			}
		}

		return $sizes;
	}
}
