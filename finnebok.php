<?php

/**
 *
 * @package   Ebook Search by Webloft
 * @author    Håkon Sundaune <haakon@bibliotekarensbestevenn.no>
 * @license   GPL-3.0+
 * @link      http://www.bibvenn.no
 * @copyright 2014 Sundaune
 *
 * @wordpress-plugin
 * Plugin Name:       Ebook Search by Webloft
 * Plugin URI:        http://www.bibvenn.no/finnebok
 * Description:       S&oslash;ker etter gratis PDF- og e-b&oslash;ker / search for free PDFs and e-books
 * Version:           1.0.3
 * Author:            H&aring;kon Sundaune
 * Author URI:        http://www.sundaune.no
 * Text Domain:       finnebok-locale
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

// INCLUDE NECESSARY

add_action( 'wp_enqueue_scripts', 'ebok_register_assets' );

/**
 * Only register stylesheet. Enqueue when needed.
 */
function ebok_register_assets() {
  wp_register_style( 'finnebok-shortcode-style', plugins_url('/css/public.css', __FILE__) );
  wp_register_script( 'finnebok-script', plugins_url( 'js/public.js', __FILE__ ), array('jquery') );
  wp_register_script( 'finnebok-tab-script', plugins_url( 'js/tabcontent.js', __FILE__ ), array('jquery') );
}

// FIRST COMES THE SHORTCODE... EH, CODE!

function finnebok_func ($atts){

  extract(shortcode_atts(array(
    'width' => "250px",
    'dummy' => "dummytekst",
    'makstreff' => "25",
    'show_heading' => false,
    'show_share_links' => false
    ), $atts));

  if ( $show_heading === 'false' ) {
    $show_heading = false;
  }
  $show_heading = (boolean) $show_heading;

  if ( $show_share_links === 'false' ) {
    $show_share_links = false;
  }
  $show_share_links = (boolean) $show_share_links;

    if ($makstreff > 100) { // ikke vær dust'a
    $makstreff = 100;
  }

  // DEFINE HTML TO OUTPUT WHEN SHORTCODE IS FOUND

  $width = strip_tags(stripslashes($width));

  $htmlout = '<script type="text/javascript">';
  $htmlout .= "var pluginsUrl = '" . plugins_url('/search.php' , __FILE__) . "'";
  $htmlout .= "/***********************************************";
  $htmlout .= "* Tab Content script v2.2- © Dynamic Drive DHTML code library (www.dynamicdrive.com)";
  $htmlout .= "* This notice MUST stay intact for legal use";
  $htmlout .= "* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code";
  $htmlout .= "***********************************************/";
  $htmlout .= '</script>';
  $htmlout .= '<div class="ebok_skjema" style="width: ' . $width . '">';

  if ( $show_heading ) {
    $htmlout .= '<h2>S&oslash;k i e-bok og PDF</h2>';
  }

  $htmlout .= '<form target="_blank" method="GET" action="' . plugins_url('ebok_fullpagesearch.php' , __FILE__) . '">';
  $htmlout .= '<table style="width: 85%; border: 0; margin: 0; padding: 0;"><tr><td style="border: 0; padding: 0; margin: 0; vertical-align: middle; width: 80%;">';
  $htmlout .= '<input name="query" type="text" autocomplete="off" id="finnebok_search" placeholder="S&oslash;k etter..." />';
  $htmlout .= '</td></tr></table>';
  $htmlout .= '<input type="hidden" name="makstreff" id="finnebok_makstreff" value="' . $makstreff . '" />';
  $htmlout .= '<input type="hidden" name="show_share_links" id="finnebok_show_share_links" value="' . $show_share_links . '" />';
  $htmlout .= '<div class="sjekkbokser">';
  $htmlout .= '<input id="finnebok_epub" type="checkbox" style="vertical-align: middle;" name="epub" value="2" checked>';
  $htmlout .= '<label for="finnebok_epub">.epub</label>&nbsp;&nbsp;';
  $htmlout .= '<input id="finnebok_pdf" type="checkbox" style="vertical-align: middle;" name="pdf" value="1" checked>';
  $htmlout .= '<label for="finnebok_pdf">.pdf</label>';
  $htmlout .= '</div>';
  $htmlout .= '<br style="clear: both;">';
  $htmlout .= '</div>';
  $htmlout .= '<div id="ebs_loader" class="small"><div></div></div>';
  $htmlout .= '<div id="results-text" style="display: none; width: ' . $width . '">';
  $htmlout .= 'Viser maks. ' . $makstreff . ' treff for: <span id="finnebok_search-string"></span><br /><span>S&oslash;ket oppdateres mens du skriver, og kan ta noen sekunder... v&aelig;r t&aring;lmodig! Vil du &aring;pne s&oslash;ket i et eget vindu og eventuelt vise flere treff, klikk <input style="font-size: 1em; padding: 3px; height: 2em; font-weight: bold; vertical-align: top;" type="submit" value="her!"></form></span></div>';
  $htmlout .= '<div id="finnebok_results" style="width: ' . $width . '"></div>';

  return $htmlout;

}; // end function

add_shortcode("finnebok_skjema", "finnebok_func");

function ebok_enqueue_style() {
  global $post;
  if ( is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'finnebok_skjema' ) ) {
    wp_enqueue_style('finnebok-shortcode-style');
    wp_enqueue_script( 'finnebok-script' );
    wp_enqueue_script( 'webloft-tab-script' ); // in order to prevent enqueueing a script more than once if localhistory search is active
  }
}
add_action( 'wp_enqueue_scripts', 'ebok_enqueue_style');
