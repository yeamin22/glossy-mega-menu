<?php
namespace GlossyMM\Glossymm_HF {
    // What are you trying to do?
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Cpt {

        /**
         * Instance of Cpt
         *
         * @var Cpt
         */
        private static $_instance = null;

        /**
         * Instance of Cpt
         *
         * @return Cpt Instance of Cpt
         */
        public static function instance() {
            if ( !isset( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct() {
            add_action( "init", [$this, "post_type"] );
            if ( is_admin() && current_user_can( 'manage_options' ) ) {
                add_action( 'admin_menu', [$this, 'register_admin_menu'], 50 );
                add_action( "admin_enqueue_scripts", [$this, "glossymm_hf_assets"] );
            }            
            add_action( 'add_meta_boxes', [$this, 'glossymm_hf_register_metabox'] );
        }

        public function glossymm_hf_assets( $hook ) {
            $screen = get_current_screen();
            if ( "glossymm_hf" == $screen->post_type && "post" == $screen->base ) {
                wp_enqueue_style( "glossymm-hf", GLOSSYMM_ADMIN_ASSETS . '/css/glossymm-hf.css' );
                wp_enqueue_script( "glossymm-hf", GLOSSYMM_ADMIN_ASSETS . '/js/glossymm-hf.js', ['jquery'], time(), true );
            }
        }

        public function post_type() {

            if ( !current_user_can( 'manage_options' ) ) {
                return;
            }
            $labels = [
                'name'               => esc_html__( 'Header & Footer Builder', 'glossy-mega-menu' ),
                'singular_name'      => esc_html__( 'Header & Footer Builder', 'glossy-mega-menu' ),
                'menu_name'          => esc_html__( 'Header & Footer Builder', 'glossy-mega-menu' ),
                'name_admin_bar'     => esc_html__( 'Header & Footer Builder', 'glossy-mega-menu' ),
                'add_new'            => esc_html__( 'Add New', 'glossy-mega-menu' ),
                'add_new_item'       => esc_html__( 'Add New Header or Footer', 'glossy-mega-menu' ),
                'new_item'           => esc_html__( 'New Template', 'glossy-mega-menu' ),
                'edit_item'          => esc_html__( 'Edit Template', 'glossy-mega-menu' ),
                'view_item'          => esc_html__( 'View Template', 'glossy-mega-menu' ),
                'all_items'          => esc_html__( 'All Templates', 'glossy-mega-menu' ),
                'search_items'       => esc_html__( 'Search Templates', 'glossy-mega-menu' ),
                'parent_item_colon'  => esc_html__( 'Parent Templates:', 'glossy-mega-menu' ),
                'not_found'          => esc_html__( 'No Templates found.', 'glossy-mega-menu' ),
                'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', 'glossy-mega-menu' ),
            ];

            $args = [
                'labels'              => $labels,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'exclude_from_search' => true,
                'capability_type'     => 'post',
                'hierarchical'        => false,
                'menu_icon'           => 'dashicons-editor-kitchensink',
                'supports'            => ['title', 'thumbnail', 'elementor'],
            ];
            register_post_type( 'glossymm_hf', $args );
        }

        /**
         * Register the admin menu for Elementor Header & Footer Builder.
         *
         * @since  1.0.0
         * @since  1.0.1
         *         Moved the menu under Appearance -> Elementor Header & Footer Builder
         */
        public function register_admin_menu() {
            add_submenu_page(
                'themes.php',
                __( 'Header & Footer Builder', 'glossy-mega-menu' ),
                __( 'Header & Footer Builder', 'glossy-mega-menu' ),
                'edit_pages',
                'edit.php?post_type=glossymm_hf'
            );
        }

        /**
         * Register meta box(es).
         */
        public function glossymm_hf_register_metabox() {
            add_meta_box(
                'glossymm-hf-meta-box',
                __( 'Header & Footer Builder Options', 'glossy-mega-menu' ),
                [
                    $this,
                    'glossymm_hf_metabox_render',
                ],
                'glossymm_hf',
                'normal',
                'high'
            );
        }

        /**
         * Render Meta field.
         *
         * @param  POST $post Currennt post object which is being displayed.
         */
        public function glossymm_hf_metabox_render( $post ) {
            $values = get_post_custom( $post->ID );
            $template_type = isset( $values['glossymm_hf_template_type'] ) ? esc_attr( sanitize_text_field( $values['glossymm_hf_template_type'][0] ) ) : '';
            $display_on_canvas = isset( $values['display-on-canvas-template'] ) ? true : false;

            // We'll use this nonce field later on when saving.
            wp_nonce_field( 'glossymm_hf_meta_nounce', 'glossymm_hf_meta_nounce' );
            ?>

           <div class="glossymm_hf_post_options">

                <div class="glossymm-template-select">
                    <?php
                        $template_type = get_post_meta( get_the_id(), 'glossymm_template_type', true );
                        Glossymm_Conditions_Fields::settings_select_field(
                            'glossymm-template-select',
                            [
                                'title' => __( 'Type of Template', 'glossy-mega-menu' ),
                                'type'  => 'template_type',
                            ],
                            $template_type
                        );

                        Glossymm_Conditions_Fields::settings_select_field(
                            'glossymm-target-location-select',
                            [
                                'title' => __( 'Display On', 'glossy-mega-menu' ),
                                'type'  => 'target_location',
                            ],
                            $template_type
                        );

                        Glossymm_Conditions_Fields::settings_select_field(
                            'glossymm-target-user-select',
                            [
                                'title' => __( 'User Roles', 'glossy-mega-menu' ),
                                'type'  => 'target_user',
                            ],
                            $template_type
                        );
                        ?>
                </div>
           </div>
		<?php
    }

        public function flush_rewrites() {
            $this->post_type();
            flush_rewrite_rules();
        }

    } // class end
}