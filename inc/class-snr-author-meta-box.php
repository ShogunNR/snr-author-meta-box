<?php

/**
 * SNR Author Meta Box.
 *
 * Defines the plugin name, version, the meta box functionality
 * and the JavaScript for loading the Media Uploader.
 *
 * @package    SNR_Author_Meta_Box
 * @author     ShogunNR
 */
class SNR_Author_Meta_Box {
 
        /**
         * The ID of this plugin.
         *
         * @since       1.0.0
         * @access      private
         * @var         string          $name           The ID of this plugin.
         */
        private $id;
 
        /**
         * The name of this plugin.
         *
         * @since       1.0.0
         * @access      private
         * @var         string          $name           The ID of this plugin.
         */
        private $name;

        /**
         * The current version of the plugin.
         *
         * @since       1.0.0
         * @access      private
         * @var         string          $version        The version of the plugin.
         */
        private $version;

        /**
         * The screens on which to show the meta box.
         *
         * @since       1.0.0
         * @access      public
         * @var         array           $screens         The screens on which to show the meta box.
         */
        private $screens;

        /**
         * Initializes the plugin by defining the properties.
         *
         * @since 1.0.0
         */
        public function __construct() {

                $this->id       = 'snr_author_meta_box';
                $this->name     = 'SNR Author Meta Box';
                $this->version  = '1.0.0';
                $this->screens  = apply_filters( 'SNR_Author_Meta_Box__screens', array( 'post', 'page' ) );

        }

        /**
         * Defines the hooks that will register and enqueue the JavaScript
         * and the meta box that will render the option.
         *
         * @since 1.0.0
         */
        public function run() {

                add_action( 'admin_enqueue_scripts',            array( $this, 'enqueue_scripts'                 )               );
                add_action( 'add_meta_boxes',                   array( $this, 'add_meta_box'                    )               );
                add_action( 'save_post',                        array( $this, 'save_post'                       )               );
                
                add_filter( 'get_the_author_display_name',      array( $this, 'get_the_author_display_name'     )               );
                add_filter( 'get_the_author_nickname',          array( $this, 'get_the_author_display_name'     )               );
                add_filter( 'the_author',                       array( $this, 'get_the_author_display_name'     )               );
                add_filter( 'get_the_author_description',       array( $this, 'get_the_author_description'      )               );
                add_filter( 'get_the_author_user_url',          array( $this, 'get_the_author_user_url'         )               );
                add_filter( 'get_avatar',                       array( $this, 'get_avatar'                      ), 10, 5        );

        }
        
        /**
         * Registers the JavaScript for handling the media uploader.
         *
         * @since 1.0.0
         */
        public function enqueue_scripts() {
                
                if ( in_array( get_current_screen()->id, $this->screens ) ) {

                        wp_enqueue_media();

                        wp_enqueue_script(
                                $this->id,
                                plugin_dir_url( SNRAMB_PLUGIN_FILE ) . 'assets/js/admin.js',
                                array( 'jquery' ),
                                $this->version,
                                true
                        );
                        
                }

        }

        /**
         * Renders the meta box on the post and pages.
         *
         * @since 1.0.0
         */
        public function add_meta_box() {

                foreach ( $this->screens as $screen ) {

                        add_meta_box(
                                $this->id,
                                $this->name,
                                array( $this, 'display_meta_box' ),
                                $screen,
                                'normal'
                        );

                }

        }
        
        /**
         * Renders the view that displays the contents for the meta box that for triggering
         * the meta box.
         *
         * @param    WP_Post    $post           The post object
         * @since    1.0.0
         */
        public function display_meta_box( $post ) {
                $name           = get_post_meta( $post->ID, 'snramb_display_name',      true );
                $description    = get_post_meta( $post->ID, 'snramb_description',       true );
                $url            = get_post_meta( $post->ID, 'snramb_url',               true );
                $avatar_src     = get_post_meta( $post->ID, 'snramb_avatar_src',        true );
                $avatar_title   = get_post_meta( $post->ID, 'snramb_avatar_title',      true );
                
                // Add an nonce field so we can check for it later.
                wp_nonce_field( $this->id . '_action', $this->id . '_name' );
                
                include_once( dirname( __FILE__ ) . '/views/admin.php' );
        }

