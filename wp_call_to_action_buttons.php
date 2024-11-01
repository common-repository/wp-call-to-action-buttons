<?php
/*
	Plugin Name: WP Call To Action Buutons
	Plugin URI: http://www.connorlaird.com/cta
	Description: A plugin to insert Call to ActionButtons
	Version: 1.00
	Author: Connor Laird
	Author URI: http://www.connorlaird.com
	License: GPL2
*/
/*  Copyright 2013 Connor Laird  (email : connorlaird@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/********************************************
*Global Variables
********************************************/

$wpcta_prefix = 'wpcta_';
$wpcta_plugin_name = 'WP Call To Action Buttons';



/********************************************
*Functions
********************************************/

add_shortcode( 'WPCTA', 'wpcta_Shortcode_Handler' );
add_shortcode( 'wpcta', 'wpcta_Shortcode_Handler' );


/** The main function, adds option for a new color code if there is not already one,
* then call function to output markup
*@param Shortcode $atts
*@param Shortcode $content
*@return none
*/
function wpcta_Shortcode_Handler($atts, $content = null){
	extract( shortcode_atts( array(
		'color' => 'f43456',
		'style' => '',
		'href' => '',
		'rounded' => false,
		'size' => '',
	), $atts ) );
	while(strlen($color) != 6){
		if(strpos($color,"#") == 0){
			$color = substr($color, 1);
		}
		if(strlen($color) == 0){
			$color = "8d8d8d";
			$content = "Invalid Color Code";
		}
	}
	if($size == 'Large' || $size == 'large') $style.= "padding: 12px 32px; font-size:1.2em;";
	if(get_option($color) == null) add_option( $color, wpcta_genorateColors($color));
	if($rounded == true) $style .= "border-radius: 10px;";
	wpcta_printMarkup($color, $style, $href, $content);
}


/** Genorates Colors for Hover, Gradient, Border and text colors
*@param $hex the base color in 6 digit form
*@return String with Hover, Gradient, Border and text colors no spaces or #'s
*/
function wpcta_genorateColors($hex){
	$color =  wpcta_parseHex($hex);
  	$hover =  wpcta_modColor($color, -12, -16);
	$gradient =  wpcta_modColor($color, 10, 3);
	$border =  wpcta_modColor($color, -33, -30);
	if(array_sum($color) < 600) $text = "ededed";
	else $text = "3d3d3d";
	$return =  wpcta_biuldHex($hover).wpcta_biuldHex($gradient).wpcta_biuldHex($border).$text;
	return $return;
}

/** 
*@param $hex the base color in 6 digit form
*@return Returns an array with 3 decimal values
*/
function wpcta_parseHex($hex){
	$color = array( "red" => (int) hexdec(substr($hex, 0, 2)), "green" => (int) hexdec(substr($hex, 2, 2)), "blue" => (int) hexdec(substr($hex, 4, 2)) );
	return $color;
}
/** Givin array of int values, adds $dom to max and $min to others
*@param array of colors, dom value and min value
*@return Returns an array with 3 decimal values
*/
function wpcta_modColor($newColor, $dom, $min){
	$max = max($newColor);
	foreach($newColor as &$a){ 
		if($a == $max) $a += ($dom - $min);
		$a += $min;
		if($a > 255) $a= 255;
		if($a < 0) $a = 0;
	}
	return $newColor;
}
function wpcta_biuldHex($array){
	$string = wpcta_biuldHex_helper((string) dechex($array["red"]));
	$string .=   wpcta_biuldHex_helper((string) dechex($array["green"]));
	$string .=   wpcta_biuldHex_helper((string) dechex($array["blue"]));
	return $string;
}
function  wpcta_biuldHex_helper($number){
	if(strlen($number) == 1) $number = "0".$number;
	return $number;
}

