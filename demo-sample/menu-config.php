<?php
$top_menu    = get_term_by('name', 'Top Menu', 'nav_menu');
$main_menu   = get_term_by('name', 'Main Menu', 'nav_menu');
$footer_menu = get_term_by('name', 'Main Menu', 'nav_menu');
set_theme_mod( 'nav_menu_locations', array(
	'top-menu' => $top_menu->term_id,
	'primary' => $main_menu->term_id,
	'footer-menu' => $footer_menu->term_id,
	)
);