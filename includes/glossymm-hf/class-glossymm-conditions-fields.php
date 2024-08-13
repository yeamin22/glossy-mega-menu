<?php
namespace GlossyMM\Glossymm_HF {
    // What are you trying to do?
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Glossymm_Conditions_Fields {

        /**
         * User Selection Option
         *
         * @since  1.0.0
         *
         * @var $user_selection
         */
        private static $user_selection;

        /**
         * Location Selection Option
         *
         * @since  1.0.0
         *
         * @var $location_selection
         */
        private static $location_selection;

        public static function settings_select_field( $input_name, $settings, $value ) {
            $type = isset( $settings['type'] ) ? $settings['type'] : '';
            $title = isset( $settings['title'] ) ? $settings['title'] : '';

            $output = '';

            $output .= '<div class="glossymm-select-field" id="'.$input_name.'">';
            $output .= sprintf( '<label for="#%s">%s</label>', esc_attr( $input_name ), $title );

            $output .= '<select name="' . esc_attr( $input_name ) . '" class="template-select form-control">';
            $output .= '<option value="">' . __( 'Select', 'header-footer-elementor' ) . '</option>';
            if ( "template_type" == $type ) {
                $template_options = [
                    'header'   => __( "Header", "glossy-mega-menu" ),
                    'footer'   => __( "Footer", "glossy-mega-menu" ),
                    'template' => __( "Template", "glossy-mega-menu" ),
                ];

                foreach ( $template_options as $key => $option ) {
                    $output .= sprintf( '<option value="%s" >%s</option>', $key, $option );
                }
            }elseif("target_location" == $type){
                if ( isset( self::$location_selection ) || empty( self::$location_selection ) ) {
                    self::$location_selection = self::get_location_selections();
                }
                $selection_options = self::$location_selection;

                foreach ( $selection_options as $group => $group_data ) {
                    $output .= '<optgroup label="' . $group_data['label'] . '">';
                    foreach ( $group_data['value'] as $opt_key => $opt_value ) {
                        $output .= '<option value="' . $opt_key . '">' . $opt_value . '</option>';
                    }
                    $output .= '</optgroup>';
                }

            }elseif("target_user" == $type){
                if ( !isset( self::$user_selection ) || empty( self::$user_selection ) ) {
                    self::$user_selection = self::get_user_selections();
                }
                $selection_options = self::$user_selection;
    
                foreach ( $selection_options as $group => $group_data ) {
                    $output .= '<optgroup label="' . $group_data['label'] . '">';
                    foreach ( $group_data['value'] as $opt_key => $opt_value ) {
                        $output .= '<option value="' . $opt_key . '" ' . selected( $value, $opt_key, false ) . '>' . $opt_value . '</option>';
                    }
                    $output .= '</optgroup>';
                }
            }


            $output .= '</select>';
            $output .= '</div>';

            echo $output;

        }


        /**
         * Get location selection options.
         *
         * @return array
         */
        public static function get_location_selections() {
            $args = [
                'public'   => true,
                '_builtin' => true,
            ];

            $post_types = get_post_types( $args, 'objects' );
            unset( $post_types['attachment'] );

            $args['_builtin'] = false;
            $custom_post_type = get_post_types( $args, 'objects' );

            $post_types = apply_filters( 'astra_location_rule_post_types', array_merge( $post_types, $custom_post_type ) );

            $special_pages = [
                'special-404'    => __( '404 Page', 'header-footer-elementor' ),
                'special-search' => __( 'Search Page', 'header-footer-elementor' ),
                'special-blog'   => __( 'Blog / Posts Page', 'header-footer-elementor' ),
                'special-front'  => __( 'Front Page', 'header-footer-elementor' ),
                'special-date'   => __( 'Date Archive', 'header-footer-elementor' ),
                'special-author' => __( 'Author Archive', 'header-footer-elementor' ),
            ];

            if ( class_exists( 'WooCommerce' ) ) {
                $special_pages['special-woo-shop'] = __( 'WooCommerce Shop Page', 'header-footer-elementor' );
            }

            $selection_options = [
                'basic'         => [
                    'label' => __( 'Basic', 'header-footer-elementor' ),
                    'value' => [
                        'basic-global'    => __( 'Entire Website', 'header-footer-elementor' ),
                        'basic-singulars' => __( 'All Singulars', 'header-footer-elementor' ),
                        'basic-archives'  => __( 'All Archives', 'header-footer-elementor' ),
                    ],
                ],

                'special-pages' => [
                    'label' => __( 'Special Pages', 'header-footer-elementor' ),
                    'value' => $special_pages,
                ],
            ];

            $args = [
                'public' => true,
            ];

            $taxonomies = get_taxonomies( $args, 'objects' );

            if ( !empty( $taxonomies ) ) {
                foreach ( $taxonomies as $taxonomy ) {

                    // skip post format taxonomy.
                    if ( 'post_format' == $taxonomy->name ) {
                        continue;
                    }

                    foreach ( $post_types as $post_type ) {
                        $post_opt = self::get_post_target_rule_options( $post_type, $taxonomy );

                        if ( isset( $selection_options[$post_opt['post_key']] ) ) {
                            if ( !empty( $post_opt['value'] ) && is_array( $post_opt['value'] ) ) {
                                foreach ( $post_opt['value'] as $key => $value ) {
                                    if ( !in_array( $value, $selection_options[$post_opt['post_key']]['value'] ) ) {
                                        $selection_options[$post_opt['post_key']]['value'][$key] = $value;
                                    }
                                }
                            }
                        } else {
                            $selection_options[$post_opt['post_key']] = [
                                'label' => $post_opt['label'],
                                'value' => $post_opt['value'],
                            ];
                        }
                    }
                }
            }

            $selection_options['specific-target'] = [
                'label' => __( 'Specific Target', 'header-footer-elementor' ),
                'value' => [
                    'specifics' => __( 'Specific Pages / Posts / Taxonomies, etc.', 'header-footer-elementor' ),
                ],
            ];

            /**
             * Filter options displayed in the display conditions select field of Display conditions.
             *
             * @since 1.5.0
             */
            return apply_filters( 'glossymm_display_on_list', $selection_options );
        }

        /**
         * Get target rules for generating the markup for rule selector.
         *
         * @since  1.0.0
         *
         * @param object $post_type post type parameter.
         * @param object $taxonomy taxonomy for creating the target rule markup.
         */
        public static function get_post_target_rule_options( $post_type, $taxonomy ) {
            $post_key = str_replace( ' ', '-', strtolower( $post_type->label ) );
            $post_label = ucwords( $post_type->label );
            $post_name = $post_type->name;
            $post_option = [];

            /* translators: %s post label */
            $all_posts = sprintf( __( 'All %s', 'header-footer-elementor' ), $post_label );
            $post_option[$post_name . '|all'] = $all_posts;

            if ( 'pages' != $post_key ) {
                /* translators: %s post label */
                $all_archive = sprintf( __( 'All %s Archive', 'header-footer-elementor' ), $post_label );
                $post_option[$post_name . '|all|archive'] = $all_archive;
            }

            if ( in_array( $post_type->name, $taxonomy->object_type ) ) {
                $tax_label = ucwords( $taxonomy->label );
                $tax_name = $taxonomy->name;

                /* translators: %s taxonomy label */
                $tax_archive = sprintf( __( 'All %s Archive', 'header-footer-elementor' ), $tax_label );

                $post_option[$post_name . '|all|taxarchive|' . $tax_name] = $tax_archive;
            }

            $post_output['post_key'] = $post_key;
            $post_output['label'] = $post_label;
            $post_output['value'] = $post_option;

            return $post_output;
        }
        

        /**
         * Get user selection options.
         *
         * @return array
         */
        public static function get_user_selections() {
            $selection_options = [
                'basic'    => [
                    'label' => __( 'Basic', 'header-footer-elementor' ),
                    'value' => [
                        'all'        => __( 'All', 'header-footer-elementor' ),
                        'logged-in'  => __( 'Logged In', 'header-footer-elementor' ),
                        'logged-out' => __( 'Logged Out', 'header-footer-elementor' ),
                    ],
                ],

                'advanced' => [
                    'label' => __( 'Advanced', 'header-footer-elementor' ),
                    'value' => [],
                ],
            ];

            /* User roles */
            $roles = get_editable_roles();

            foreach ( $roles as $slug => $data ) {
                $selection_options['advanced']['value'][$slug] = $data['name'];
            }

            /**
             * Filter options displayed in the user select field of Display conditions.
             *
             * @since 1.5.0
             */
            return apply_filters( 'astra_user_roles_list', $selection_options );
        }

    } // Class End
}