function wpcta_printMarkup($color, $style, $href, $content){	
	$genoratedColors = get_option($color);	
	$hover= substr($genoratedColors, 0, 6);
	$gradient = substr($genoratedColors, 6, 6);
	$border = substr($genoratedColors, 12, 6);
	$text =  substr($genoratedColors, 18, 6);
	$markup = "<a class='wpcta  wpcta_{$color}' style='{$style}' href='{$href}'>$content</a>";
	$markup .=  "<script type='text/javascript'>";
		$markup .= "var css = '";
		$markup .= ".wpcta{";
		  $markup .= "margin: 4px;";	
		  $markup .= "text-shadow: 0 1px rgba(0, 0, 0, 0.1);";
		  $markup .= "text-decoration: none;";
		  $markup .= "font-weight: bold;";
		  $markup .= "text-align: center;";
		  $markup .= "/*Shape*/";
		  $markup .= "-webkit-border-radius: 2px;";
		  $markup .= "-moz-border-radius: 2px;";
		  $markup .= "border-radius: 2px;";
		  $markup .= "padding: 6px 16px;";
		  $markup .= "display: inline-block;";
		$markup .= "}';";
		$markup .= "var wpcta_{$color} = '";
		$markup .= ".wpcta_{$color}{";
		  $markup .= "color: #{$text};";
		  $markup .= "/*Color */";
		  $markup .= "background-color: #{$color};";
		  $markup .= "background-image: -webkit-linear-gradient(top,#{$gradient},#{$color});";
		  $markup .= "background-image: -moz-linear-gradient(top,#{$gradient},#{$color});";
		  $markup .= "background-image: -ms-linear-gradient(top,#{$gradient},#{$color});";
		  $markup .= "background-image: -o-linear-gradient(top,#{$gradient}, #{$color});";
		  $markup .= "background-image: linear-gradient(top,#{$gradient},#{$color});";
		  $markup .= "border: 1px solid #{$border};}";
		$markup .= ".wpcta_{$color}:visited{";
		  $markup .= "border: 1px solid #{$border};";
		  $markup .= "text-decoration: none;";
		$markup .= "}";
		$markup .= ".wpcta_{$color}:hover{";
		  $markup .= "color: #{$text};";
		  $markup .= "background-color: #{$hover};";
		  $markup .= "background-image: -webkit-linear-gradient(top,#{$gradient},#{$hover});";
		  $markup .= "background-image: -moz-linear-gradient(top,#{$gradient},#{$hover});";
		  $markup .= "background-image: -ms-linear-gradient(top,#{$gradient},#{$hover});";
		  $markup .= "background-image: -o-linear-gradient(top,#{$gradient},#{$hover});";
		  $markup .= "background-image: linear-gradient(top,#{$gradient},#{$hover});";
		  $markup .= "border: 1px solid #{$border};";
		  $markup .= "-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);";
		  $markup .= "box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);";
		  $markup .= "text-decoration: none;";
		$markup .= "}";
		$markup .= ".wpcta_{$color}:active {";
		  $markup .= "color:white;";
		  $markup .= "border: 1px solid #{$border};";
		  $markup .= "text-decoration: none;";
		$markup .= "}';";
		$markup .= "var head = document.getElementsByTagName('head')[0]; \n";
		$markup .= "var style = document.createElement('style'); \n";
		$markup .= "if(style2 == null){var style2 = document.createElement('style');style2.type = 'text/css';if (style.styleSheet){ style.styleSheet.cssText =  css;} else {style.appendChild(document.createTextNode(css));} head.appendChild(style2);}";
		$markup .= "style.type = 'text/css'; \n";
		$markup .= "if (style.styleSheet){ \n";
		$markup .= "  style.styleSheet.cssText =  wpcta_{$color}; \n";
		$markup .= "} else { \n";
		$markup .= "  style.appendChild(document.createTextNode(wpcta_{$color})); \n";
		$markup .= "} \n";

		$markup .= " head.appendChild(style);\n";
	$markup .= "</script>\n";
	echo $markup;
}


	

?>
