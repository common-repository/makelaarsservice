<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://penthion.nl
 * @since      1.0.0
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/admin
 * @author     Penthion <dd@penthion.nl>
 */
class Makelaarsservice_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

    private $wpdb;
    private $option_defaults;
    private $options;
    private $table_name;

	public function __construct( $plugin_name, $version ) {
        global $wpdb;

        $this->wpdb = $wpdb;
		$this->plugin_name = $plugin_name;
        $this->version = $version;
        
        $this->option_defaults = array(
            'color' => '#2babe2',
        );

        $this->table_name = $this->wpdb->prefix . 'pnt_makelaarsservice_tokens';
		$this->options = get_option( 'makelaarsservice-settings-options', $this->option_defaults );
    }
      
    public function welcome_screen_do_activation_redirect() {
        // Bail if no activation redirect
        if ( !get_transient( '_welcome_screen_activation_redirect' ) ) {
            return;
        }

        //Delete the transient
        delete_transient( '_welcome_screen_activation_redirect' );

        // Bail if activating from bulk or network
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            return;
        }

        // Redirect to about page
        wp_safe_redirect( add_query_arg( array( 'page' => $this->plugin_name . '-welcome' ), admin_url( 'admin.php' ) ) );
    }

    public function welcome_screen_pages() {
        $hook_suffix = add_submenu_page(
            $this->plugin_name,
            __( 'Welkom', $this->plugin_name ),
            __( 'Instructies', $this->plugin_name ),
            'read',
            $this->plugin_name . '-welcome',
            array($this, 'welcome_screen_content')
        );

        add_action( 'load-' . $hook_suffix, array($this, 'enqueue_styles' ), 100 );
        add_action( 'load-' . $hook_suffix, array($this, 'enqueue_scripts' ), 100 );
    }

    public function welcome_screen_content() {
        include_once( 'partials/makelaarsservice-welcome-display.php' );
    }

    public function welcome_screen_remove_menus() {
        remove_submenu_page( $this->plugin_name, $this->plugin_name . '-welcome' );
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( $this->plugin_name . '_bulma', plugin_dir_url( __FILE__ ) . 'css/bulma.min.css', array(), '0.8.0', 'all' );
        wp_enqueue_style( $this->plugin_name . '_font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/makelaarsservice-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/makelaarsservice-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );
	}

    /**
     * Register the plug-n menu for the admin area.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        $hook_suffix = add_menu_page(
            'Makelaarsservice',
            'Makelaarsservice',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_settings_page'),
            'dashicons-admin-settings',
            '30'
        );

        add_action( 'load-' . $hook_suffix, array($this, 'enqueue_styles' ), 100 );
        add_action( 'load-' . $hook_suffix, array($this, 'enqueue_scripts' ), 100 );
    }

    /**
     * Register the actions links for the plug-in.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ) {
        $settings_link = array(
            '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __( 'Instellingen', $this->plugin_name )
        );

        return array_merge( $settings_link, $links );
    }

    /**
     * Display the settings page for the plug-in.
     *
     * @since    1.0.0
     */
    public function display_plugin_settings_page() {
        include_once( 'partials/makelaarsservice-admin-display.php' );
    }

    /**
     * Register the plug-in settings for use with the WP Options API
     *
     * @since    1.0.0
     */
    public function display_options() {
        add_settings_section(
            'ms_section',
            __( 'Opties', $this->plugin_name ),
            array( $this, 'display_section' ),
            'makelaarsservice'
        );

        add_settings_field(
            'makelaarsservice-color-field',
            __( 'Accent kleur', $this->plugin_name ),
            array( $this, 'color_settings_field'),
            'makelaarsservice',
            'ms_section'
        );

        register_setting( 'makelaarsservice', 'makelaarsservice-settings-options', array($this, 'validate_options') );
    }

    /**
     * Display settings sections for the WP Options API. Should be empty.
     *
     * @since    1.0.0
     */
    public function display_section() {

    }

    /**
     * Initialize the color settings field.
     *
     * @since    1.0.0
     */
    public function color_settings_field() {
        $val = ( isset( $this->options['color'] ) ) ? $this->options['color'] : '';
        echo '<input type="text" name="makelaarsservice-settings-options[color]" value="' . $val . '" class="makelaarsservice-colorpicker"/>';
    }

    /**
     * Validation function for the settings fields.
     * return: validated fields. If validation fails: error.
     *
     * @since    1.0.0
     *
     */
    public function validate_options( $fields ) {
        $valid_fields = array();

        //Validate color field
        $color = trim( $fields['color'] );

        if ( FALSE === $this->check_color( $color ) ) {

        } else {
            $valid_fields['color'] = $color;
        }

        return apply_filters( 'validate_options', $valid_fields, $fields );
    }

    /**
     * Check if value is a valid color in HEX format.
     * Return: boolean
     *
     * @since    1.0.0
     *
     */
    public function check_color( $value ) {
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) return true;

        return false;

    }

    /**
     * Initializes custom post types with taxonomies if not already present.
     *
     * Post types: estate_property (Objects), estate_agent (Realtors)
     * Taxonomies (estate_property): property_city, property_category
     * Taxonomies (estate_agent): property_city_agent
     *
     * @since    1.0.0
     *
     */
    public function add_cpt() {
        if ( post_type_exists('estate_property') === false ) {
            register_post_type( 'estate_property', array(
                    'labels' 		=> array(
                        'name'					=> __( 'Objecten', $this->plugin_name ),
                        'singular_name'			=> __( 'Object', $this->plugin_name ),
                        'add_new'				=> __( 'Nieuw object toevoegen', $this->plugin_name ),
                        'add_new_item'			=> __( 'Object toevoegen', $this->plugin_name ),
                        'edit'					=> __( 'Bewerken', $this->plugin_name ),
                        'edit_item'				=> __( 'Object bewerken', $this->plugin_name ),
                        'view'					=> __( 'Bekijken', $this->plugin_name ),
                        'view_item'				=> __( 'Object bekijken', $this->plugin_name ),
                        'search_items'			=> __( 'Object zoeken', $this->plugin_name ),
                        'not_found'				=> __( 'Geen objecten gevonden', $this->plugin_name ),
                        'not_found_in_trash' 	=> __( 'Geen objecten in prullenmand gevonden', $this->plugin_name ),
                    ),
                    'public'		=> true,
                    'has_archive'	=> true,
                    'rewrite'		=> array( 'slug' => __('objecten', $this->plugin_name ) ),
                    'menu_icon'		=> 'dashicons-admin-multisite',
                    'supports'		=> array(
                        'title', 'editor', 'thumbnail'
                    ),
                )
            );
        }

        if ( taxonomy_exists( 'property_city') === false ) {
            register_taxonomy( 'property_city', array( 'estate_property' ), array(
                    'labels' => array(
                        'name'			=> __( 'Plaats', $this->plugin_name ),
                        'add_new_item'	=> __( 'Plaats toevoegen', $this->plugin_name ),
                        'new_item_name'	=> __( 'Nieuwe plaats', $this->plugin_name ),
                        'not_found'		=> __( 'Geen plaatsen gevonden', $this->plugin_name ),
                    ),
                    'hierarchical' 	=> true,
                    'query_var'		=> true,
                )
            );
        }

        if ( taxonomy_exists( 'property_category' ) === false ) {
            register_taxonomy( 'property_category', array( 'estate_property' ), array(
                    'lables' => array(
                        'name'			=> __( 'Categorie', $this->plugin_name ),
                        'add_new_item'	=> __( 'Categorie toevoegen', $this->plugin_name ),
                        'new_item_name'	=> __( 'Nieuwe categorie', $this->plugin_name ),
                        'not_found'		=> __( 'Geen categorieën gevonden', $this->plugin_name ),
                    ),
                    'hierarchical'	=> true,
                    'query_var'		=> true,
                )
            );
        }

        if ( post_type_exists('estate_agent') === false ) {
            register_post_type( 'estate_agent', array(
                    'labels' 		=> array(
                        'name'					=> __( 'Makelaars', $this->plugin_name ),
                        'singular_name'			=> __( 'Makelaar', $this->plugin_name ),
                        'add_new'				=> __( 'Nieuwe makelaar toevoegen', $this->plugin_name ),
                        'add_new_item'			=> __( 'Makelaar toevoegen', $this->plugin_name ),
                        'edit'					=> __( 'Bewerken', $this->plugin_name ),
                        'edit_item'				=> __( 'Makelaar bewerken', $this->plugin_name ),
                        'view'					=> __( 'Bekijken', $this->plugin_name ),
                        'view_item'				=> __( 'Makelaar bekijken', $this->plugin_name ),
                        'search_items'			=> __( 'Makelaar zoeken', $this->plugin_name ),
                        'not_found'				=> __( 'Geen makelaars gevonden', $this->plugin_name ),
                        'not_found_in_trash' 	=> __( 'Geen makelaars in prullenmand gevonden', $this->plugin_name ),
                    ),
                    'public'		=> true,
                    'has_archive'	=> true,
                    'rewrite'		=> array( 'slug' => __('makelaars', $this->plugin_name ) ),
                    'menu_icon'		=> 'dashicons-admin-multisite',
                )
            );
        }

        if ( taxonomy_exists( 'property_city_agent') === false ) {
            register_taxonomy( 'property_city_agent', array( 'estate_agent' ), array(
                    'labels' => array(
                        'name'			=> __( 'Plaats', $this->plugin_name ),
                        'add_new_item'	=> __( 'Plaats toevoegen', $this->plugin_name ),
                        'new_item_name'	=> __( 'Nieuwe plaats', $this->plugin_name ),
                    ),
                    'hierarchical' 	=> true,
                    'query_var'		=> true,
                )
            );
        }
    }

    /**
     * Get either all tokens or a single token from the database.
     * Parameters: $token_id (optional) - When provided, only the token with matching ID is retrieved.
     * Return: Array with the token.
     *
     * @since    1.0.0
     *
     */
    public function get_tokens( $token_id = '' ) {
        $query = "SELECT * FROM $this->table_name WHERE is_active=1" . ( !empty( $token_id ) ? " AND token_id=$token_id" : "" );

        return $this->wpdb->get_results( $query );
    }

    /**
     * Perform various API requests.
     * Parameters: $token - the token to use for the API request, $function - The API function to call
     * Return: Decoded JSON reponse
     *
     * @since    1.0.0
     *
     */
    public function api_request( $token, $function ) {
        $url = "https://trial-api.ad4all.nl/api/$function";

        // Set API call header, body
        $args = array(
            'headers' => array(
                'Accept'		=> 'application/json',
                'Authorization'	=> "Basic $token",
            ),
            'body' => array(
                'token'			=> $token,
            ),
        );

        // Run API call, save result and decode.
        $result = wp_remote_post( $url, $args );
        $decoded = json_decode( $result['body'] );

        // Check if token is valid.
        if (array_key_exists('error', $decoded)) {
            return "invalid";
        }

        switch ( $function ) {
            case 'check_token':
                // Return token/realtor info.
                $xml = simplexml_load_string( $decoded->response );
                return $xml->advertiser;
                
                break;
            case 'advertiser_real_estates':
                // Return all real_estates.
                return $decoded;
                break;
            default:
                // No valid API function is provided.
                return 'Unsupported';
        }

    }

    /**
     * Synchronizes all tokens on page load via add_estate_properties function.
     *
     * @since    1.0.0
     *
     */
    public function pageload_add_estate_properties() {
        $this->add_estate_properties( $this->get_tokens() );
    }

    /**
     * Ajax callback function that runs add_estate_properties function with given tokens
     * Return: If token invalid, return invalid. Else: return timestamp
     *
     * @since    1.0.0
     *
     */
    public function ajax_add_estate_properties() {
        $token_id = sanitize_text_field( $_POST['token_id'] );

        if ( $_POST['type'] === 'all' ) {
            $tokens = $this->get_tokens();
        } else if ( $_POST['type'] === 'single' ) {
            $tokens = $this->get_tokens( $token_id );
        } else {
            echo "Invalid token type";
            wp_die();
        }

        $this->add_estate_properties( $tokens );

        echo $this->get_update_timestamp( $token_id );

        wp_die();
    }

    /**
     * Add/update estate properties for each provided token.
     * Return: If token invalid, return invalid. Else: return timestamp
     *
     * @since    1.0.0
     *
     */
    public function add_estate_properties( $tokens ) {
        foreach ( $tokens as $token ) {
            // Get id's of current estate properties.
            $current_objects = $this->get_current_ids();

            $new_objects = array();

            // API call to recieve all current objects of the given token and store the xml
            $result = $this->api_request( $token->token, 'advertiser_real_estates' );

            if($result == "invalid") {
                return;
            }

            $xml = simplexml_load_string( $result->response );

            // Loop through the returned xml and process each object
            foreach ( $xml->real_estates->real_estate as $value ) {
                $id = sanitize_text_field( $value->id );
                $new_objects[] = $value->id;

                //Check if object exists
                $table = 'wp_postmeta';

                $exists = $this->wpdb->get_results(
                    "
					SELECT * FROM $table 
					WHERE meta_key='ms_property_updated_at' 
					AND post_id=(
						SELECT post_id from $table 
						WHERE meta_key='ms_property_id'
						AND meta_value='$value->id'
						)
					"
                );

                if ( $exists == NULL ) {
                    //Object doesn't exist yet. Store in WP using store_property function.
                    $this->store_property( $value, $token->agent_id );
                } else if ( $exists[0]->meta_value < strtotime( $value->updated_at ) ) {
                    //Object exists but is outdated. Update in WP using update_property function.
                    $this->update_property( $value, $exists[0]->post_id );
                }
            }

            // Check if current properties in WP are still present in Makelaarsservice.
            foreach( $current_objects as $object_id ) {
                if( !in_array( $object_id->meta_value, $new_objects ) ) {
                    //Property not present in Makelaarsservice. Delete from WP as well.
                    $this->delete_property( $object_id->post_id );
                }
            }

            // Set updated_at timestamp to current time
            $this->wpdb->query( "UPDATE $this->table_name SET updated_at=CURRENT_TIMESTAMP WHERE token='$token->token'" );
        }
    }

    /**
     * Get updated_at field of given token.
     * Param: $token_id
     * Return: timestamp
     *
     * @since    1.0.0
     *
     */
    public function get_update_timestamp( $token_id ) {
        $row = $this->wpdb->get_row( "SELECT updated_at FROM $this->table_name WHERE token_id='$token_id'" );

        return $row->updated_at;
    }

    /**
     * Store provided property in WP and assign property to provided agent.
     * Param: $property - All property info, $agent_id
     *
     * @since    1.0.0
     *
     */
    public function store_property( $property, $agent_id ) {
        // create estate_property post
        $post_id = wp_insert_post(
            array(
                'post_content' 		=> (string) $property->description,
                'post_title' 		=> sanitize_text_field( (string) $property->address ) . " " . sanitize_text_field( (string) $property->city ),
                'post_status'		=> 'publish',
                'post_type'			=> 'estate_property',
            )
        );

        // Initialize Makelaarsservice properties in the correct format for the post.
        $ms_properties = $this->set_ms_properties( $property, $post_id );

        // Initialize WPEstate properties
        $wp_properties = array(
            'property_rooms'					=> '',
            'property_bathrooms'				=> '',
            'owner_notes'						=> '',
            'property_status'					=> 'normal',
            'prop_featured'						=> '0',
            'property_theme_slider'				=> '0',
            'image_to_attach'					=> '',
            'embed_video_type'					=> 'vimeo',
            'embed_video_id'					=> '',
            'embed_virtual_tour'				=> '',
            'property-garage-size'				=> '',
            'property-date'						=> '',
            'property-basement'					=> '',
            'property-external-construction' 	=> '',
            'property-roofing'					=> '',
            'page_custom_zoom'					=> '14',
            'property_google_view'				=> '',
            'google_camera_angle'				=> '0',
            'property_agent'					=> $agent_id,
            'post_show_title'					=> 'yes',
            'header_type'						=> '2',
            'header_transparent'				=> 'global',
            'local_pgpr_slider_type'			=> 'global',
            'local_pgpr_content_type'			=> 'global',
            'sidebar_option'					=> 'right',
            'adv_filter_search_action'			=> '',
            'adv_filter_search_category'		=> '',
            'current_adv_filter_city'			=> '',
            'current_adv_filter_area'			=> '',
            'slide_template'					=> 'default',
        );

        $properties = array_merge( $ms_properties, $wp_properties );

        //Images
        $menu_order_count = 1;
        $attachments = '';

        foreach ( $property->photos->photo as $photo ) {
            // Store photo in WP
            $attachment_id = $this->store_photo( $post_id, $property->id, $photo, $menu_order_count );

            // Set post thumbnail
            if ( $menu_order_count === 1 ) $properties['_thumbnail_id'] = $attachment_id;
            $menu_order_count++;

            $attachments .= $attachment_id . ',';
        }

        // Add images to post attachments
        $properties['image_to_attach'] = $attachments;

        // Add post properties as WP post meta
        foreach ( $properties as $key => $value ) {
            add_post_meta( $post_id, $key, $value );
        }

    }

    /**
     * Update property in WP with new Makelaarsservice values
     * Param: $property - Property info, $post_id - The ID of the post to update
     *
     * @since    1.0.0
     *
     */
    public function update_property( $property, $post_id ) {
        $current_attachments = get_attached_media( '', $post_id );
        $current_urls = array();
        $new_urls = array();
        $header_img = get_post_meta( $post_id, '_thumbnail_id', true );
        $image_to_attach = get_post_meta( $post_id, 'image_to_attach', true );
        $menu_order_count = ( $header_img === false ) ? '1' : count( $current_urls ) + 1;

        //Get current post attachments and store url's.
        foreach ($current_attachments as $v) {
            $current_urls[] = $v->guid;
        }

        // Add new photos
        foreach ( $property->photos->photo as $photo ) {
            $new_urls[] = $photo->url;

            // If photo isn't in WP yet, add to WP.
            if ( in_array( $photo->url, $current_urls ) == false ) {
                $attachment_id = $this->store_photo( $post_id, $property->id, $photo, $menu_order_count );
                $image_to_attach .= "$attachment_id,";

                if ( $menu_order_count === 1 ) $thumbnail = $attachment_id;

                $menu_order_count++;
            }
        }

        // Initialize Makelaarsservice properties in the correct format for the post.
        $properties = $this->set_ms_properties( $property, $post_id );

        $properties['image_to_attach'] = $image_to_attach;
        if ( isset( $thumbnail ) ) $properties['_thumbnail_id'] = $thumbnail;

        // Check if image in WP is still present in Makelaarsservice. If not, delete from WP.
        foreach ( $current_attachments as $v ) {
            if ( !in_array( $v->guid, $new_urls ) ) {
                //Delete attachment by id.
                wp_update_post( array('ID' => $v->ID, 'post_parent' => '0' ));
                wp_delete_attachment( $v->ID, true );
            }
        }

        //Update post content
        $post = array(
            'ID' => $post_id,
            'post_content' => $property->description,
        );

        wp_update_post($post);

        // Update post properties as WP post meta
        foreach ( $properties as $key => $value ) {
            update_post_meta( $post_id, $key, $value );
        }

    }

    /**
     * Delete a property in WP
     * Param: $post_id - The ID of the post to delete
     *
     * @since    1.0.0
     *
     */
    public function delete_property( $post_id ) {
        wp_delete_post( $post_id );
    }

    /**
     * Delete post attachments
     * Param: $post_id - The ID of the post to which the attachments belong
     *
     * @since    1.0.0
     *
     */
    public function delete_attachments( $post_id ) {
        if ( get_post_type( $post_id ) == 'estate_property' ) {
            foreach ( get_attached_media( '', $post_id ) as $attachment ) {
                wp_delete_attachment( $attachment->ID, 'true' );
            }
        }
    }

    /**
     * Reformat the Makelaarsservice for use as WP post meta
     * Param: $property - Property info from Makelaarsservice
     * Return: array with properly formatted info
     *
     * @since    1.0.0
     *
     */
    public function set_ms_properties( $property, $post_id ) {
        $sanitized = array();

        foreach ( $property as $k => $v ) {
            $sanitized[$k] = sanitize_text_field( (string) $v );
        }

        // Set post terms for property_city, property_category
        wp_set_post_terms( $post_id, $this->check_term_city( $sanitized['city'], 'property_city' ), 'property_city' );
        wp_set_post_terms( $post_id, $this->check_term_category( $sanitized['kind'], 'property_category' ), 'property_category' );

        $properties = array(
            'ms_property_id'					=> $sanitized['id'] ,
            'property_price'					=> substr_replace( ($sanitized['for_sale'] == 'true' ? $sanitized['sale_price'] : $sanitized['rental_price']), '.', -2, -2 ),
            'property_label'					=> ($sanitized['for_sale'] == 'true' ? $sanitized['sale_postfix'] : $sanitized['rental_postfix']),
            'property_label_before' 			=> ($sanitized['for_sale'] == 'true' ? $sanitized['sale_prefix'] : $sanitized['rental_prefix']),
            'property_address' 					=> $sanitized['address'],
            'property_zip'						=> $sanitized['zipcode'],
            'property_country'					=> 'Netherlands',
            'property_size'						=> $sanitized['living_area'],
            'property_lot_size'					=> $sanitized['parcel_size'],
            'property_bedrooms'					=> $sanitized['bedrooms'],
            'property-year'						=> $sanitized['construction'] . "-01-01",
            'property-garage'					=> $sanitized['garage'],
            'property_latitude'					=> $sanitized['latitude'],
            'property_longitude'				=> $sanitized['longitude'],
            'ms_property_kind'					=> $sanitized['kind'],
            'ms_property_content'				=> $sanitized['content'],
            'ms_property_energy_label'			=> $sanitized['energy_label'],
            'ms_property_date_of_acceptance'	=> $sanitized['date_of_acceptance'],
            'ms_property_joint_costs'			=> $sanitized['joint_costs'],
            'ms_property_new_construction'		=> $sanitized['new_construction'],
            'ms_property_maintenance'			=> $sanitized['maintenance'],
            'ms_property_garden'				=> $sanitized['garden'],
            'ms_property_insulation'			=> $sanitized['insulation'],
            'ms_property_wm_home'				=> $sanitized['wm_home'],
            'ms_property_wm_top'				=> $sanitized['wm_top'],
            'ms_property_sold'					=> $sanitized['sold'],
            'ms_property_created_at'			=> strtotime( $sanitized['created_at'] ),
            'ms_property_updated_at'			=> strtotime( $sanitized['updated_at'] ),
        );

        return $properties;
    }

    /**
     * Store photo as attachment to post in WP
     * Param: $post_id, $property_id, $photo - The photo, $menu_order_count - Count in WP
     * Return: string - the ID of the attachment created
     *
     * @since    1.0.0
     *
     */
    public function store_photo( $post_id, $property_id, $photo, $menu_order_count ) {
        // Generate filename for the iamge
        $filename = 'property_ ' . $property_id . '_name_' . sanitize_text_field( (string) $photo->name );
        // Retrieve image info like width, height, mime type
        $info = @getimagesize( esc_url_raw( trim( $photo->url ) ) );

        // Set attachment properties and metadata
        $attachment = array(
            'guid' 				=> esc_url_raw( trim( $photo->url ) ),
            'post_mime_type' 	=> $info['mime'],
            'post_title' 		=> $filename,
            'post_parent' 		=> $post_id,
            'menu_order'		=> $menu_order_count,
        );

        $attachment_meta = array(
            'width' 			=> $info[0],
            'height' 			=> $info[1],
            'file'				=> $filename,
        );

        //Insert attachment and update attachment metadata
        $attachment_id = wp_insert_attachment( $attachment );
        wp_update_attachment_metadata( $attachment_id, $attachment_meta );

        return $attachment_id;
    }

    /**
     * Check if provided city exists as a term.
     * If so, return existing term_id. If not, add new term.
     * Param: $city - The property city
     * Return: string - the ID of the term
     *
     * @since    1.0.0
     *
     */
    public function check_term_city( $city, $taxonomy ) {
        $term = term_exists( $city, $taxonomy );

        if ( $term !== 0 && $term !== null ) {
            // City exists. Set $term_id to existing value.
            $term_id = $term;
        } else {
            // City doesn't exist. Insert city as term.
            $term_id = wp_insert_term(
                $city,
                $taxonomy,
                array( 'slug' => strtolower( $city ) )
            );
        }

        return $term_id;
    }

    /**
     * Check if provided category exists as a term.
     * If so, return existing term_id. If not, add new term.
     * Param: $category - The property category
     * Return: string - the ID of the term
     *
     * @since    1.0.0
     *
     */
    public function check_term_category( $category, $taxonomy ) {
        $term = term_exists( $category, $taxonomy );

        if ( $term !== 0 && $term !== null ) {
            // Category exists. Set $term_id
            $term_id = $term;
        } else {
            // Category doesn't exists. Add as new term.
            $term_id = wp_insert_term(
                ucfirst( $category ),
                $taxonomy,
                array( 'slug' => strtolower( $category ) )
            );
        }

        return $term_id;
    }

    /**
     * Check if agent exists in WP by name
     * Param: $name - Name of the agent as set in Makelaarsservice
     * Return: boolean if agent doesn't exist, post_id of agent if exists
     *
     * @since    1.0.0
     *
     */
    public function check_agent( $name ) {
        $post_id = post_exists( $name, '', '', 'estate_agent' );

        // If post_id 0/null, agent doesn't exist => return false. Else return post_id.
        return ( $post_id === 0 || $post_id === null ) ? false : $post_id;
    }

    /**
     * Add agent to WP
     * Param: $info - Info of the agent like name, city, email etc.
     * Return: post_id of newly added agent
     *
     * @since    1.0.0
     *
     */
    public function add_agent( $info ) {
        $post_id = wp_insert_post( array(
            'post_title'		=> sanitize_text_field( (string) $info->name ),
            'post_status'		=> 'publish',
            'post_type'			=> 'estate_agent',
        ) );

        $properties = array(
            'agent_email'		=> sanitize_email( (string) $info->email ),
            'agent_phone'		=> sanitize_text_field( (string) $info->phone ),
            'agent_address'     => sanitize_text_field( (string) $info->address ),
            'agent_zipcode'     => sanitize_text_field( (string) $info->zipcode ),
        );

        // Add agent city
        wp_set_post_terms( $post_id, $this->check_term_city( sanitize_text_field( (string) $info->city ), 'property_city_agent' ), 'property_city_agent' );

        // Set agent properties as post meta
        foreach ( $properties as $key => $value ) {
            add_post_meta( $post_id, $key, $value );
        }

        return $post_id;
    }

    /**
     * Ajax function callback that runs add_token function
     * Return: return data from add_token function
     *
     * @since    1.0.0
     *
     */
    public function ajax_add_token() {
        $return = $this->add_token( sanitize_text_field( $_POST['token'] ) );
        echo json_encode( $return );
        wp_die();
    }

    /**
     * Add a Makelaarsservice token to WP.
     * Return: if token invalid: "invalid", if token exists: "exists", else: token/agent data
     *
     * @since    1.0.0
     *
     */
    public function add_token( $token ) {
        // Check if token is valid
        $info = $this->api_request( $token, 'check_token' );

        if ( $info === "invalid" ) {
            return "invalid";
        } else if ( $this->token_exists( $token ) === true ){
            return "exists";
        } else {
            // Check if agent exists
            $agent = $this->check_agent( $info->name );
            $agent_id = $agent ? $agent : $this->add_agent( $info );

            // Insert token into database
            $this->wpdb->insert( $this->table_name, array(
                'name' 		=> $info->name,
                'token' 	=> $token,
                'is_active' => '1',
                'agent_id' 	=> $agent_id,
            ));

            // Retrieve insert_id from inserted row
            $insert_id = $this->wpdb->insert_id;

            $data = array(
                'token_id' => $insert_id,
                'name'		=> $info->name,
                'agent_id'	=> $agent_id,
            );

            return $data;
        }
    }

    /**
     * Ajax callback to run pause_token function
     *
     * @since    1.0.0
     *
     */
    public function ajax_pause_token() {
        $this->pause_token( sanitize_text_field( $_POST['token_id'] ) );
    }

    /**
     * Pause synchronisation of a token
     * Param: $token_id - ID of the token to pause
     * @since    1.0.0
     *
     */
    public function pause_token( $token_id ) {
        // Get current is_active status of token
        $status = $this->wpdb->get_row( "SELECT * FROM $this->table_name WHERE token_id='$token_id'" );

        // If token is in-active (0), set to active (1). Else, set to in-active
        $is_active = ( $status->is_active == 0 ) ? 1 : 0;

        // Update the is_active field in the database
        $this->wpdb->query( "UPDATE $this->table_name SET is_active=$is_active WHERE token_id='$token_id'" );
    }

    /**
     * Ajax callback to run delete_token function
     *
     * @since    1.0.0
     *
     */
    public function ajax_delete_token() {
        $this->delete_token( sanitize_text_field( $_POST['token_id'] ) );
    }

    /**
     * Delete a token
     * Param: $token_id - The ID of the token to delete
     * Return: string "success"
     * @since    1.0.0
     *
     */
    public function delete_token( $token_id ) {
        // Query to delete token row from database
        $this->wpdb->query( "DELETE FROM $this->table_name WHERE token_id='$token_id'" );
    }

    /**
     * Get the is_active status of a token from the database
     * Param: $token_id - The ID of the token to check
     * Return: array with status values
     * @since    1.0.0
     *
     */
    public function is_active( $token_id ) {
        $token = $this->get_tokens( $token_id );

        if ( $this->api_request( $token[0]->token, 'check_token' ) == "invalid" ) {
            // Token is invalid (in-active)
            $arr = array(
                'status' => '3',
                'color' => 'danger',
                'message' => __( 'Ongeldig', $this->plugin_admin ),
            );

            return $arr;
        }

        // Check if token is active or paused
        if ( $token[0]->is_active == false ) {
            // Token is paused. Set appropriate color/text
            $arr = array(
                'status' => '2',
                'color' => 'warning',
                'message' => __( 'Gepauzeerd', $this->plugin_admin ),
            );
        } else {
            
            // Token is active. Set appropriate color/text
            $arr = array(
                'status' => '1',
                'color' => 'success',
                'message' => __( 'Actief', $this->plugin_admin ),
            );
        }

        return $arr;
    }

    /**
     * Check if a token exists in the database
     * Param: $token - The ID of the token to check
     * Return: boolean
     * @since    1.0.0
     *
     */
    public function token_exists( $token ) {
        $exists = $this->wpdb->get_var( "SELECT token_id FROM $this->table_name WHERE token='$token'" );

        return ($exists == null) ? false : true;
    }

    /**
     * Get attachments of post
     * Param: $token - The ID of the post for which to get the attachments of
     * Return: array with attachments
     * @since    1.0.0
     *
     */
    public function get_attachments( $post_id ) {
        $attachments = get_attached_media('', $post_id);

        return $attachments;
    }

    /**
     * Get the id's of currently presented properties
     * Return: array with the id's
     * @since    1.0.0
     *
     */
    public function get_current_ids() {
        $results = $this->wpdb->get_results( "SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='ms_property_id'" );

        return $results;
    }
}
