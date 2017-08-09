<?php
/**
 * Plugin Name: Wetterwarner
 * Plugin URI: http://tim.knigge-ronnenberg.de/projekte/wetterwarner/
 * Description: Zeigt amtliche Wetterwarnungen in einem Widget an. Gefällt Dir? Lass es mich wissen, auf der <a target="_blank" href="https://www.facebook.com/wetterwarner">Facebook Seite</a>. Danke!
 * Version: 2.2
 * Author: Tim Knigge
 * Author URI: http://tim.knigge-ronnenberg.de
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once dirname(__FILE__) . '/resources/file-cache/file-cache.php';
require_once dirname(__FILE__) . '/wetterwarner-settings.php';
require_once dirname(__FILE__) . '/wetterwarner-functions.php';
if(!class_exists('Wetterwarner_Widget')) {
    class Wetterwarner_Widget extends WP_Widget {
		public function __construct()
		{
        parent::__construct(
            'Wetterwarner_Widget',
            'Wetterwarner',
            array(
                'description' => __('Zeigt amtliche Wetterwarnungen sowie auf Wunsch eine Wetterkarte in einem Widget an.'),
                'customize_selective_refresh' => true,
            )
        );
		add_action( 'wp_enqueue_scripts', 'enqueueStyleAndScripts' );
		}
		/* Update Funktion der Einstellungen in die WP Datenbank */
		public function update($new_instance, $old_instance)
		{
		$instance = array();
		/* Textboxen */
        $instance['title'] = sanitize_title($new_instance['title'], 'Wetterwarnungen', 'save');
		$instance['ww_widget_titel'] = sanitize_text_field($new_instance['ww_widget_titel'], 'Wetterwarnungen', 'save');
        $instance['ww_einleitungstext'] = sanitize_text_field($new_instance['ww_einleitungstext']);
		$instance['ww_hinweistext'] = sanitize_text_field($new_instance['ww_hinweistext']);
		$instance['ww_text_feed'] = sanitize_text_field($new_instance['ww_text_feed']);
        $instance['ww_feed_id'] = sanitize_key(strtolower($new_instance['ww_feed_id']));
		
        if (!isset($new_instance['ww_kartengroesse']) OR !is_numeric($new_instance['ww_kartengroesse']))
			$instance['ww_kartengroesse'] = (int) ($old_instance['ww_kartengroesse']);
		else
			$instance['ww_kartengroesse'] = (int) ($new_instance['ww_kartengroesse']);
		if (!isset($new_instance['ww_max_meldungen']) || !is_numeric($new_instance['ww_max_meldungen'])) 
			$instance['ww_max_meldungen'] = (int) ($old_instance['ww_max_meldungen']);
		else
			$instance['ww_max_meldungen'] = (int) ($new_instance['ww_max_meldungen']);
		
		/* Dropdowns */
			$karten_url = "";
		switch ($new_instance['ww_kartenbundesland']){
			case "Schleswig-Holstein":
			case "Hamburg":
			$karten_url = "warning_map_shh.png";
			break;
			case "Niedersachsen":
			case "Bremen":
			$karten_url = "warning_map_nib.png";
			break;
			case "Rheinland-Pfalz":
			case "Saarland":
			$karten_url = "warning_map_rps.png";
			break;
			case "Berlin":
			case "Brandenburg":
			$karten_url = "warning_map_bbb.png";
			break;
			case "Nordrhein-Westfalen":
			$karten_url = "warning_map_nrw.png";
			break;
			case "Sachsen":
			$karten_url = "warning_map_sac.png";
			break;
			case "Sachsen-Anhalt":
			$karten_url = "warning_map_saa.png";
			break;
			case "Thüringen":
			$karten_url = "warning_map_thu.png";
			break;
			case "Bayern":
			$karten_url = "warning_map_bay.png";
			break;
			case "Hessen":
			$karten_url = "warning_map_hes.png";
			break;
			case "Mecklenburg-Vorpommern":
			$karten_url = "warning_map_mvp.png";
			break;
			case "Baden-Württemberg":
			$karten_url = "warning_map_baw.png";
			break;
			default:
			$karten_url = "warning_map.png";
		}
			/* Korrekte Bild-URL zum eingestellten Kartenbundesland in Datenbank speichern 	*/
			$url = "https://www.dwd.de/DWD/warnungen/warnapp/json/".$karten_url;
			$instance['ww_kartenbundeslandURL'] = (string) $url;
			$instance['ww_kartenbundesland'] = (string) strip_tags($new_instance['ww_kartenbundesland']);
			
			/* Checkboxes */
			$instance['ww_immer_zeigen'] = ($new_instance ['ww_immer_zeigen']) ? 1 : 0;
			$instance['ww_feed_zeigen'] = ($new_instance ['ww_feed_zeigen']) ? 1 : 0;
			$instance['ww_gueltigkeit_zeigen']  = ($new_instance ['ww_gueltigkeit_zeigen']) ? 1 : 0;
			$instance['ww_quelle_zeigen']  = ($new_instance ['ww_quelle_zeigen']) ? 1 : 0;
			$instance['ww_tooltip_zeigen']  = ($new_instance ['ww_tooltip_zeigen']) ? 1 : 0;
			$instance['ww_icons_zeigen']  = ($new_instance ['ww_icons_zeigen']) ? 1 : 0;
			$instance['ww_hintergrundfarbe']  = ($new_instance ['ww_hintergrundfarbe']) ? 1 : 0;
			$instance['ww_meldungen_verlinken']  = ($new_instance ['ww_meldungen_verlinken']) ? 1 : 0;
            return $instance;
		}
		/* Aufbau Formular Widget Einstellungen / Default Werte	*/
            public function form($instance) {
				try{
				/* Prüfung ob PHP.ini korrekt konfiguriert ist */
			if(ini_get('allow_url_fopen')== false)
			throw new Exception('Externes laden von Inhalten in PHP.ini deaktiviert: Plugin kann nicht ordnungsgemäß funktionieren.</b><br>Bitte das Attribut allow_url_fopen auf ON setzen!');
            $instance = wp_parse_args((array) $instance, array(
				'ww_widget_titel' => 'Wetterwarnungen',
				'ww_text_feed' => 'Wetterwarnungen %region%',
				'ww_max_meldungen' => '3',
				'ww_feed_id' => 'HAN',
				'ww_einleitungstext' => 'Wetterwarnungen für %region%',
				'ww_hinweistext' => 'Keine Wetterwarnungen für %region% vorhanden!',
				'ww_kartengroesse' => '65',
				'ww_kartenbundesland' => 'Niedersachsen',
				'ww_kartenbundeslandURL' => 'https://www.dwd.de/DWD/warnungen/warnapp/json/warning_map_nib.png',
				'ww_immer_zeigen' => false,
				'ww_gueltigkeit_zeigen' => false,
				'ww_feed_zeigen' => false,
				'ww_tooltip_zeigen' => true,
				'ww_icons_zeigen' => true,
				'ww_hintergrundfarbe'=> true,
            	'ww_meldungen_verlinken'=> true
            ));
            ?>
			<p style="border-bottom: 1px solid #DFDFDF;"><strong>Widget Titel</strong></p>
			<p>
                <input id="<?php echo $this->get_field_id(ww_widget_titel); ?>" name="<?php echo $this->get_field_name('ww_widget_titel'); ?>" type="text" value="<?php echo $instance['ww_widget_titel']; ?>" size="18"/>
            </p>
				<p style="border-bottom: 1px solid #DFDFDF;"><strong>Feed ID</strong></p>
                <input id="<?php echo $this->get_field_id('ww_feed_id'); ?>" name="<?php echo $this->get_field_name('ww_feed_id'); ?>" type="text" maxlength="3" size="3" value="<?php echo $instance['ww_feed_id']; ?>" />
				<br><p>Die Feed ID der Seite <a href="http://wettwarn.de/warnregion" target="_blank">wettwarn.de</a> entnehmen!</p>
			<p style="border-bottom: 1px solid #DFDFDF;"><strong>Erweiterte Einstellungen</strong></p>
			<table>
				<tr><td>Einleitung</td><td><input id="<?php echo $this->get_field_id('ww_einleitungstext'); ?>" name="<?php echo $this->get_field_name('ww_einleitungstext'); ?>" type="text" value="<?php echo $instance['ww_einleitungstext']; ?>" size="20"/></td></tr>
				<tr><td>Hinweistext</td><td><input id="<?php echo $this->get_field_id('ww_hinweistext'); ?>" name="<?php echo $this->get_field_name('ww_hinweistext'); ?>" type="text" value="<?php echo $instance['ww_hinweistext']; ?>" size="20"/></td></tr>
				<tr><td>Feed Link anzeigen</td><td><input id="<?php echo $this->get_field_id('ww_feed_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_feed_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_feed_zeigen'], true); ?>/></td></tr>
				<tr><td>Feed Text</td><td><input id="<?php echo $this->get_field_id('ww_text_feed'); ?>" name="<?php echo $this->get_field_name('ww_text_feed'); ?>" type="text" value="<?php echo $instance['ww_text_feed']; ?>" size="20" <?php if ($instance['ww_feed_zeigen'] == false) echo "disabled ";?>/></td></tr>
				<tr><td>Max. Meldungen<br></td><td><input id="<?php echo $this->get_field_id('ww_max_meldungen'); ?>" name="<?php echo $this->get_field_name('ww_max_meldungen'); ?>" maxlength="2" size="3" type="text" value="<?php echo $instance['ww_max_meldungen']; ?>" /></td></tr>
				<tr><td>Kartengröße</td><td><input id="<?php echo $this->get_field_id('ww_kartengroesse'); ?>" name="<?php echo $this->get_field_name('ww_kartengroesse'); ?>" type="text" maxlength="3" size="3" value="<?php echo $instance['ww_kartengroesse']; ?>" /> 0 = Karte unsichtbar</td></tr>
				<tr><td>Kartenbundesland</td><td>
				<select id="<?php echo $this->get_field_id('ww_kartenbundesland'); ?>" name="<?php echo $this->get_field_name('ww_kartenbundesland'); ?>" value="<?php echo $instance['ww_kartenbundesland']; ?>" > 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Deutschland') echo "selected" ?>> Deutschland </option>
					<option <?php if ($instance['ww_kartenbundesland'] == 'Baden-Württemberg') echo "selected" ?>>Baden-Württemberg</option>				
					<option <?php if ($instance['ww_kartenbundesland'] == 'Bayern') echo "selected" ?>>Bayern</option>
					<option <?php if ($instance['ww_kartenbundesland'] == 'Berlin') echo "selected" ?>>Berlin</option>
					<option <?php if ($instance['ww_kartenbundesland'] == 'Brandenburg') echo "selected" ?>>Brandenburg</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Bremen') echo "selected" ?>>Bremen</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Hamburg') echo "selected" ?>>Hamburg</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Hessen') echo "selected" ?>>Hessen</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Mecklenburg-Vorpommern') echo "selected" ?>>Mecklenburg-Vorpommern</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Niedersachsen') echo "selected" ?>>Niedersachsen</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Nordrhein-Westfalen') echo "selected" ?>>Nordrhein-Westfalen</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Rheinland-Pfalz') echo "selected" ?>>Rheinland-Pfalz</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Saarland') echo "selected" ?>>Saarland</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Sachsen') echo "selected" ?>>Sachsen</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Sachsen-Anhalt') echo "selected" ?>>Sachsen-Anhalt</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Schleswig-Holstein') echo "selected" ?>>Schleswig-Holstein</option> 
					<option <?php if ($instance['ww_kartenbundesland'] == 'Thüringen') echo "selected" ?>>Thüringen</option> 
				</select></td></tr>
				<tr><td>Gültigkeit anzeigen<br></td><td><input id="<?php echo $this->get_field_id('ww_gueltigkeit_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_gueltigkeit_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_gueltigkeit_zeigen'], true); ?>/></td></tr>
				<tr><td>Quelle anzeigen<br></td><td><input id="<?php echo $this->get_field_id('ww_quelle_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_quelle_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_quelle_zeigen'], true); ?>/></td></tr>
				<tr><td>Immer anzeigen<br></td><td><input id="<?php echo $this->get_field_id('ww_immer_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_immer_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_immer_zeigen'], true); ?>/></td></tr>
				<tr><td>Tooltip erzeugen<br></td><td><input id="<?php echo $this->get_field_id('ww_tooltip_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_tooltip_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_tooltip_zeigen'], true); ?>/></td></tr>
				<tr><td>Icons anzeigen<br></td><td><input id="<?php echo $this->get_field_id('ww_icons_zeigen'); ?>" name="<?php echo $this->get_field_name('ww_icons_zeigen'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_icons_zeigen'], true); ?>/></td></tr>
				<tr><td>Hintergrundfarbe aktivieren<br></td><td><input id="<?php echo $this->get_field_id('ww_hintergrundfarbe'); ?>" name="<?php echo $this->get_field_name('ww_hintergrundfarbe'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_hintergrundfarbe'], true); ?>/></td></tr>
				<tr><td>Meldungen verlinken<br></td><td><input id="<?php echo $this->get_field_id('ww_meldungen_verlinken'); ?>" name="<?php echo $this->get_field_name('ww_meldungen_verlinken'); ?>" type="checkbox" value="1" <?php checked(1, $instance['ww_meldungen_verlinken'], true); ?>/></td></tr>
			</table>
		
			<p style="border-bottom: 1px solid #DFDFDF;"></p>
			<?php }
			catch( Exception $e ) {
			echo '<p style="color:red; font-weight:bold">Leider ist etwas schief gelaufen.</p>',  $e->getMessage(), "\n";
			echo '<br><br>';
			}
        }
			/* Generierung Widget Front End */
        public function widget($args, $instance) {
			try {
			extract($args);
			$options = get_option('wetterwarner_settings');
			/* Feed einlesen und abrufen */
			if($instance['ww_feed_id'] != '100')
				$feed_url = 'https://wettwarn.de/rss/'.strtolower($instance['ww_feed_id']).'.rss';
			else
				$feed_url = 'http://tim.knigge-ronnenberg.de/wetterwarner/test.rss';
			$feed = $feed_url;
			if(!empty($options['ww_cache']))
			if ($options['ww_cache'])
			{
			wetterwarner_cache_rss($feed_url, strtolower($instance['ww_feed_id']), 600);
			$feed = plugin_dir_url( __FILE__ ) . "tmp/".$instance['ww_feed_id'].".rss";
			}
			$xml_data = wetterwarner_xml($feed);
			$feed = wetterwarner_meldungen($xml_data, $instance);	
			$parameter = wetterwarner_parameter($xml_data, $instance);
			$link ="";
		/* Bei Warnungen Widget aufbauen */
		if ($feed[0]['title'] != 'Keine Warnungen' ) {
		$output = $args['before_widget'];
		$output .= $args['before_title'].$parameter->widget_title.$args['after_title'];
		if(isset($parameter->einleitung))
		$output .= '<span class="ww_einleitung">'.$parameter->einleitung.'</span><br>';
		$output .= '<ul class="ww_wetterwarnungen">';
		foreach ($feed as $value) {
			if(strpos($value['title'], 'VORABINFORMATION')){
				$shorttitle = explode("VORABINFORMATION", $value['title']);
				$shorttitle = $shorttitle[1];
				$vorabinformation = true;
				}
			else {
				$shorttitle = explode(":", $value['title']);
				$shorttitle = trim($shorttitle[1]); 
				$vorabinformation = false;
				}
		$details = explode("Details:", $value['description']);
		$info_url = $details[1];
		/* Prüfen ob tooltip angezeigt werden soll */
		if ($instance['ww_tooltip_zeigen'])
		$tooltip_code = wetterwarner_tooltip($value);
		/* Prüfen ob Meldung verlinkt werden soll */
		if (isset($instance['ww_meldungen_verlinken'])AND $instance['ww_meldungen_verlinken'])
			$link = "href=\"".$info_url."\"";
		/* Zusammenbau der Wettermeldung */
		if($instance['ww_hintergrundfarbe'])
		$hintergrund = wetterwarner_meldung_hintergrund($value, $options);
		$output .= '<li class="ww_wetterwarnung"';
		if(isset($hintergrund)) 
			$output .= $hintergrund;
		$output .= ">";
		/* Prüfen ob icon angezeigt werden soll */
		if (isset($instance['ww_icons_zeigen']) AND $instance['ww_icons_zeigen'])
		$output .="<i class=\"".wetterwarner_icons($shorttitle)."\"></i>";	
		$output .= "<a ".$link."target=\"_blank\"";
		if(isset($tooltip_code))
			$output .= $tooltip_code;
		if(isset($vorabinformation) AND $vorabinformation){
			$output .= "style=\"font-style: italic;\">".$shorttitle."</a></span>";
			if (isset($instance['ww_icons_zeigen']) AND $instance['ww_icons_zeigen'])
			$output .= "<br><span class=\"ww_Info\"><i class=\"fa fa-info\"></i> Vorabinformation</span>";
		}
		else{
			$output .= "> ".$shorttitle."</a></span>";
		}
		/* Prüfen ob Gültigkeit angezeigt werden soll */
		if ($instance['ww_gueltigkeit_zeigen'])
			$output .= wetterwarner_gueltigkeit($value, $parameter);
		
			/* Prüfen ob Quelle angezeigt werden soll */
		if ($instance['ww_quelle_zeigen'])
			$output .= wetterwarner_quelle($value);
		}
		$output .='</li></ul>';
		}
		else{
			/* Prüfen ob Widget immer angezeigt werden soll */
			if ($instance['ww_immer_zeigen']){
				$output = $args['before_widget'];
				if(isset($instance['ww_hinweistext'])){
				if(strpos($instance['ww_hinweistext'], '%region%'))
				$hinweis = str_replace("%region%", $parameter->region, $instance['ww_hinweistext']);
				}
				$output .= $before_title . $parameter->widget_title . $after_title;
				if(isset($hinweis)){
				$output .= '<span class="ww_hinweis">';
			if(isset($instance['ww_icons_zeigen']) and $instance['ww_icons_zeigen'])
				$output .=  "<i class=\"wi wi-horizon-alt\" style=\"text-align:center;font-size:30pt;display: inline-block; width: 100%;\"></i><br>";
				$output .= $hinweis.'</span><br>';
				}
			}
		}
		if ($instance['ww_immer_zeigen'] or $feed[0]['title'] != 'Keine Warnungen'){
			/* Prüfen ob Karte angezeigt werden soll */
			if($instance['ww_kartengroesse']>0){
				$output .= wetterwarner_wetterkarte($instance, $args, $parameter->region);
			}
			/* Prüfen ob Link zum Feed angezeigt werden soll */
			if ($instance['ww_feed_zeigen']){
				$output .= wetterwarner_feed_link($instance, $parameter);
			}
		}
			$options = get_option('wetterwarner_settings');
			if(!empty($options['ww_debug']))
			if ($options['ww_debug'])
			{
				ini_set('display_errors', 'On');
				error_reporting(-1);
				$debug = '<h3>Wetterwarner Debug Info</h3>';
				$debug .= wetterwarner_debug_info($instance, $options);
			}
			/*	Widget Ausgabe	*/
				if(isset($output)){
					$output .= $args['after_widget'];
					echo $output;
				}
				if(isset($debug)){
					echo $debug;
				}
			}
			/* Abfangen von Fehlern */
		catch( Exception $e) {
			if(empty($title))
			$title = "Wetterwarner";
			$output  = $args['before_widget'];
			$output .=  $before_title . $title . $after_title;
			$output .=  '<p style="color:red; font-weight:bold">Leider ist etwas schief gelaufen.</p>'.$e->getMessage()."\n";
			$output .=  $args['after_widget'];
			echo $output;
			}
			}
		}
    }
/* Widget  registrieren*/
	add_action('widgets_init', create_function('', 'return register_widget("Wetterwarner_Widget");'));
	add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wetterwarner_action_links' );
?>