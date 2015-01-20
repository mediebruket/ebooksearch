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

    add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );

    /**
     * Add stylesheet to the page
     */
    function safely_add_stylesheet() {
        wp_enqueue_style( 'finnebok-shortcode-style', plugins_url('/css/public.css', __FILE__) );
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
//$htmlout .= '</td><td style="border: 0; padding: 0; margin: 0; vertical-align: middle; width: 20%;">';
//$htmlout .= '<input type="submit" value="" class="finnebok_submit" />';
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
//$htmlout .= '</form>';
$htmlout .= '</div>';
$htmlout .= '<div id="results-text" style="display: none; width: ' . $width . '">';
//$htmlout .= '<img style="float: left; margin-right: 2%; margin-bottom: 5px; box-shadow: none; width: 40px;" class="webloftlogo" src="' . plugins_url( 'g/webloftlogo.png', __FILE__ ) . '" alt="Bibliotekarens beste venn / Webløft" />';
$htmlout .= 'Viser maks. ' . $makstreff . ' treff for: <span id="finnebok_search-string"></span><br /><span>S&oslash;ket oppdateres mens du skriver, og kan ta noen sekunder... v&aelig;r t&aring;lmodig! Vil du &aring;pne s&oslash;ket i et eget vindu og eventuelt vise flere treff, klikk <input style="font-size: 1em; padding: 3px; height: 2em; font-weight: bold; vertical-align: top;" type="submit" value="her!"></form></span></div>';
$htmlout .= '<div id="finnebok_results" style="width: ' . $width . '"></div>';

return $htmlout;

}; // end function

add_shortcode("finnebok_skjema", "finnebok_func");

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
//require_once( plugin_dir_path( __FILE__ ) . 'public/class-finnebok.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
//register_activation_hook( __FILE__, array( 'finnebok', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'finnebok', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
//add_action( 'plugins_loaded', array( 'finnebok', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	//require_once( plugin_dir_path( __FILE__ ) . 'admin/class-finnebok_admin.php' );
	//add_action( 'plugins_loaded', array( 'finnebok_admin', 'get_instance' ) );

}






// WIDGET CODE TO FOLLOW

// TODO: change 'Widget_Name' to the name of your plugin
class finnebok_widget extends WP_Widget {

    /**
     * @TODO - Rename "widget-name" to the name your your widget
     *
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'finnebok_widget';

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain - TRENGER IKKE, ALLEREDE GJORT
		// add_action( 'init', array( $this, 'finnebok_widget' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO: update description
		parent::__construct(
			$this->get_widget_slug(),
			__( 'Finn ebok-widget', $this->get_widget_slug() ),
			array(
				'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Sett inn en søkeboks for å finne e-bøker.', $this->get_widget_slug() )
			)
		);

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	} // end constructor


    /**
     * Return the widget slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {


		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset ( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset ( $cache[ $args['widget_id'] ] ) )
			return print $cache[ $args['widget_id'] ];

		// go on with your widget logic, put everything into a string and


		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		// TODO: Here is where you manipulate your widget's values based on their input fields
		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;


		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $widget_string;

	} // end widget


	public function flush_widget_cache()
	{
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// TODO: Here is where you update your widget's old values with the new, incoming values

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		// TODO: Define default values for your variables
		$instance = wp_parse_args(
			(array) $instance
		);

		// TODO: Store the values of the widget in their own variable

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'widget-name' to the name of *your* plugin
		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'languages/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( $this->get_widget_slug().'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		// Bruk itj skiten
		//wp_enqueue_style( $this->get_widget_slug().'-widget-styles', plugins_url( 'css/widget.css', __FILE__ ) );

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/public.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( $this->get_widget_slug().'-tab-script', plugins_url( 'js/tabcontent.js', __FILE__ ), array('jquery') );

	} // end register_widget_scripts

} // end class

// TODO: Remember to change 'Widget_Name' to match the class name definition
add_action( 'widgets_init', create_function( '', 'register_widget("finnebok_widget");' ) );
