<?php   
    if(isset($_POST['location_hidden']) && $_POST['location_hidden'] == "Y") {
        //Form data sent  
        $ncl_text = $_POST['ncl_text'];  
        update_option('ncl_text', $ncl_text);  
          
        $ncl_coords = $_POST['ncl_coords'];  
        update_option('ncl_coords', $ncl_coords);

        $ncl_wiki_url = $_POST['ncl_wiki_url'];  
        update_option('ncl_wiki_url', $ncl_wiki_url);

		update_option('ncl_updated_date', time());
		$ncl_updated_date = get_option('ncl_updated_date');
		
		$ncl_api_key = get_option('ncl_api_key');
		
        ?>  
        <div class="updated"><p><strong><?php _e('Current Location Updated.' ); ?></strong></p></div> 
        <?php
	} else if (isset($_POST['api_key_hidden']) && $_POST['api_key_hidden'] == "Y"){

		// Generate a new API key
        $ncl_api_key = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,15);  
        update_option('ncl_api_key', $ncl_api_key);  

        $ncl_text = get_option('ncl_text');  
        $ncl_coords = get_option('ncl_coords');
        $ncl_wiki_url = get_option('ncl_wiki_url');
		$ncl_updated_date = get_option('ncl_updated_date');

        ?>  
        <div class="updated"><p><strong><?php _e('New API Key Generated.' ); ?></strong></p></div>  
        <?php
	} else if (isset($_POST['api_enable_hidden']) && $_POST['api_enable_hidden'] == "Y"){

        $ncl_text = get_option('ncl_text');  
        $ncl_coords = get_option('ncl_coords');
        $ncl_wiki_url = get_option('ncl_wiki_url');
		$ncl_updated_date = get_option('ncl_updated_date');
		$ncl_api_key = get_option('ncl_api_key');
		$ncl_api_enable = get_option('ncl_api_enable');
		
		// If the remote API is disabled, enable it. Otherwise disable it.
		if ($ncl_api_enable == "0") {
			update_option('ncl_api_enable', "1");
			$ncl_api_enable = get_option('ncl_api_enable');
			$ncl_success_msg = "Remote API Enabled.";
			
		} else {
			update_option('ncl_api_enable', "0");
			$ncl_api_enable = get_option('ncl_api_enable');
			$ncl_success_msg = "Remote API Disabled.";
		}

        ?>  
        <div class="updated"><p><strong><?php _e($ncl_success_msg ); ?></strong></p></div>  
    <?php
    } else {  
        //Normal page display  
        $ncl_text = get_option('ncl_text');  
        $ncl_coords = get_option('ncl_coords');
        $ncl_wiki_url = get_option('ncl_wiki_url');
		$ncl_updated_date = get_option('ncl_updated_date');

		// if ncl_api_key hasn't been generated, generate an initial key
        if(!$ncl_api_key = get_option('ncl_api_key')){
	        $ncl_api_key = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,15);  
	        update_option('ncl_api_key', $ncl_api_key);
			$ncl_api_key = get_option('ncl_api_key');
		}

		// if ncl_api_enable hasn't been set, set it to disabled by default
		if(!$ncl_api_enable = get_option('ncl_api_enable')){
	        update_option('ncl_api_enable', "0");
			$ncl_api_enable = get_option('ncl_api_enable');
		}
    }  
?>

