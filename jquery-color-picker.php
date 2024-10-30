<?php
/* 
Plugin Name: jQuery Color Picker
Plugin URI: http://creativedev.in
Version: 1.0 
Author: softy.5454
Description: A simple plugin to select color in the same way you select color in Adobe Photoshop using jQuery
*/
/*To enqueue css*/
wp_register_style('layout',plugins_url('css/layout.css',__FILE__));
wp_enqueue_style('layout');
wp_register_style('colorpickercss',plugins_url('css/colorpicker.css',__FILE__));
wp_enqueue_style('colorpickercss');
if(!is_admin()){
		wp_register_script('colorpickerjs',plugins_url('js/colorpicker.js',__FILE__),array('jquery'), false, false);
		wp_enqueue_script('colorpickerjs');
		wp_register_script('eyejs',plugins_url('js/eye.js',__FILE__),array('jquery'), false, false);
		wp_enqueue_script('eyejs');
		wp_register_script('utilsjs',plugins_url('js/utils.js',__FILE__),array('jquery'), false, false);
		wp_enqueue_script('utilsjs');
}

/* core files included */
include_once('classes/core.php');
/*To enqueue script */

/* activate hook */
register_activation_hook( __FILE__, 'colorpicker_activate');
function colorpicker_activate(){
	global $wpdb;
    $table_name = $wpdb->prefix . "colorpicker";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
 		$sql = "CREATE TABLE `$table_name` (
						 `nCpId` int(11) NOT NULL AUTO_INCREMENT,
						 `nPickerType` tinyint(1) NOT NULL COMMENT '1=Text,2=Icon',
						 `sPickername` varchar(255) NOT NULL,
						 `sDefaultColor` varchar(255) NOT NULL,
						 `sShortcode` varchar(255) NOT NULL,
						 PRIMARY KEY (`nCpId`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    	dbDelta($sql);
	}
}

/* Deactivate hook */
register_deactivation_hook( __FILE__, 'colorpicker_deactivate');
function colorpicker_deactivate(){
	global $wpdb;
	$table_name = $wpdb->prefix . "colorpicker";
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}
function color_picker($atts,$content=null)
{ 

    $color= $atts['color'];
	$id=$atts['id'];
	$type=$atts['type'];
	if($type=='textfield'){ 
	 
	  $data = '<input type="text" maxlength="6" size="6" id="'.$id.'" value="'.$color.'">
				<script type="text/javascript">
					(function($) {$("#'.$id.'").ColorPicker({
						onSubmit: function(hsb, hex, rgb, el) {		
							$(el).val(hex);
							$(el).ColorPickerHide();		
						},		
						onBeforeShow: function () {		
							$(this).ColorPickerSetColor(this.value);		
						}
					})
					.bind("keyup", function(){
						$(this).ColorPickerSetColor(this.value);
		}); })(jQuery);
	  </script>';
	}else{
			$data = '<div id="'.$id.'" style="position: relative;width: 36px;height: 36px;background: url('.plugins_url('images/select.png',__FILE__).');"><div style="position: absolute;top: 3px;left: 3px;width: 30px;height: 30px;
			background:url('.plugins_url('images/select.png',__FILE__).') center #'.$color.';"></div></div>
			<script type="text/javascript">
			(function($) {$("#'.$id.'").ColorPicker({
					color: "#'.$color.'",
					onShow: function (colpkr) {
						$(colpkr).fadeIn(500);
						return false;
				},
					onHide: function (colpkr) {
						$(colpkr).fadeOut(500);
						return false;
			},
					onChange: function (hsb, hex, rgb) {
					$("#'.$id.' div").css("backgroundColor", "#" + hex);
			}
		}); })(jQuery);</script>';
			
	}return $data;
}
add_shortcode('color-picker','color_picker');
/* color picker admin page */
function colorpicker_admin() { 
	include('colorpicker_admin.php');
	$colorpicker->colorpickersettings = new Colorpicker();
}
/* color picker admin Menu in setting */
function colorpicker_admin_actions() {
	add_options_page("Color Picker", "Color Picker", 'manage_options', "Color-Picker", "colorpicker_admin");
}
add_action('admin_menu', 'colorpicker_admin_actions');