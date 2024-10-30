<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://penthion.nl
 * @since      1.0.0
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/public
 * @author     Penthion <dd@penthion.nl>
 */
class Makelaarsservice_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Makelaarsservice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Makelaarsservice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style( $this->plugin_name . '_bulma', plugin_dir_url(__FILE__) . 'css/bulma.min.css', array(), '0.8.0' );
        wp_enqueue_style( $this->plugin_name . '_bulma-carousel', plugin_dir_url(__FILE__) . 'css/bulma-carousel.min.css', array(), '4.0.4' );
        wp_enqueue_style( $this->plugin_name . '_font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
		wp_enqueue_style( $this->plugin_name . '_custom', plugin_dir_url( __FILE__ ) . 'css/makelaarsservice-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Makelaarsservice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Makelaarsservice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_script( $this->plugin_name . '_bulma-carousel', plugin_dir_url( __FILE__ ) . 'js/bulma-carousel.min.js', array(), '4.0.4', false );
		wp_enqueue_script( $this->plugin_name . '_custom', plugin_dir_url( __FILE__ ) . 'js/makelaarsservice-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Load the templates for the properties listing and single property page
     * if there isn't already a template for the custom_post_type present.
     * Param: $template - The template that is loaded for a page call
     * Return: $template - The template to use
     *
     * @since    1.0.0
     */
	public function load_ms_templates( $template ) {
        // Set post type and file names
        $post_types = array( 'estate_property' );
        $file_archive = 'archive-estate_property.php';
        $file_singular = 'single-estate_property.php';

        // Check if request is archive of specified post_type and if a template is present
        if ( is_post_type_archive( $post_types ) && locate_template( array( $file_archive ) ) !== $template )  {
            // Load the plugin's archive template
            $template = plugin_dir_path( __FILE__ ) . $file_archive;
        }

        // Check if request is singular of specified post_type and if a template is present
        if ( is_singular( $post_types ) && locate_template( array( $file_singular ) ) !== $template ) {
            // Load the plugin's singular template
            $template = plugin_dir_path( __FILE__ ) . $file_singular;
        }

        return $template;
    }

}
