<?php
/**
 * @package Tally Theme Setup
 */
/*
Plugin Name: Tally Theme Setup
Plugin URI: http://tallythemes.com/
Description: Import demo content for Tally Themes
Version: 1.0
Author: TallyThemes
Author URI: http://tallythemes.com/
License: GPLv2 or later
Text Domain: tally-theme-setup
Prefix: tallythemesetup_
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA..
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'TALLYTHEMESETUP__VERSION', '1.0' );
define( 'TALLYTHEMESETUP__MINIMUM_WP_VERSION', '4.0' );
define( 'TALLYTHEMESETUP__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TALLYTHEMESETUP__PLUGIN_DRI', plugin_dir_path( __FILE__ ) );
define( 'TALLYTHEMESETUP__DEBUG', true );

require_once('script-loader.php');
require_once('notice.php');


add_action( 'wp_ajax_tallythemesetup_demo_import', 'tallythemesetup_demo_import' );
function tallythemesetup_demo_import(){

 	/*
		1. XML importer
	------------------------------------------------------------------*/
	if($_REQUEST['target'] == 'xml_import'):
		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);
	
		// Load Importer API
		require_once ('inc/wordpress-importer.php');	
	
		if ( class_exists( 'WP_Importer' ) ){ 
			
			if(file_exists(get_stylesheet_directory() ."/inc/demo/content.xml")){
				$import_filepath = get_stylesheet_directory() ."/inc/demo/content.xml";
			}else{
				$import_filepath = get_template_directory() ."/inc/demo/content.xml";
			}
			
			if(file_exists($import_filepath)){
				include_once('bootstrapguru-import.php');
	
				$wp_import = new tallythemesetup_import();
				$wp_import->fetch_attachments = false;
				
				set_time_limit(0);
				ob_start();
				$wp_import->import($import_filepath);
				$log = ob_get_contents();
						ob_end_clean();
		
				if($wp_import->check()){
					echo 'Sample contents are imported.';
					update_option('tallythemesetup_is_xml', 'yes');
				}
			}else{
				echo 'No XML file found in the theme.';	
				echo $import_filepath;
			}
	
		}else{
			echo 'Please install "wordpress-importer" plugin';	
		}
	endif;
	
	
	/*
		2. Widget importer
	------------------------------------------------------------------*/
	if($_REQUEST['target'] == 'widget_import'):
		if ( !function_exists( 'tallythemesetup_import_widget_data' ) ){ 
			require_once ('inc/widgets-importer.php');	
		}
		
		if(function_exists( 'tallythemesetup_import_widget_data' )){
			if(file_exists(get_stylesheet_directory() ."/inc/demo/widgets.wie")){
				$wie_filepath = get_stylesheet_directory() ."/inc/demo/widgets.wie";
			}else{
				$wie_filepath = get_template_directory() ."/inc/demo/widgets.wie";
			}
			
			if(file_exists($wie_filepath)){
				if(tallythemesetup_process_import_widget_file( $wie_filepath )){
					echo 'Sample Widgets are imported.';
					update_option('tallythemesetup_is_widget', 'yes');
				}
			}else{
				echo 'No widgets.wie file found in the theme';	
			}
		}
	endif;
	
	
	/*
		3. Setup Home page as front page
	------------------------------------------------------------------*/
	if($_REQUEST['target'] == 'setup_home'):
		$home_page_data = get_page_by_title( apply_filters('tallythemesetup_home_title', 'Home') );
		$home_blog_data = get_page_by_title( apply_filters('tallythemesetup_blog_title', 'Blog') );
		$text_content = '';
		if($home_page_data){
			if(update_option( 'page_on_front', $home_page_data->ID )){
				update_option( 'show_on_front', 'page' );
				$text_content .= 'Set home page as Front page. <br>';
				update_option('tallythemesetup_is_home', 'yes');
				
			}
		}
		if($home_blog_data){
			if(update_option( 'page_for_posts', $home_blog_data->ID )){
				$text_content .=  '<br>Set Blog page as Post page.';
				update_option('tallythemesetup_is_blog', 'yes');
			}
		}
		echo $text_content;
	endif;
	
	
	/*
		4. Setup the menu
	------------------------------------------------------------------*/
	if($_REQUEST['target'] == 'setup_menu'):
		$menu_term_id = '';
		$get_all_menu = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
		if(!empty($get_all_menu) && ! is_wp_error( $get_all_menu )){
			foreach($get_all_menu as $the_menu){
				if($the_menu->slug == apply_filters('tallythemesetup_menu_slug', 'primary')){
					$menu_term_id = $the_menu->term_id;
				}
			}
		}
		$locations = get_theme_mod('nav_menu_locations');
		$locations['primary'] = $menu_term_id;  //$foo is term_id of menu
		set_theme_mod('nav_menu_locations', $locations);
		if( $locations['primary'] == $menu_term_id ){
			echo 'Set primary menu as site menu.';
			update_option('tallythemesetup_is_menu', 'yes');
		}
	endif;
	
	/*
		5. Import Builder pages
	------------------------------------------------------------------*/
	if($_REQUEST['target'] == 'builder_import'):
		if(function_exists('tallybuilder_import_page_from_array')){
			$pages_list = apply_filters('tallybuilder_prebuild_pages', NULL);
			if(is_array($pages_list)){
				foreach($pages_list as $page){
					if(file_exists($page['file'])){
						include($page['file']);
						tallybuilder_import_page_from_array($page_data);
					}
				}
			}
			update_option('tallythemesetup_is_builder', 'yes');
			echo 'Builder content imported';
		}else{
			echo '<strong>Could not import builder content.</strong> Please Install the Builder Plugin';	
		}
	endif;
	
	if($_REQUEST['target'] == 'update_option'):
		echo '<p style="font-size:18px; color:#0A9900;">All Done</p>';
	endif;
	
   
   die(); // this is required to return a proper result
}


