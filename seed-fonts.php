<?php
/*
Plugin Name: Seed Fonts
Plugin URI: https://github.com/SeedThemes/seed-fonts
Description: A plugin for thai font-face
Version: 0.9.2
Author: Seed Themes
Author URI: http://www.seedthemes.com
License: GPL2
*/

/*
Copyright 2016 SeedThemes  (email : info@seedthemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('Seed_Fonts'))
{
	class Seed_Fonts
	{
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
        } // END public function __construct
        
        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        public static $fonts = array	(
        	"athiti" => array(
        		"weights" => array(500, 600)
        		),
        	"kanit" => array(
        		"weights" => array(300, 400, 500)
        		),
        	"mitr" => array(
        		"weights" => array(300, 400, 500)
        		),
        	"prompt" => array(
        		"weights" => array(400, 500, 600)
        		),
        	);

        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
    } // END class Seed_Fonts
} // END if(!class_exists('Seed_Fonts'))

if(class_exists('Seed_Fonts'))
{
    // Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Seed_Fonts', 'activate'));
	register_deactivation_hook(__FILE__, array('Seed_Fonts', 'deactivate'));

    // instantiate the plugin class
	$seed_fonts = new Seed_Fonts();
}

add_action( 'wp_enqueue_scripts', 'seed_fonts_scripts', 30 );

function seed_fonts_scripts() {
	if( ! is_admin() ) {
		$is_enabled = ( get_option( 'seed_fonts_is_enabled' ) );
		$font = get_option( 'seed_fonts_font' );
		$weight = get_option( 'seed_fonts_weight' );
		$selectors = get_option( 'seed_fonts_selectors' );
		$is_important = ( get_option( 'seed_fonts_is_important' ) );


		if( $is_enabled && ( $font !== FALSE ) && ( $font != '' ) ) {
			if( $selectors != '' )
				$font_styles = $selectors.' ';

			$font_styles .= '{font-family: "'.$font.'",  sans-serif'.( $is_important ? ' !important' : '' ).';';
			if( $weight != '' )
				$font_styles .= ' font-weight: '.$weight.( $is_important ? ' !important' : '' ).';';
			$font_styles .= ' }';
			wp_enqueue_style( 'seed-fonts-all', plugin_dir_url( __FILE__ ) . '/fonts/' . $font . '/all.css' , array(  ) );
			wp_add_inline_style( 'seed-fonts-all', $font_styles );
		}
	}
}

add_action( 'admin_menu', 'seed_fonts_setup_menu' );

function seed_fonts_setup_menu() {
	$seed_font_page = add_submenu_page ( 'themes.php', 'Seed Fonts', 'Fonts', 'manage_options', 'seed-fonts', 'seed_fonts_init' );

	add_action( 'load-' . $seed_font_page, 'seed_fonts_admin_styles' );
}

function seed_fonts_admin_styles() {
//		foreach( Seed_fonts::$fonts as $_font ):
//			wp_enqueue_style( 'seed-fonts-'.$_font["font-family"], $_font["css"] , array() );
//		endforeach;

//		wp_enqueue_style( 'seed-fonts-admin', plugin_dir_url( __FILE__ ) . '/seed-fonts-admin.css' , array() );

	wp_enqueue_script( 'seed-fonts', plugin_dir_url( __FILE__ ) . '/seed-fonts-admin.js' , array( 'jquery' ), '2016-1', true );
	wp_enqueue_style( 'seed-fonts', plugin_dir_url( __FILE__ ) . '/seed-fonts-admin.css' , array(  ) );
}

function seed_fonts_init() {
	$is_enabled = get_option( 'seed_fonts_is_enabled' );
	$font = get_option( 'seed_fonts_font' );
	$weight = get_option( 'seed_fonts_weight' );
	$selectors = get_option( 'seed_fonts_selectors' );
	$is_important = get_option( 'seed_fonts_is_important' );

	if( $font === FALSE )
		$font = key ( Seed_fonts::$fonts );

	if( $weight === FALSE )
		$weight = '';

	if( $selectors === FALSE )
		$selectors = 'h1, h2, h3, h4, h5, h6, ._heading';

	echo '<div class="wrap">';
	echo '<h1>Seed Fonts</h1>';

	echo '<form id="seed-fonts-form" method="post" name="seed_fonts_form" action="'.get_bloginfo( 'url' ).'/wp-admin/admin-post.php" >';
	echo '<table class="form-table"><tbody>';
	echo '<tr><th scope="row">Enable</th><td><label for="seed-fonts-is-enabled"><input id="seed-fonts-is-enabled" type="checkbox" name="seed_fonts_is_enabled" value="on"'.( $is_enabled ? ' checked="checked"' : '').' /></label></td></tr>';
	echo '<tr><th scope="row">Fonts</th><td><select id="seed-fonts-font" name="seed_fonts_font"'.( $is_enabled ? '' : ' disabled' ).'>';
	foreach( Seed_fonts::$fonts as $_font_family => $_font ):
		echo '<option value="'.$_font_family.'" '.(($font == $_font_family) ? ' selected="selected"' : '').'>'.$_font_family.'</option>';
	endforeach;
	echo '</select></td></tr>';

	echo '<tr><th scope="row">Weight</th><td><select id="seed-fonts-weight" name="seed_fonts_weight"'.( $is_enabled ? '' : ' disabled' ).'>';
	echo '<option value=""></option>';
	foreach( Seed_fonts::$fonts[$font]['weights'] as $_weight ):
		echo '<option value="'.$_weight.'" '.(($weight == $_weight) ? ' selected="selected"' : '').'>'.$_weight.'</option>';
	endforeach;
	echo '</select></td></tr>';

	echo '<tr><th scope="row">Selectors</th><td><input id="seed-fonts-selectors" class="regular-text" type="text" name="seed_fonts_selectors" value="'.htmlspecialchars( $selectors ).'"'.( $is_enabled ? '' : ' disabled' ).' /></td></tr>';
	echo '<tr><th scope="row">Force using this font?</th><td><label for="seed-fonts-is-important"><input id="seed-fonts-is-important" type="checkbox" name="seed_fonts_is_important" value="on"'.( $is_important ? ' checked="checked"' : '').( $is_enabled ? '' : ' disabled' ).' /> !important</label></td></tr>';
	echo '<tr><th scope="row">Generated CSS</th><td><textarea id="seed-fonts-css-generated" rows="4" cols="60" class="code" readonly'.( $is_enabled ? '' : ' style="display:none"' ).'></textarea>';
	echo '<input type="hidden" name="action" value="seed_fonts_save_options" /></td></tr></tbody></table>';
	echo '<p class="submit"><input id="seed-fonts-submit" type="button" class="button button-primary" value="Save Changes" /></p>';

	foreach( Seed_fonts::$fonts as $_font_family => $_font ):
		echo '<select id="seed-fonts-'.$_font_family.'-weights" style="display:none">';
		echo '<option value=""></option>';
		foreach( $_font['weights'] as $_weight ):
			echo '<option value="'.$_weight.'">'.$_weight.'</option>';
		endforeach;
		echo '</select>';
	endforeach;

	echo '</form>';
	
	echo '</div>';
}

add_action( 'admin_post_seed_fonts_save_options', 'seed_fonts_save' );

function seed_fonts_save() {
	if( array_key_exists( 'seed_fonts_is_enabled', $_POST ) && ( $_POST['seed_fonts_is_enabled'] == 'on' ) )
		update_option( 'seed_fonts_is_enabled' , true );
	else
		update_option( 'seed_fonts_is_enabled' , false );

	update_option( 'seed_fonts_font' , $_POST['seed_fonts_font'] );
	update_option( 'seed_fonts_weight' , $_POST['seed_fonts_weight'] );
	update_option( 'seed_fonts_selectors' , $_POST['seed_fonts_selectors'] );

	if( array_key_exists( 'seed_fonts_is_important', $_POST ) && ( $_POST['seed_fonts_is_important'] == 'on' ) )
		update_option( 'seed_fonts_is_important' , true );
	else
		update_option( 'seed_fonts_is_important' , false );

//		print_r($_POST);

	header("Location: admin.php?page=seed-fonts&saved=true");
}

function seed_fonts( ) {
}