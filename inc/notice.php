<?php
add_action('admin_notices', 'tallythemesetup_admin_notice');
function tallythemesetup_admin_notice() {
	global $current_user;
	$user_id = $current_user->ID;
	$theme = wp_get_theme();
	$theme_demo_url = (defined('TALLYTHEME_DEMO_URL') ? TALLYTHEME_DEMO_URL : '');
	
	$plugins_lists = apply_filters('tallythemesetup_plugin_list', '');
	
	if(file_exists(tallythemesetup_demo_files_url('plugin-list-config.php'))){
		$plugins_lists =  include(tallythemesetup_demo_files_url('plugin-list-config.php'));	
	}
	
	$installed_plugin_count = 0;
	if(is_array($plugins_lists)){
		foreach($plugins_lists as $plugins_list){
			if(!is_plugin_active( $plugins_list )){
				$installed_plugin_count++;
			}
		}
	}
	
	$all_data_imported = false;
	if((get_option(TALLYTHEMESETUP_IS_XML) == 'yes') 
		&& (get_option(TALLYTHEMESETUP_IS_WIDGET) == 'yes') 
		&& (get_option(TALLYTHEMESETUP_IS_MENU) == 'yes') 
		&& (get_option(TALLYTHEMESETUP_IS_HOME) == 'yes')
		&& (get_option(TALLYTHEMESETUP_IS_BLOG) == 'yes')
		&& (get_option(TALLYTHEMESETUP_IS_BUILDER) == 'yes')
		&& (get_option(TALLYTHEMESETUP_IS_REVOLUTION) == 'yes')){
			$all_data_imported = true;
	}
			
	$user_ignored_notice = false;
	if( get_user_meta($user_id, TALLYTHEMESETUP_IGNOR_NOTICE, 'yes') == 'yes' ) {
		$user_ignored_notice = true;
	}	
	
	$is_current_page_impoter_page = false;
	if(isset($_GET['page'])){
		if($_GET['page'] == 'tallythemesetup-demo-importer'){
			$is_current_page_impoter_page = true;
		}
	}
	
	$is_current_page_tgmpa_page = false;
	if(isset($_GET['page'])){
		if($_GET['page'] == 'tgmpa-install-plugins'){
			$is_current_page_tgmpa_page = true;
		}
	}
	
	if ($user_ignored_notice == false) {
		if($all_data_imported == false){
		?>
		<div class="tallythemesetup_notice">
			<h2><?php _e( 'Thanks for installing <strong>'.$theme->get( 'Name' ).'</strong> Theme', 'tally-theme-setup' ); ?></h2>
            <?php if($installed_plugin_count == 0): ?>
                <?php if($is_current_page_impoter_page == true): ?>
                	<p>Now you are in the Sample Data Impoter page. Please click on <strong>Import Sample Data</strong> button to make your site look like the theme demo.</p>
                <?php else: ?>
                	<p>You are away of one simple step to make your site look like the Theme 
                    <a href="<?php echo $theme_demo_url; ?>" target="_blank">Demo</a> Please click on the button below and it will take you to the Demo Impoter page</p>
                    <a class="button button-primary button-hero" href="<?php echo admin_url('themes.php?page=tallythemesetup-demo-importer'); ?>">Take me to the Impoter Page</a> 
                <?php endif; ?>
            <?php else: ?>  
            	<p>If you want to make the site look like the <a href="<?php echo $theme_demo_url; ?>" target="_blank">Demo</a> of the theme please follow the simple 2 steps below.</p>
                <ol>
                	<li>Install Recommended Plugins</li>
                    <li>Import Sample Data</li>
                </ol>
                <?php if($is_current_page_tgmpa_page == true): ?>
                	<p>Below are the listed Required Plugins that need to install. Please install all plugins.</p>
                <?php else: ?>
                	<a class="button button-primary button-hero" href="<?php echo admin_url('themes.php?page=tgmpa-install-plugins'); ?>">Install Recommended Plugins</a> 
                <?php endif; ?>
            <?php endif; ?>   
			<a class="n-dismiss" href="<?php echo admin_url('themes.php?page=tallythemesetup-demo-importer&amp;tallythemesetup_ignore_notice=yes'); ?>">Dismiss</a>
		</div>
		<?php
		}
	}
}

add_action('admin_init', 'tallythemesetup_admin_notice_dismiss');
function tallythemesetup_admin_notice_dismiss() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	
	if (isset($_GET['tallythemesetup_ignore_notice'])){
		
		if('yes' == $_GET['tallythemesetup_ignore_notice']){
			add_user_meta($user_id, TALLYTHEMESETUP_IGNOR_NOTICE, 'yes');
			update_user_meta($user_id, TALLYTHEMESETUP_IGNOR_NOTICE, 'yes');
		}elseif('no' == $_GET['tallythemesetup_ignore_notice']){
			update_user_meta($user_id, TALLYTHEMESETUP_IGNOR_NOTICE, 'no');
		}
		
    }
}