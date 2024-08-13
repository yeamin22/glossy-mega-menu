<?php
/*
Elementor Header Footer
 */

namespace GlossyMM\Glossymm_HF {
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }


    class Glossymm_HF {

    /**
	 * Instance of HFE_Admin
	 *
	 * @var HFE_Admin
	 */
	private static $_instance = null;

	/**
	 * Instance of HFE_Admin
	 *
	 * @return HFE_Admin Instance of HFE_Admin
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		add_action( 'elementor/init', __CLASS__ . '::load_admin', 0 );

		return self::$_instance;
	}

    public function __construct() {
            add_action( 'plugins_loaded', [$this, 'init_glossymm_hf'] ); 
        }

        public function init_glossymm_hf() { 
            require_once GLOSSYMM_PATH . "/includes/glossymm-hf/class-glossymm-conditions-fields.php";
            if ( file_exists( GLOSSYMM_PATH . "/includes/glossymm-hf/class-cpt.php" ) ) {
                require_once GLOSSYMM_PATH . "/includes/glossymm-hf/class-cpt.php";
                if ( class_exists( '\GlossyMM\Glossymm_HF\Cpt' ) ) {
                    Cpt::instance();
                }

            }
            
        }

    public function load_admin(){

        
    }



    
    } // End Class

    // Instantiate the main class
    new Glossymm_HF();
}
