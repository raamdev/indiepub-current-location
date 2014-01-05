<?php
//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete saved options to clean up the database
delete_option( 'ncl_text' );
delete_option( 'ncl_coords' );
delete_option( 'ncl_api_key' );
delete_option( 'ncl_api_enable' );

?>