add_action('admin_menu', 'tallythemesetup_admin_page');
function tallythemesetup_admin_page() {
	add_theme_page('Sample Data', 'Sample Data', 'manage_options', 'tallythemesetup-demo-importer', 'tallythemesetup_importer_admin_page_html');
}
function tallythemesetup_importer_admin_page_html(){
	?>
    <div class="wrap tallythemesetup_page">
    	<h1>Import Sample Data</h1>
		<?php if((get_option('tallythemesetup_is_xml') == 'yes') 
		&& (get_option('tallythemesetup_is_widget') == 'yes') 
		&& (get_option('tallythemesetup_is_menu') == 'yes') 
		&& (get_option('tallythemesetup_is_home') == 'yes')
		&& (get_option('tallythemesetup_is_blog') == 'yes')
		&& (get_option('tallythemesetup_is_builder') == 'yes')): ?>
        	
        	<strong style="color:#F00; font-size:16px; line-height:1.5;">Looks like you already import the sample data. So you don't need to do it again. If you import again duplicate content will be generated</strong>
        <?php endif; ?>
        <p style="font-weight:bold; color:#000; font-size:14px; line-height:1.5;">Sample data is not recommended for live site. It is recommended on a fresh wordpress installation. So if your current wordpress installation already have content( Images, Page's, Posts, etc. ) you should not import sample data. </p>
		<div class="tallythemesetup_import_message" style="margin-bottom:20px;"></div>
		<div class="tallythemesetup_import_message1" style="margin-bottom:20px; display:none;">
        	<img src="<?php echo TALLYTHEMESETUP__PLUGIN_URL; ?>assets/images/loader.gif" /> Importing Sample Data
        </div>
        <div class="tallythemesetup_import_message2" style="margin-bottom:20px; display:none;">
        	<img src="<?php echo TALLYTHEMESETUP__PLUGIN_URL; ?>assets/images/loader.gif" /> Importing Widgets
        </div>
        <div class="tallythemesetup_import_message3" style="margin-bottom:20px; display:none;">
        	<img src="<?php echo TALLYTHEMESETUP__PLUGIN_URL; ?>assets/images/loader.gif" />Setting Up Home Page
        </div>
        <div class="tallythemesetup_import_message4" style="margin-bottom:40px; display:none;">
        	<img src="<?php echo TALLYTHEMESETUP__PLUGIN_URL; ?>assets/images/loader.gif" />Setting Up Site Menu
        </div>
        <div class="tallythemesetup_import_message5" style="margin-bottom:40px; display:none;">
        	<img src="<?php echo TALLYTHEMESETUP__PLUGIN_URL; ?>assets/images/loader.gif" />Importing Builder Content
        </div>
        <div class="tallythemesetup_import_message6" style="margin-bottom:40px;">
        	
        </div>
        <a href="#" class="tallythemesetup_bootstrapguru_import button button-primary button-hero">Import Sample Data</a>
    </div>
    <?php
}