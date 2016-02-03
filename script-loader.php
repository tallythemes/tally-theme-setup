<?php
function tallythemesetup_load_admin_script() {
	wp_enqueue_style( 'tally-theme-setup', TALLYTHEMESETUP__PLUGIN_URL . '/assets/css/admin.css');
	
	wp_enqueue_script( 'tally-theme-setup', TALLYTHEMESETUP__PLUGIN_URL.'/assets/js/bootstrapguru-import.js', array('jquery'), '', true ); 
}

add_action( 'admin_enqueue_scripts', 'tallythemesetup_load_admin_script' );