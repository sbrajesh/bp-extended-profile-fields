<?php
/**

 * Plugin Name: BP Extended profile Fields
 * Version: 1.0
 * Plugin URI: http://buddydev.com
 * Author: Brajesh Singh
 * 
 */

class BP_Extended_Profile_Fields_Helper{
    
    /**
     *
     * @var BP_Extended_Profile_Fields_Helper 
     */
    private static $instance;
    /**
     * Path to this plugin directory
     * @var string 
     */
    private $path = '';
    
    /**
     * The url to this plugin directory
     * @var string url 
     */
    private $url = '';
    
    private function __construct() {
        
        $this->path = plugin_dir_path( __FILE__ ); //with trailing slash
        $this->url  = plugin_dir_url( __FILE__ ); //with trailing slash
        
        add_action( 'plugins_loaded', array( $this, 'load' ) );
        
        add_action( 'admin_print_scripts', array( $this, 'load_admin_js' ) );
        
    }
    
    /**
     * 
     * @return BP_Extended_Profile_Fields_Helper
     */
    public static function get_instance(){
        
        if( !isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
    }
    
    public function load(){
        
        
        $files = array(
                'fields-manager.php',
                'fields/base-field.php',
                'fields/email-field.php',
                'fields/groups.php',
                'fields/single-group.php',
                'fields/countries.php',
        );
        
        foreach( $files as $file )
            require_once $this->path . $file;
        
        
    }
    
    public function load_admin_js(){
        
        wp_enqueue_script( 'bpepfjs', $this->url . '_inc/bpepf.js', array( 'jquery' ) );
    }
    
    
}
BP_Extended_Profile_Fields_Helper::get_instance();