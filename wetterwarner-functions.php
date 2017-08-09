<?php
/*
* Wetterwarner Funktionen
* Author: Tim Knigge
* http://tim.knigge-ronnenberg.de/projekte/wetterwarner/dokumentation/
*/ 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('DOCUMENT_ROOT', dirname(__FILE__));
require_once dirname(__FILE__) . '/resources/file-cache/file-cache.php';
function wetterwarner_xml($feed_url){
$use_errors = libxml_use_internal_errors(true);
if( !$xml = simplexml_load_file($feed_url))
			throw new Exception('Fehler beim Einlesen der XML Datei. Bitte Pfad überprüfen! '. $feed_url);
libxml_clear_errors();
libxml_use_internal_errors($use_errors);
		/* Prüfen ob Feed ID gültig */
		if( !isset($xml->channel[0]->item) ){
			throw new Exception('Feed ID konnte nicht gefunden werden. Konfiguration prüfen!');
			}
		return $xml;
}
function wetterwarner_meldungen($xml_data, $instance){
		/* Feed einlesen */
		$feed = array();
		$i = $instance['ww_max_meldungen'];
		foreach($xml_data->channel[0]->item as $item) {
		if( $i-- == 0 )
			break;
		$feed[] = array(
        'title'        => (string) $item->title,
        'description'  => (string) $item->description,
        'link'         => (string) $item->guid,
        'date'         => date('d.m.Y H:i', strtotime((string) $item->pubDate))
		);
		}
		return $feed;
}
function wetterwarner_wetterkarte($instance, $args, $region) { 
	$options = get_option('wetterwarner_settings');
	if (!$instance['ww_kartenbundeslandURL'] or $instance['ww_kartenbundeslandURL']== "")
		throw new Exception('Karten URL konnte nicht abgerufen werden.');
	
	$karten_url = $instance['ww_kartenbundeslandURL'];
	if(!empty($options))
	if (isset($options['ww_cache']))
	{
	wetterwarner_cache_img($instance['ww_kartenbundeslandURL'], $args['widget_id'], 600);
	$karten_url= plugin_dir_url( __FILE__ ) . "tmp/map-".$args['widget_id'].".png";
	}
	$karte = '<br><a href="http://www.wettergefahren.de/index.html" target="_blank" title="Aktuelle Wetterwarnungen für '.$region.'"><img src="'.$karten_url.'" style="border: 0;" alt="Aktuelle Wetterwarnungen für'.$region.'" width="'.$instance["ww_kartengroesse"].'%"/></a>';
	return $karte;
}
function wetterwarner_cache_img($url, $name, $valid_for) {
	$file_cache = new FileCache("map-".$name.".png", $url, $valid_for);
}
function wetterwarner_cache_rss($url, $name, $valid_for) {
	$file_cache = new FileCache($name.'.rss', $url, $valid_for);
}
function wetterwarner_feed_link($instance, $parameter){
	if(strpos($instance['ww_text_feed'], '%region%'))
		$feed_title = str_replace('%region%', $parameter->region, $instance['ww_text_feed']);
	else
		$feed_title = $instance['ww_text_feed'];
	$feedlink = '<p class="ww_wetterfeed"><span class="fa fa-rss"></span><a href="'.$parameter->feed_url.'"> '.$feed_title.'</a></p>';
	return $feedlink;
}
function wetterwarner_quelle($value){
	$quelle = explode("Quelle:", $value['description']);
	$quelle = explode("<br />", $quelle[1]);
	$quelle = '<br><span class="ww_Quelle">Quelle: '.$quelle[0].'</span>';
	return $quelle;
}
function wetterwarner_gueltigkeit($value, $parameter){
	$gueltigkeit = explode($parameter->region, $value['description']);
	$gueltigkeit = explode("<br />", $gueltigkeit[1]);
	$gueltigkeit = '<br><span class="ww_Zeit">Gültig: '.$gueltigkeit[1].'</span>';
	return $gueltigkeit;
	
}
function wetterwarner_tooltip($value){
		$tooltip = explode("Quelle:", $value['description']);
		$tooltip = str_replace(array("\r","\n"),"",$tooltip[0]);
		$tooltip_code = 'onmouseover="popup(\' '.$tooltip.' \')"';
		return $tooltip_code;
}
function enqueueStyleAndScripts(){
		wp_register_script( 'tooltip', plugin_dir_url( __FILE__ ) . '/js/nhpup_1.1.js', array( 'jquery' ), '1.1', false );
		wp_register_style( 'style-frontend',  plugin_dir_url( __FILE__ ) . 'css/style-frontend.css' );
		wp_register_style( 'weather-icons',  plugin_dir_url( __FILE__ ) . 'resources/weather-icons/css/weather-icons.min.css' );

		/* Font Awesome vom CDN laden */
		wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style('style-frontend');
		wp_enqueue_style('weather-icons');
		wp_enqueue_script( 'tooltip' );
}
function wetterwarner_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php?page=wetterwarner' ) ) . '">' . __( 'Einstellungen', 'textdomain' ) . '</a>'
	), $links );
	return $links;
}
function wetterwarner_icons($shorttitle){	
		$icon="";
		switch ($shorttitle){
			case "STURMBÖEN":
			case "WINDBÖEN":
			case "SCHWERE STURMBÖEN":
			$icon = "wi-cloudy-windy";
			break;
			case "GEWITTER":
			case "STARKES GEWITTER":
			$icon = "wi-storm-showers";
			break;
			case "GEWITTER":
			case "STARKES GEWITTER":
			case "SCHWERES GEWITTER":
			case "EXTREMES GEWITTER":
			$icon = "wi-thunderstorm";
			break;
			case "DAUERREGEN":
			$icon = "wi-rain";
			break;
			case "SCHNEEFALL":
			case "STARKER SCHNEEFALL":
			$icon = "wi-snow";
			break;
			case "GLATTEIS":
			case "FROST":
			$icon = "wi-snowflake-cold";
			break;
			case "STARKREGEN": 
			$icon = "wi-rain-wind";
			break;
			case "NEBEL": 
			$icon = "wi-fog";
			break;
			case "TAUWETTER": 
			$icon = "wi-horizon-alt";
			break;
			default:
			$icon = "wi-cloudy";
			}
		return "ww_icon wi ".$icon;
}
function wetterwarner_parameter($xml_data, $instance){
			/* Variablen deklarieren */
			$rss_title = explode("Warnregion:", $xml_data->channel->title);
			$title = (empty($instance['ww_widget_titel'])) ? '' : apply_filters('ww_widget_titel', $instance['ww_widget_titel']);
			$feed_title = $instance['ww_text_feed'];
			$einleitung = $instance['ww_einleitungstext'];
			
			/* Variablen durch Text ersetzen */
			if(strpos($instance['ww_einleitungstext'], '%region%'))
			$einleitung = str_replace("%region%", $rss_title[1], $instance['ww_einleitungstext']);
			if(strpos($instance['ww_text_feed'], '%region%'))
			$feed_title = str_replace('%region%', $rss_title[1], $instance['ww_text_feed']);		
			if(strpos($title, '%region%'))
			$title = str_replace('%region%', $rss_title[1], $title);
			$parameter = (object) array(
			'einleitung' 	=> $einleitung,
			'feed_title' 	=> $feed_title,
			'widget_title' 	=> $title,
			'region'  		=> $rss_title[1],
			'rss_title'    	=> $instance['ww_einleitungstext'],
			'feed_url'		=> 'https://wettwarn.de/rss/'.strtolower($instance['ww_feed_id']).'.rss'
			);
			if($instance['ww_feed_id'] =="100")
			$parameter->feed_url = "http://tim.knigge-ronnenberg.de/projekte/wetterwarner/";
			return $parameter;
} 
function wetterwarner_debug_info($instance, $options){
$debug_inf = "++++++++++++++++++++++++++++++\n";
$debug_inf .= "Wetterwarner Version: 2.2\n";
$debug_inf .= "Temp Ordner beschreibbar:"; if(is_writable(__DIR__ . '/tmp/')) $debug_inf .= " ja\n"; else $debug_inf .= "nein\n";
$debug_inf .= "Seiten URL: ".site_url()."\n";
$debug_inf .= "Wetterwarner Pfad: ".plugin_dir_path(__FILE__)."\n";
$debug_inf .= "Cache aktiviert: "; if(!empty($options))if(isset($options['ww_cache'])) $debug_inf .= "ja\n";  else $debug_inf .= "nein\n";
$debug_inf .= "PHP Ini korrekt: "; if(ini_get('allow_url_fopen'))$debug_inf .= "ja\n"; else $debug_inf .= "nein\n";
$debug_inf .= "Feed ID: ".$instance['ww_feed_id']."\n";
$debug_inf .= "Max Meldungen: ".$instance['ww_max_meldungen']."\n";
$debug_inf .= "Kartengroesse: ".$instance['ww_kartengroesse']."\n";
$debug_inf .= "Kartenbundesland: ".$instance['ww_kartenbundesland']."\n";
$debug_inf .= "Karten URL: ".$instance['ww_kartenbundeslandURL']."\n";
$debug_inf .= "Feed Link zeigen: "; if($instance['ww_feed_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Gueltigkeit zeigen: "; if($instance['ww_gueltigkeit_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Quelle zeigen: "; if($instance['ww_quelle_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Immer zeigen: "; if($instance['ww_immer_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Tooltip aktiviert: "; if($instance['ww_tooltip_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Icons zeigen: "; if($instance['ww_icons_zeigen']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Hintergrundfarbe zeigen: "; if($instance['ww_hintergrundfarbe']) $debug_inf .= "ja\n"; else $debug_inf .="nein\n";
$debug_inf .= "Farbe Stufe 1: "; if(!empty($options))if(isset($options['ww_farbe_stufe1'])) $debug_inf .= $options['ww_farbe_stufe1']."\n";  else $debug_inf .= "! Kein Wert !\n";
$debug_inf .= "Farbe Stufe 2: "; if(!empty($options))if(isset($options['ww_farbe_stufe2'])) $debug_inf .= $options['ww_farbe_stufe2']."\n";  else $debug_inf .= "! Kein Wert !\n";
$debug_inf .= "Farbe Stufe 3: "; if(!empty($options))if(isset($options['ww_farbe_stufe3'])) $debug_inf .= $options['ww_farbe_stufe3']."\n";  else $debug_inf .= "! Kein Wert !\n";
$debug_inf .= "Farbe Stufe 4: "; if(!empty($options))if(isset($options['ww_farbe_stufe4'])) $debug_inf .= $options['ww_farbe_stufe4']."\n";  else $debug_inf .= "! Kein Wert !\n";
$debug_inf .= "++++++++++++++++++++++++++++++";
//echo '<span class="ww_debug"'.$debug_inf.'</span>';
$return = "<textarea rows=\"8\" cols=\"50\" readonly>".$debug_inf."</textarea>";
return $return;
}
function wetterwarner_meldung_hintergrund($value, $options){
		$stufe = explode("Stufe ", $value['description']);
		$stufe = explode(" ", $stufe[1]);
			switch ($stufe[0]){
			case "1":
			$farbe = $options['ww_farbe_stufe1'];
			break;
			case "2":
			$farbe = $options['ww_farbe_stufe2'];
			break;
			case "3":
			$farbe = $options['ww_farbe_stufe3'];
			break;
			case "4":
			$farbe = $options['ww_farbe_stufe4'];
			break;
			}
			$hintergrund = "style=\"background-color:".$farbe."\"";
			return $hintergrund;
}
?>