        /**
         * Sanitized and saves the post featured footer image meta data specific with this post.
         *
         * @param       int     $post_id        The ID of the post with which we're currently working.
         * @since       1.0.0
         */
        public function save_post( $post_id ) {
                
                if ( ! in_array( get_current_screen()->id, $this->screens ) ) {
                        return;
                }
                
                /*
                 * We need to verify this came from the our screen and with proper authorization,
                 * because save_post can be triggered at other times.
                 */

                // Check if our nonce is set.
                if ( ! isset( $_POST[ $this->id . '_name' ] ) ) {
                        return $post_id;
                }

                // Verify that the nonce is valid.
                if ( ! wp_verify_nonce( $_POST[ $this->id . '_name' ], $this->id . '_action' ) ) {
                        return $post_id;
                }

                // If this is an autosave, our form has not been submitted,
                // so we don't want to do anything.
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                        return $post_id;
                }

                // Check the user's permissions.
                if ( 'page' == $_POST[ 'post_type' ] ) {
                        if ( ! current_user_can( 'edit_page', $post_id ) ) {
                                return $post_id;
                        }
                } else {
                        if ( ! current_user_can( 'edit_post', $post_id ) ) {
                                return $post_id;
                        }
                }

                /**
                 * OK, its safe for us to save the data now.
                 */
                                
                update_post_meta( $post_id, 'snramb_display_name',      sanitize_text_field( $_POST[ 'snramb_display_name'      ] ) );
                update_post_meta( $post_id, 'snramb_description',       sanitize_text_field( $_POST[ 'snramb_description'       ] ) );
                update_post_meta( $post_id, 'snramb_url',               sanitize_text_field( $_POST[ 'snramb_url'               ] ) );
                update_post_meta( $post_id, 'snramb_avatar_src',        sanitize_text_field( $_POST[ 'snramb_avatar_src'        ] ) );
                update_post_meta( $post_id, 'snramb_avatar_title',      sanitize_text_field( $_POST[ 'snramb_avatar_title'      ] ) );

        }

        /**
         * Change the display name of the current post's author.
         *
         * @param       string  $display_name   The author's display name.
         * @since       1.0.0
         */
        public function get_the_author_display_name( $display_name ) {
                
                $custom_display_name = get_post_meta( get_the_ID(), 'snramb_display_name', true );
                        
                return $custom_display_name ? $custom_display_name : $display_name;

        }

        /**
         * Change the biographical info of the current post's author.
         *
         * @param       string  $description    The author's biographical info.
         * @since       1.0.0
         */
        public function get_the_author_description( $description ) {
                
                $custom_description = get_post_meta( get_the_ID(), 'snramb_description', true );
                        
                return $custom_description ? $custom_description : $description;

        }

        /**
         * Change the website address of the current post's author.
         *
         * @param       string  $user_url       The author's website address.
         * @since       1.0.0
         */
        public function get_the_author_user_url( $user_url ) {
                
                $custom_user_url = get_post_meta( get_the_ID(), 'snramb_url', true );
                        
                return $custom_user_url ? $custom_user_url : $user_url;

        }

        /**
         * Change the website address of the current post's author.
         *
         * @param string            $avatar      Image tag for the user's avatar.
         * @param int|object|string $id_or_email A user ID, email address, or comment object.
         * @param int               $size        Square avatar width and height in pixels to retrieve.
         * @param string            $alt         Alternative text to use in the avatar image tag.
         *                                       Default empty.
         * @since       1.0.0
         */
        public function get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
                
                $custom_avatar_src = get_post_meta( get_the_ID(), 'snramb_avatar_src', true );
                                                
                if ( $custom_avatar_src ) {
                        $avatar = '<img alt="' . esc_attr( $alt ) . '" src="' . $custom_avatar_src . '" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
                }
                
                return $avatar;

        }
 
}