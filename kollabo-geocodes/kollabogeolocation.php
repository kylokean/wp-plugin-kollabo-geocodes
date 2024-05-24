<?php
/*
Plugin Name: Kollabo Geocodes
Description: Replacing Google location codes with location names
Version: 1.0
Author: Kylokean
Author URI: http://aletheme.com
License: GPLv2 or later
Text Domain: kollabogeolocation
*/
if(!defined('ABSPATH')){
    die;
}


class kollabogeocodes {


    // connect backend css/js 
    public function enqueue_admin() {
        wp_enqueue_style ('kbgeocode_backend_style', plugins_url('/admin/style-backend.css', __FILE__));
        wp_enqueue_script('kbgeocode_backend_script', plugins_url('/admin/script-backend.js', __FILE__), array('jquery'),'1.0', true);
    }

    // connect frontend css/js
    public function enqueue_front() {
        wp_enqueue_style ('kbgeocode_frontend_style', plugins_url('/front/style-frontend.css', __FILE__));
        wp_enqueue_script('kbgeocode_frontend_script', plugins_url('/front/script-frontend.js', __FILE__), array('jquery'),'1.0', true);
    }


    public function register() {

        // action backend css/js
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);

        // action frontend css/js
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);

        // added template_redirect action to apply 'is_page' function inside plugin
        add_action('template_redirect', [$this, 'activate_plugin_on_selected_pages']);

        // connect settings in the admin backend    
        add_action('admin_menu', [$this, 'add_menu_item']);

        add_action('admin_init', [$this, 'settings_init']);


    }


    public function settings_init() {
        register_setting('kollabogeolocation_settings', 'kollabogeolocation_settings_options');

        add_settings_section('kollabogeolocation_settings_section', esc_html__('List of pages and their IDs', 'kollabogeolocation'), [$this, 'kollabogeolocation_settings_section_html'], 'kollabogeolocation_settings');

        add_settings_field('checkbox_element', esc_html__('Check needed pages:', 'kollabogeolocation'), [$this, 'checkbox_element_callback'], 'kollabogeolocation_settings', 'kollabogeolocation_settings_section');  

    }



    public function checkbox_element_callback() {

        $options = get_option('kollabogeolocation_settings_options'); // this is an array
        $checked_pages = isset( $options['checkbox_element'] ) ? (array) $options['checkbox_element'] : [];

        
        // Loop through $checked_pages array
        // echo '<br>Plugin activated on those pages:</br>';
        // foreach($checked_pages as $key => $value){            
        //     echo $key . " : " . $value . "<br>";;
        // }

        $post_args = array (
            'order'          => 'ASC',
            'orderby'          => 'title',
            'numberposts'      => -1,
            'post_type'    => 'page', // output only pages            
            'post_status'  => 'publish'
        ); 

        echo '<div class="post_type_allpages">';
        // echo '<h4>Post type: '.$post_type->labels->singular_name.'</h4>';
        $all_posts = get_posts($post_args);        
            foreach ( $all_posts as $single_post ) {
                $html = '<div class="post_type_singlepage">';
                $checked = in_array($single_post->ID, $checked_pages) ? 'checked' : "";
                $html .= '<input type="checkbox" id="'.$single_post->ID.'" name="kollabogeolocation_settings_options[checkbox_element][]" value="'.$single_post->ID.'"'.$checked. '/>';
                $html .= '<label for="'.$single_post->ID.'"><a href="'.get_permalink($single_post->ID).'">' .$single_post->post_title.' ('.$single_post->ID. ')</a></label>';
                $html .= '</div>';
                echo $html;                
              } 
            echo '</div>';
        } // end of the foreach loop 




    public function kollabogeolocation_settings_section_html() {
        esc_html_e('Select pages where to activate plugin');
    }
    



    // output plugin setting page in the admin panel    
    public function add_menu_item() {
        add_menu_page(
            esc_html__('Kollabogeocodes Settings Page', 'kollabogeolocation'),
            esc_html__('Kollabo Geocodes', 'kollabogeolocation'),
            'manage_options',
            'kollabogeocodes_settings',  // URL slug of the settings page
            [$this, 'main_admin_page'],
            'dashicons-location-alt',
            90,
        );

        add_submenu_page(
            'kollabogeocodes_settings',  // URL slug of the parent settings page,
            esc_html__('Location Management', 'kollabogeolocation'),
            esc_html__('Location Management', 'kollabogeolocation'),
            'manage_options', 
            'csvupload',  // URL slug of the current settings page
            [$this, 'uploadfile_callback'] //The function to be called to output the content for this page.
        );

    }

    // create page with select pages settings    
    public function main_admin_page() {
        // echo 'Hello, this is settings page';
        require_once __DIR__ . '/admin/select-pages.php';
    }

    
       // create page with CSV upload settings    
       public function uploadfile_callback() {
        require_once __DIR__ . '/admin/upload-csv.php';
    }

    // Define frontend pages where to output plugin code
    public function activate_plugin_on_selected_pages() {

        // echo "Start Function initialized";

        $options = get_option('kollabogeolocation_settings_options');

        // convert array of checkbofes to string
        if(isset($options['checkbox_element'])){
             $checked_pages_array = isset( $options['checkbox_element'] ) ? (array) $options['checkbox_element'] : [];
                if (is_page($checked_pages_array) || is_single($checked_pages_array)) {
                    require_once('kbgeocode_main.php');
                    // echo '<div><h3>plugin actiavated here</h3></div>';
                }
        }   

    }


    static function activation(){        
        
        // Create new DB Table 'wp_kollabo_geocodes' function
        init_db_myplugin(); 

        // Insert values into this table
        // update_db_myplugin(); 

    }


    static function deactivation(){
        // do not need to delete table 'wp_kollabo_geocodes' from DB!!
        clear_table_values_myplugin();

    }

}


if(class_exists('kollabogeocodes')){
    $kollaboGeocodes = new kollabogeocodes();
    $kollaboGeocodes->register();
}

register_activation_hook( __FILE__, array( $kollaboGeocodes, 'activation' ) );

register_deactivation_hook( __FILE__, array( $kollaboGeocodes, 'deactivation' ) );


// Initialize DB Table
function init_db_myplugin() {

    // WP Globals
    global $table_prefix, $wpdb;

    // Customer Table
    $kbgeocodesTable = $table_prefix . 'kbgeocodes';

    // Create Customer Table if not exist
    if( $wpdb->get_var( "show tables like '$kbgeocodesTable'" ) != $kbgeocodesTable ) {

        // Query - Create Table
        $sql = "CREATE TABLE `$kbgeocodesTable` (";
        $sql .= " `geocode_num` int(11) NOT NULL, ";
        $sql .= " `geocode_txt` char(100) NOT NULL, ";
        $sql .= "  PRIMARY KEY  (geocode_num) ";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        // Include Upgrade Script for DB updates
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    
        // Create Table
        dbDelta( $sql );
    }

}

    // CLear DB Table from all values
    function clear_table_values_myplugin() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kbgeocodes';
        $delete = $wpdb->query("TRUNCATE TABLE `$table_name`");
    } 