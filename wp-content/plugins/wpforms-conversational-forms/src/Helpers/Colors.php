<?php

namespace WPFormsConversationalForms\Helpers;

/**
 * Conversational Forms colors helper.
 *
 * @since 1.0.0
 */
class Colors {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Validate hex color code and return proper value.
	 *
	 * Input: String - Format #ffffff, #fff, ffffff or fff
	 * Output: hex value - 3 byte (000000 if input is invalid)
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex Hex color.
	 *
	 * @return string
	 */
	public function validate_hex( $hex ) {

		// Complete patterns like #ffffff or #fff.
		if ( preg_match( '/^#([0-9a-fA-F]{6})$/', $hex ) || preg_match( '/^#([0-9a-fA-F]{3})$/', $hex ) ) {
			// Remove #.
			$hex = substr( $hex, 1 );
		}

		// Complete patterns without # like ffffff or 000000.
		if ( preg_match( '/^([0-9a-fA-F]{6})$/', $hex ) ) {
			return $hex;
		}

		// Short patterns without # like fff or 000.
		if ( preg_match( '/^([0-9a-f]{3})$/', $hex ) ) {
			// Spread to 6 digits.
			return preg_replace( '/(\w)/', '$1$1', $hex );
		}

		// If input value is invalid return black.
		return '000000';
	}

	/**
	 * Make hex darker/lighter emulating opacity with black/white color as a background.
	 *
	 * Examples:
	 * $alpha = 0    Unchanged color
	 * $alpha = 1    Unchanged color
	 * $alpha = -1   Unchanged color
	 * $alpha = 0.1  Very light, almost white
	 * $alpha = -0.1 Very dark, almost black
	 * $alpha = 0.9  Slightly lighter, almost unchanged
	 * $alpha = -0.9 Slightly darker, almost unchanged
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex   Hex color.
	 * @param float  $alpha Emulates alpha channel in RGBa. Min -1, max 1.
	 *
	 * @return string
	 */
	public function hex_opacity( $hex, $alpha ) {

		$hex = $this->validate_hex( $hex );

		$alpha = (float) $alpha;

		if ( empty( $alpha ) ) {
			return $hex;
		}

		// Limit $alpha min -1 and max 1.
		$alpha = max( - 1, min( 1, $alpha ) );

		$blend_color = $alpha > 0 ? 255 : 0;

		// Split into three parts: R, G and B.
		$color_parts = str_split( $hex, 2 );
		$output      = '#';

		foreach ( $color_parts as $color ) {

			// Convert to decimal.
			$color = hexdec( $color );

			// Adjust color.
			$color = \abs( $alpha ) * $color + ( 1 - \abs( $alpha ) ) * $blend_color;

			// Pad left with zeroes if hex $color is less than two characters long.
			$output .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT );
		}

