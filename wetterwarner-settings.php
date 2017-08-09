<?php
/*
* Wetterwarner Admin Einstellungen
* Author: Tim Knigge
* http://tim.knigge-ronnenberg.de/projekte/wetterwarner/dokumentation/
*/ 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action( 'admin_menu', 'wetterwarner_add_admin_menu' );
add_action( 'admin_init', 'wetterwarner_settings_init' );
add_action( 'admin_enqueue_scripts', 'wetterwarner_admin_scripts' );

function wetterwarner_add_admin_menu(  ) { 
	add_options_page( 'Wetterwarner Einstellungen', 'Wetterwarner', 'manage_options', 'wetterwarner', 'wetterwarner_options_page' );
}
function wetterwarner_settings_init(  ) { 
	register_setting( 'pluginPage', 'wetterwarner_settings');
	add_settings_section(
		'wetterwarner_pluginPage_section', 
		__( 'Weitere Wetterwarner Optionen', 'wordpress' ), 
		'wetterwarner_settings_section_callback', 
		'pluginPage'
	);
	add_settings_field( 
		'ww_cache', 
		__( 'Cache aktivieren (empfohlen)', 'wordpress' ), 
		'ww_cache_render', 
		'pluginPage', 
		'wetterwarner_pluginPage_section'
	);
	add_settings_field( 
		'ww_debug', 
		__( 'Debug Modus aktivieren', 'wordpress' ), 
		'ww_debug_render', 
		'pluginPage', 
		'wetterwarner_pluginPage_section' 
	);
    add_settings_field( 
	'ww_farbe_stufe1', 
	__( 'Hintergrundfarbe Stufe 1', 'wordpress' ), 
	'ww_farbe_stufe1_field',
	'pluginPage', 
	'wetterwarner_pluginPage_section'
	);
	    add_settings_field( 
	'ww_farbe_stufe2', 
	__( 'Hintergrundfarbe Stufe 2', 'wordpress' ), 
	'ww_farbe_stufe2_field',
	'pluginPage', 
	'wetterwarner_pluginPage_section'
	);
	    add_settings_field( 
	'ww_farbe_stufe3', 
	__( 'Hintergrundfarbe Stufe 3', 'wordpress' ), 
	'ww_farbe_stufe3_field',
	'pluginPage', 
	'wetterwarner_pluginPage_section'
	);
	    add_settings_field( 
	'ww_farbe_stufe4', 
	__( 'Hintergrundfarbe Stufe 4', 'wordpress' ), 
	'ww_farbe_stufe4_field',
	'pluginPage', 
	'wetterwarner_pluginPage_section'
	);
}
function wetterwarner_admin_scripts(  ) { 
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( '/js/wp-color-picker-alpha.js',  __FILE__ ), array( 'wp-color-picker' ), '1.2.2', true );
}
function ww_cache_render(  ) { 
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_cache'] ) ) $options['ww_cache'] = false;
	?>
    <input type='checkbox' name='wetterwarner_settings[ww_cache]' <?php checked( $options['ww_cache'], 1 ); ?> value='1'>
	<?php
}
function ww_debug_render(  ) { 
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_debug'] ) ) $options['ww_debug'] = false;
	?>
	<input type='checkbox' name='wetterwarner_settings[ww_debug]' <?php checked( $options['ww_debug'], 1 ); ?> value='1'>
	<?php
}
function ww_farbe_stufe1_field( ) {
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_farbe_stufe1'] ) ) $options['ww_farbe_stufe1'] = 'rgba(255,255,,0.2)';
    echo '<input type="text" class="color-picker" name="wetterwarner_settings[ww_farbe_stufe1]" data-alpha="true" value="' .$options['ww_farbe_stufe1']. '" class="cpa-color-picker" >';
}
function ww_farbe_stufe2_field( ) {
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_farbe_stufe2'] ) ) $options['ww_farbe_stufe2'] = 'rgba(255,125,0,0.2)';
    echo '<input type="text" class="color-picker" name="wetterwarner_settings[ww_farbe_stufe2]" data-alpha="true" value="' .$options['ww_farbe_stufe2']. '" class="cpa-color-picker" >';
}
function ww_farbe_stufe3_field( ) {
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_farbe_stufe3'] ) ) $options['ww_farbe_stufe3'] = 'rgba(255,0,0,0.2)';
    echo '<input type="text" class="color-picker" name="wetterwarner_settings[ww_farbe_stufe3]" data-alpha="true" value="' .$options['ww_farbe_stufe3']. '" class="cpa-color-picker" >';
}
function ww_farbe_stufe4_field( ) {
	$options = get_option( 'wetterwarner_settings' );
	if( !isset( $options['ww_farbe_stufe4'] ) ) $options['ww_farbe_stufe4'] = 'rgba(200,0,180,0.2)';
    echo '<input type="text" class="color-picker" name="wetterwarner_settings[ww_farbe_stufe4]" data-alpha="true" value="' .$options['ww_farbe_stufe4']. '" class="cpa-color-picker" >';
}
function wetterwarner_check_folder_permissions( ) {
	$folder = __DIR__ . '/tmp/';
	if (is_writable($folder))
	return true;
	else
	return false;
}
function wetterwarner_check_konfig( ) {
	if(is_writable(__DIR__ . "/tmp/"))$temp = "green"; else $temp = "red";
	if(ini_get('allow_url_fopen'))$phpini = "green"; else $phpini = "red";

	if(function_exists('curl_version')){
		$version = curl_version();
		$curl = "green";
		$curl_text = "CURL Version: ".$version["version"];
	}
	else{
		 $curl = "red";
		 $curl_text = "CURL nicht installiert.";
	}
	$php_version = phpversion();
	$php_version_min = '5.3.0';
	if (version_compare(PHP_VERSION, $php_version_min) >= 0) $phpv = "green"; else $phpv = "red";
	echo '<table style="border-collapse:collapse;border-spacing:0;border-color:#ccc;width:250px"><tr><th style="font-family:Arial, sans-serif;font-size:14px;font-weight:bold;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#f0f0f0;vertical-align:top">Konfigurationsprüfung<br></th></tr><tr><td style="font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:'.$temp.';background-color:#fff;vertical-align:top">/tmp/ Ordner beschreibbar<br></td></tr><tr><td style="font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:'.$phpini.';background-color:#fff;vertical-align:top">php.ini korrekt konfiguriert<br></td></tr><tr><td style="font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:'.$phpv.';background-color:#fff;vertical-align:top">PHP Version '.$php_version.'<br></td></tr><tr><td style="font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:'.$curl.';background-color:#fff;vertical-align:top">'.$curl_text.'<br></td></tr></table>';
	echo '<br><hr>';
}
function wetterwarner_settings_section( ){
	$beschreibbar = wetterwarner_check_folder_permissions();
	if($beschreibbar){
		do_settings_sections( 'pluginPage' );
	echo 'Ich empfehle den Cache zu aktivieren! So muss das Plugin nicht bei jedem Seitenaufruf die externen Inhalte neu laden, <br>sondern kann diese direkt von deinem Webspace laden. Dies verkürzt die Ladezeit für deine Besucher.';
}
	else{
		echo '<h3><font color="red">/tmp/ Ordner nicht beschreibbar!</font></h3><br>';
		echo 'Cache kann nicht aktiviert werden. Stelle sicher das der /tmp/ Ordner im Wetterwarner Verzeichnis (/wp-content/plugins/wetterwarner) Schreibberechtigungen besitzt. (777)';
	} 
}
function wetterwarner_settings_footer(  ) { 
	echo __( '<a href="http://tim.knigge-ronnenberg.de/projekte/wetterwarner/dokumentation/" target="_blank">Dokumentation</a> | <a href="http://tim.knigge-ronnenberg.de/kontakt" target="_blank">Kontakt</a>', 'wordpress' );
}
function wetterwarner_settings_section_callback(  ) { 
	echo __( 'Nachfolgende Optionen sind unabhängig von den Widget Einstellungen.<br><br>', 'wordpress' );
}
function wetterwarner_admin_notification(  ) { 
		$notification_content = file('http://tim.knigge-ronnenberg.de/wetterwarner/admin_notification.txt');
		if($notification_content[0] != 0)
		{
			?>
			<div class="notice notice-info is-dismissible">
			<h3><?php echo $notification_content[1];?></h3>
		</div>
		<?php
		}
}
function wetterwarner_options_page(  ) {
	?>
	<form action='options.php' method='post'>
	<h1>Wetterwarner</h1>
		<?php
		settings_fields( 'pluginPage' );
		wetterwarner_admin_notification();	
		wetterwarner_check_konfig();
		wetterwarner_settings_section();
		submit_button();
		wetterwarner_settings_footer();
		?>
	</form>
	<?php
}
?>