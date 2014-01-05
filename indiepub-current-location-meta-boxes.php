<?php

/* Meta box setup function. */
function ncl_post_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'ncl_add_post_meta_boxes' );

	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'ncl_save_current_location_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function ncl_add_post_meta_boxes() {
    $post_types = get_post_types();
    foreach ( $post_types as $post_type ) {
        add_meta_box('ncl-current-location', 'Current Location', 'ncl_current_location_meta_box', $post_type, 'side', 'default');
    }
}

/* Display the post meta box. */
function ncl_current_location_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'ncl_current_location_nonce' ); ?>
	
	<?php
	if ('' == get_post_meta( $object->ID, 'ncl_current_location', true )) {
		$ncl_current_location = get_option('ncl_text');
	} else {
		$ncl_current_location = esc_attr( get_post_meta( $object->ID, 'ncl_current_location', true ) );
	}
	?>
	<p>
		<label for="ncl-current-location"><?php _e( "Add the location where this post was written.", 'example' ); ?></label>
		<br />
		<input class="widefat" type="text" name="ncl-current-location" id="ncl-current-location" value="<?php echo stripslashes($ncl_current_location); ?>" size="30" />
	</p>
<?php }

/* Save the meta box's post metadata. */
function ncl_save_current_location_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['ncl_current_location_nonce'] ) || !wp_verify_nonce( $_POST['ncl_current_location_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['ncl-current-location'] ) ? $_POST['ncl-current-location'] : '' );

	/* Get the meta key. */
	$meta_key = 'ncl_current_location';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
}


?>