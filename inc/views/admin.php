<p>
        <label>
                <strong>Display Name:</strong>
                <input type="text" name="snramb_display_name" class="widefat" value="<?php echo esc_attr( $name ); ?>">
        </label>
</p>

<p>
        <label>
                <strong>Biographical info:</strong>
                <textarea name="snramb_description" class="widefat"><?php echo $description; ?></textarea>
        </label>
</p>

<p>
        <label>
                <strong>Website:</strong>
                <input type="text" name="snramb_url" class="widefat" value="<?php echo esc_attr( $url ); ?>">
        </label>
</p>

<p>
        <strong>Avatar:</strong>
        
        <br>
        
        <span id="snramb-avatar-container" class="hidden"></span><!-- #snramb-avatar-container -->

        <br>
        
        <button id="snramb-set-avatar" class="button button-primary hide-if-no-js">Set custom avatar</button>
        <button id="snramb-remove-avatar" class="button button-secondary hide-if-no-js<?php if ( ! $avatar_src ) echo ' hidden' ?>">Remove custom avatar</button>

        <input type="hidden" id="snramb-avatar-src" name="snramb_avatar_src" value="<?php echo esc_attr( $avatar_src ); ?>">
        <input type="hidden" id="snramb-avatar-title" name="snramb_avatar_title" value="<?php echo esc_attr( $avatar_title ); ?>">

        <script type="text/html" id="snramb-avatar-template"><img src="{{src}}" alt="{{title}}" style="max-width: 100%;"></script>
</p>