		return $output;
	}

	/**
	 * Convert hex color code to RGB color.
	 *
	 * Input: String - Format #ffffff, #fff, ffffff or fff
	 * Output: Array(Red, Green, Blue) - Values from 0 to 1
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex Hex color.
	 *
	 * @return array
	 */
	public function hex2rgb( $hex ) {

		$hex = $this->validate_hex( $hex );

		$rgb = array_map( 'hexdec', str_split( $hex, 2 ) );

		$color_to_dec = function( $color ) {
			return $color / 255;
		};

		return array_map( $color_to_dec, $rgb );
	}

	/**
	 * Convert hex color code to HSL color.
	 *
	 * Input: String - Format #ffffff, #fff, ffffff or fff
	 * Output: Array(Hue, Saturation, Lightness) - Values from 0 to 1
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex Hex color.
	 *
	 * @return array
	 */
	public function hex2hsl( $hex ) {

		$hex = $this->validate_hex( $hex );

		return $this->rgb2hsl( $this->hex2rgb( $hex ) );
	}

	/**
	 * Convert RGB color to HSL color
	 *
	 * Check http://en.wikipedia.org/wiki/HSL_and_HSV#Hue_and_chroma for
	 * details
	 *
	 * Input: Array(Red, Green, Blue) - Values from 0 to 1
	 * Output: Array(Hue, Saturation, Lightness) - Values from 0 to 1
	 *
	 * @since 1.0.0
	 *
	 * @param array $rgb RGB color.
	 *
	 * @return array
	 */
	public function rgb2hsl( $rgb ) {

		// Fill variables $r, $g, $b by array given.
		list( $r, $g, $b ) = $rgb;

		// Determine lowest & highest value and chroma.
		$max    = max( $r, $g, $b );
		$min    = min( $r, $g, $b );
		$chroma = $max - $min;

		// Calculate Luminosity.
		$l = ( $max + $min ) / 2;

		// If chroma is 0, the given color is grey
		// therefore hue and saturation are set to 0.
		if ( 0 === $chroma ) {
			return array( 0, 0, $l );
		}

		// Else calculate hue and saturation.
		// Check http://en.wikipedia.org/wiki/HSL_and_HSV for details.
		switch ( $max ) {
			case $r:
				$h_ = fmod( ( ( $g - $b ) / $chroma ), 6 );
				if ( $h_ < 0 ) {
					$h_ = ( 6 - fmod( abs( $h_ ), 6 ) );
				}
				break;

			case $g:
				$h_ = ( $b - $r ) / $chroma + 2;
				break;

			case $b:
				$h_ = ( $r - $g ) / $chroma + 4;
				break;
			default:
				break;
		}

		$h = $h_ / 6;
		$s = 1 - abs( 2 * $l - 1 );

		return array( $h, $s, $l );
	}

	/**
	 * Convert RGB color to hex code
	 *
	 * Input: Array(Red, Green, Blue) - Values from 0 to 1
	 * Output: String hex value (#000000 - #ffffff)
	 *
	 * @since 1.0.0
	 *
	 * @param array $rgb RGB color.
	 *
	 * @return string
	 */
	public function rgb2hex( $rgb ) {

		$hex = '#';

		foreach ( $rgb as $color ) {

			$color = round( 255 * $color );

			// Pad left with zeroes if hex $color is less than two characters long.
			$hex .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT );
		}

		return $hex;
	}

	/**
	 * Convert HSL color to RGB color
	 *
	 * Input: Array(Hue, Saturation, Lightness) - Values from 0 to 1
	 * Output: Array(Red, Green, Blue) - Values from 0 to 1
	 *
	 * @since 1.0.0
	 *
	 * @param array $hsl HSL color.
	 *
	 * @return array
	 */
	public function hsl2rgb( $hsl ) {

		// Fill variables $h, $s, $l by array given.
		list( $h, $s, $l ) = $hsl;

		// If saturation is 0, the given color is grey and only
		// lightness is relevant.
		if ( 0 === $s ) {
			return array( $l, $l, $l );
		}

		// Else calculate r, g, b according to hue.
		// Check http://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL for details.
		$chroma = ( 1 - abs( 2 * $l - 1 ) ) * $s;
		$h_     = $h * 6;
		$x      = $chroma * ( 1 - abs( ( fmod( $h_, 2 ) ) - 1 ) );
		$m      = $l - round( $chroma / 2, 10 );

		if ( $h_ >= 0 && $h_ < 1 ) {
			$rgb = array( ( $chroma + $m ), ( $x + $m ), $m );
		} elseif ( $h_ >= 1 && $h_ < 2 ) {
			$rgb = array( ( $x + $m ), ( $chroma + $m ), $m );
		} elseif ( $h_ >= 2 && $h_ < 3 ) {
			$rgb = array( $m, ( $chroma + $m ), ( $x + $m ) );
		} elseif ( $h_ >= 3 && $h_ < 4 ) {
			$rgb = array( $m, ( $x + $m ), ( $chroma + $m ) );
		} elseif ( $h_ >= 4 && $h_ < 5 ) {
			$rgb = array( ( $x + $m ), $m, ( $chroma + $m ) );
		} elseif ( $h_ >= 5 && $h_ < 6 ) {
			$rgb = array( ( $chroma + $m ), $m, ( $x + $m ) );
		}

		return $rgb;
	}

	/**
	 * Convert HSL color to hex color
	 *
	 * Input: Array(Hue, Saturation, Lightness) - Values from 0 to 1
	 * Output: String hex value (#000000 - #ffffff)
	 *
	 * @since 1.0.0
	 *
	 * @param array $hsl HSL color.
	 *
	 * @return string
	 */
	public function hsl2hex( $hsl ) {

		$rgb = $this->hsl2rgb( $hsl );

		return $this->rgb2hex( $rgb );
	}

	/**
	 * SCSS lighten() adaptation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex     Hex color.
	 * @param int    $percent Number of percent.
	 *
	 * @return string
	 */
	public function hex_lighten( $hex, $percent ) {

		$hex = $this->validate_hex( $hex );

		$hsl = $this->hex2hsl( $hex );

		$percent = max( 0, min( 100, $percent ) );

		$hsl[2] = max( 0.01, min( 1, $hsl[2] + $percent ) );

		return $this->hsl2hex( $hsl );
	}

	/**
	 * SCSS darken() adaptation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex     Hex color.
	 * @param int    $percent Number of percent.
	 *
	 * @return string
	 */
	public function hex_darken( $hex, $percent ) {

		$hex = $this->validate_hex( $hex );

		$hsl = $this->hex2hsl( $hex );

		$percent = max( 0, min( 100, $percent ) );

		$hsl[2] = max( 0.01, min( 1, $hsl[2] - $percent ) );

		return $this->hsl2hex( $hsl );
	}

	/**
	 * Convert hex to its rgba CSS text representation using given opacity.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hex     Hex color.
	 * @param int    $opacity Opacity.
	 *
	 * @return string
	 */
	public function hex_to_rgba_css_value( $hex, $opacity ) {

		$hex = $this->validate_hex( $hex );

		$dec_to_color = function( $color ) {
			return round( 255 * $color );
		};

		$rgb = array_map( $dec_to_color, $this->hex2rgb( $hex ) );

		return 'rgba(' . implode( ', ', $rgb ) . ', ' . $opacity . ')';
	}
}
