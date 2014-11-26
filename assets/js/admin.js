/**
 * Self-invoking Function.
 * 
 * @param       object  $       A reference to the jQuery object.
 * @since       1.0.0
 */
+function( $ ) { 'use strict';
        
        var SNRAMB = function() {
                this.avatar_template    = $( '#snramb-avatar-template' ).html();
                this.$avatar_src        = $( '#snramb-avatar-src' );
                this.$avatar_title      = $( '#snramb-avatar-title' );
                this.$avatar_container  = $( '#snramb-avatar-container' );
                this.$set_avatar        = $( '#snramb-set-avatar' );
                this.$remove_avatar     = $( '#snramb-remove-avatar' );
        };
        
        
        /**
         * Initialize.
         * 
         * Begin execution of the plugin.
         * 
         * @since       1.0.0
         */
        SNRAMB.prototype.init = function() {

                this.render_image();
                this.listen();

        };
                
        /**
         * Checks to see if the input field for the thumbnail source has a value.
         * If so, then show the image and the 'remove' button. 
         * Otherwise, the set button is rendered.
         *
         * @since       1.0.0
         */
        SNRAMB.prototype.render_image = function() {
                
                if ( '' !== $.trim ( this.$avatar_src.val() ) ) {
                        
                        // Set the properties of the image and display it.
                        this.$avatar_container.html(
                                this.avatar_template
                                        .replace( '{{src}}', this.$avatar_src.val() )
                                        .replace( '{{title}}', this.$avatar_title.val() )
                        );

                        this.$avatar_container.show();
                        this.$set_avatar.hide();
                        this.$remove_avatar.show();

                }
                
        };
        
        /**
         * Listen to click event on 'set' and 'remove' button.
         * 
         * @since       1.0.0
         */
        SNRAMB.prototype.listen = function() {
                
                var self = this;
                
                this.$set_avatar.on( 'click.snramb', function( event ) {

                        // Stop the button's default behavior.
                        event.preventDefault();

                        // Display the media uploader.
                        self.render_media_uploader();

                } );

                this.$remove_avatar.on( 'click.snramb', function( event ) {

                        // Stop the button's default behavior.
                        event.preventDefault();

                        // Remove the image, toggle the buttons.
                        self.reset_form();

                } );
                
        };

        /**
         * Callback function for the 'click' event of the 'set' button.
         *
         * Displays the media uploader for selecting an image.
         *
         * @since       1.0.0
         */
        SNRAMB.prototype.render_media_uploader = function() {

                var     self = this,
                        file_frame;

                /**
                 * If an instance of file_frame already exists, then we can open it
                 * rather than creating a new instance.
                 */
                if ( undefined !== file_frame ) {

                        file_frame.open();
                        return;

                }

                /**
                 * If we're this far, then an instance does not exist, so we need to
                 * create our own.
                 *
                 * Here, use the wp.media library to define the settings of the Media
                 * Uploader. We're opting to use the 'post' frame which is a template
                 * defined in WordPress core and are initializing the file frame
                 * with the 'insert' state.
                 *
                 * We're also not allowing the user to select more than one image.
                 */
                file_frame = wp.media.frames.file_frame = wp.media( {
                        frame           : 'post',
                        state           : 'insert',
                        multiple        : false,
                        library         : {
                                type: 'image'
                        }
                } );

                /**
                 * Setup an event handler for what to do when an image has been
                 * selected.
                 *
                 * Since we're using the 'view' state when initializing
                 * the file_frame, we need to make sure that the handler is attached
                 * to the insert event.
                 */
                file_frame.on( 'insert', function() {

                        // Retrieve the image's data.
                        var     data    = file_frame.state().get( 'selection' ).first().toJSON(),
                                size    = file_frame.$el.find( '.size' ).val();
                        
                        // Make sure that we have the URL of an image to display.
                        if ( 0 > $.trim( data.url.length ) ) {
                                return;
                        }

                        // Store the image's information into the meta data fields.
                        self.$avatar_src.val( data.sizes[ size ].url );
                        self.$avatar_title.val( data.title );
                        
                        // Render the image.
                        self.render_image();

                } );

                // Now display the actual file_frame.
                file_frame.open();

        };

        /**
         * Callback function for the 'click' event of the 'remove' button.
         *
         * Resets the meta box by hiding the image and by hiding the 'remove' button.
         *
         * @since       1.0.0
         */
        SNRAMB.prototype.reset_form = function() {

                // Hide the image.
                this.$avatar_container.slideUp();

                // Display the 'set' button for selecting an image.
                this.$set_avatar.show();

                // Hide the 'remove' button.
                this.$remove_avatar.hide();

                // We reset the meta data input fields.
                this.$avatar_src.val( '' );
                this.$avatar_title.val( '' );

        };      
        
        /**
         * Exe
         * @type admin_L7.SNRAMBcute.
         */
        var snramb = new SNRAMB();
        snramb.init();
        
} ( jQuery );