<div class="wrap"> 
	<div style="width:600px;">
    <?php    echo "<h2>" . __( 'IndiePub Current Location', 'ncl_trdom' ) . "</h2>"; ?>
	<p><?php _e("Use the fields below to enter your current location. Both fields are optional and you can control what gets shown using the shortcode (see 'Showing your Current Location' below)." ); ?></p>  
    <form name="ncl_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"> 
	 	<input type="hidden" name="location_hidden" value="Y">  
        <p><?php _e("<strong>Current Location:</strong> " ); ?><input type="text" name="ncl_text" value="<?php echo stripslashes($ncl_text); ?>" size="20"><?php _e(" <em>(e.g. City, State, Country)</em>" ); ?></p>  
        <p><?php _e("<strong>Coordinates:</strong> " ); ?><input type="text" name="ncl_coords" value="<?php echo $ncl_coords; ?>" size="20"><?php _e(" <em>(e.g., 130.852855,-12.379895)</em>" ); ?></p>
        <p><?php _e("<strong>Last Updated:</strong> " ); ?> <?php echo date(DATE_RFC822, $ncl_updated_date); ?> <?php if (function_exists('time_since')) { ?> (<em><?php echo time_since(abs($ncl_updated_date), time()); ?> ago</em>) <?php } ?></p>
        <p><?php _e("<strong>Wiki URL:</strong> " ); ?><input type="text" name="ncl_wiki_url" value="<?php echo $ncl_wiki_url; ?>" size="50"><?php _e(" <br /><em>(e.g., http://en.wikipedia.org/wiki/Rapid_Creek,_Northern_Territory)</em>" ); ?></p>
  
        <p class="submit">  
        <input type="submit" name="Submit" value="<?php _e('Update Current Location', 'ncl_trdom' ) ?>" />  
        </p>  
    </form>
    <?php    echo "<h2>" . __( 'Showing your Current Location', 'ncl_trdom' ) . "</h2>"; ?>  
	<p><?php _e("Use shortcode <code>[ncl-current-location]</code> to display your current location in a WordPress Post/Page." ); ?></p>  
	<p><?php _e("If you want to show the coordinates, use <code>[ncl-current-location display=\"coordinates\"]</code>." ); ?></p>

    <?php    echo "<h2>" . __( 'Remote API for Integrating with External Scripts', 'ncl_trdom' ) . "</h2>"; ?>  
    <form name="ncl_api_enable_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	 	<input type="hidden" name="api_enable_hidden" value="Y">  
		<?php 	if ($ncl_api_enable == "0") { 
					$ncl_button_text = "Enable Remote API";
					$ncl_api_message_text = "Disabled";
				} else { 
					$ncl_button_text = "Disable Remote API"; 
					$ncl_api_message_text = "Enabled";
				}
		?>
        <p class="submit"><strong>Remote API is currently <span style="color: red;"><?php echo $ncl_api_message_text; ?></span></strong> <input type="submit" name="Submit" value="<?php echo $ncl_button_text; ?>" /></p>  
    </form>
<hr />
	<?php echo "<h3>" . __( 'Remote API Key', 'ncl_trdom' ) . "</h3>"; ?>
	<p><?php _e("The remote API allows you to update your current location settings from a remote script. The API key must be passed along in the query string to authenticate the remote request. " ); ?></p>  
	<p><?php _e("<strong>Note: This is <em>very basic security</em>. Simple network sniffing would reveal the key unless you're making requests over HTTPS.</strong>" ); ?></p>
    <form name="ncl_api_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&generate_api_key=true">
	 	<input type="hidden" name="api_key_hidden" value="Y">  
        <p><?php _e("API Key: " ); ?><code><?php echo $ncl_api_key; ?></code></p>  

        <p class="submit">  
        <input type="submit" name="Submit" value="<?php _e('Generate New API Key', 'ncl_trdom' ) ?>" />  
        </p>  
    </form>
<hr />
	<?php echo "<h3>" . __( 'Remote API Examples', 'ncl_trdom' ) . "</h3>"; ?>
	<p><?php _e("To update the Current Location Settings remotely, follow the examples below. Calling the URLs with the necessary query string variables will update the Current Location. Note that all spaces must be URLEncoded." ); ?></p>
	<p><?php _e("<strong>Return all Current Location Data</strong><br /> <code>". home_url() . "/?ncl_api_key=" . $ncl_api_key . "</code>" ); ?></p>
	<p><?php _e("<strong>Update Current Location to</strong> <em>Nightcliff, NT, Australia</em><br />  <code>". home_url() . "/?ncl_api_key=" . $ncl_api_key . "&location=Nightcliff,%20NT,%20Australia</code>" ); ?></p>
	<p><?php _e("<strong>Update Coordinates to</strong> <em>130.852855,-12.379895</em><br />  <code>". home_url() . "/?ncl_api_key=" . $ncl_api_key . "&coordinates=130.852855,-12.379895</code>" ); ?></p>		
	<p><?php _e("<strong>Update Current Location, Coordinates, and Date (unixtime)</strong><br />  <code>". home_url() . "/?ncl_api_key=" . $ncl_api_key . "&location=Nightcliff,%20NT,%20Australia&coordinates=130.852855,-12.379895&updated=1342757523</code>" ); ?></p>
	</div>
</div>