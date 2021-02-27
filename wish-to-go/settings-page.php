<?php
add_action( 'admin_menu', 'wtg_add_admin_menu' );
add_action( 'admin_init', 'wtg_settings_init' );


function wtg_add_admin_menu(  ) { 

	add_options_page( 'Wish To Go', 'Wish To Go', 'manage_options', 'wish-to-go', 'wtg_options_page' );

}


function wtg_settings_init(  ) { 

	register_setting( 'pluginPage', 'wtg_settings' );

	add_settings_section(
		'wtg_pluginPage_section', 
		__( 'Settings', 'Wish To Go' ), 
		'wtg_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'wtg_setting_hide_wish_counter', 
		__( 'Hide Wish Counter', 'Wish To Go' ), 
		'wtg_setting_hide_wish_counter_render', 
		'pluginPage', 
		'wtg_pluginPage_section' 
	);

	add_settings_field( 
		'wtg_setting_locale', 
		__( 'Locale', 'Wish To Go' ), 
		'wtg_setting_locale_render', 
		'pluginPage', 
		'wtg_pluginPage_section' 
	);

	add_settings_field( 
		'wtg_setting_sign_up_redirect', 
		__( 'Sign up redirect URL', 'Wish To Go' ), 
		'wtg_setting_sign_up_redirect_render', 
		'pluginPage', 
		'wtg_pluginPage_section' 
	);

	// add_settings_field( 
	// 	'wtg_setting_api_key', 
	// 	__( 'API Key', 'Wish To Go' ), 
	// 	'wtg_setting_api_key_render', 
	// 	'pluginPage', 
	// 	'wtg_pluginPage_section' 
	// );


}


function wtg_setting_hide_wish_counter_render(  ) { 

	$options = get_option( 'wtg_settings' );
	?>
	<input type='checkbox' name='wtg_settings[wtg_setting_hide_wish_counter]' <?php checked( $options['wtg_setting_hide_wish_counter'], 1 ); ?> value='1'>
	<?php

}


function wtg_setting_locale_render(  ) { 

	$options = get_option( 'wtg_settings' );
	?>
	<input type='text' name='wtg_settings[wtg_setting_locale]' value='<?php echo $options['wtg_setting_locale']; ?>'>
	<?php

}

function wtg_setting_sign_up_redirect_render(  ) { 

	$options = get_option( 'wtg_settings' );
	?>
	<input type='text' name='wtg_settings[wtg_setting_sign_up_redirect]' value='<?php echo $options['wtg_setting_sign_up_redirect']; ?>'>
	<?php

}

function wtg_setting_api_key_render(  ) { 

	$options = get_option( 'wtg_settings' );
	?>
	<input type='text' name='wtg_settings[wtg_setting_api_key]' value='<?php echo $options['wtg_setting_api_key']; ?>'>
	<?php

}


function wtg_settings_section_callback(  ) { 

	// echo __( 'This section description', 'Wish To Go' );

}


function wtg_options_page(  ) { 

		?>
		<form action='options.php' method='post'>

			<h2>Wish To Go</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<?php

}
