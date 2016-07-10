<?php
/*
Plugin Name: ESV Bible Passage
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/
Description: Use shortcodes to include links to ESV bible passages
Version: 0.1
Author: Andrew Ferguson
Author URI: http://www.fergcorp.com
ESV Bible Passage - Use shortcodes to include links to ESV bible passages
Copyright (c) 2016 Fergcorp, LLC

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

Class Fergcorp_ESV {
	
	private $ESV_BASE_URL	= "http://www.esvapi.org/v2/rest/";
	private $ESV_KEY			= "IP";
	
	/**
	 * Default construct to initialize settings required no matter what
	 *
	 * @since 0.1
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function __construct(){
		add_shortcode('esv', array ( &$this, 'shortcode_esv' ) );
	}

	/**
	 * Processes [esv passage="text"] shortcode
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 0.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return Bible passage
	*/
	function shortcode_esv($atts) {
		extract(shortcode_atts(array(
								'passage' => FALSE,
								),
								$atts));
		return $this->get_passage($passage);
	}
	
	/**
	 * Processes [esv passage="text"] shortcode
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 0.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return Bible passage
	*/
	function get_passage($passage){
		if (FALSE === $passage) {
			return;
		}
		
		return $this->query_source($this->build_query($passage), "passageQuery");
	}
	
	
	function build_query($passage){
		$query = array(	'key'=>$this->ESV_KEY,
						'passage'=>$passage,
						'include-audio-link'=>'false',
						'include-copyright'=>'true',
						'include-short-copyright'=>'false'
					);
		return http_build_query($query);
		
	}
	
	
	
	function query_source($query, $action){
		
		$url = $this->ESV_BASE_URL . $action . "?"	. $query;
		
		$response = wp_remote_get( $url );
		
		if( is_array($response) ) {
		  $header = $response['headers']; // array of http header lines
		  $body = $response['body']; // use the content
		}
		
		return $body;
		
	}
	
	/*
	protected function getBiblePassage ($query_string = '', $error_message = 'ERROR: Could not retrieve readings') {
			if ($query_string) {
				$this->query_string  = $query_string;
			} else {
				$this->query_string .= '&date='.date('Y-m-d'); // This is most likely never reached in the current version
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$this->plan_source_link&key=$this->access_key&$this->query_string");
			curl_setopt($ch, CURLOPT_VERBOSE, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$txt = trim(curl_exec($ch));
			curl_close($ch);
			parse_str($this->query_string);
			if ($date) {
				list($year, $month, $day) = explode('-', $date);
				$scriptures_date = date($this->date_format, mktime(0, 0, 1, $month, $day, $year));
			} else {
				$scriptures_date = date($this->date_format);
			}
			if ($txt && strpos($txt, 'ERROR') === false) {
				if ('mp3' == $this->audio_format) {
						// Utilize wp_audio_shortcode function to set up the audio tags
						$txt  = preg_replace("|<small class=\"audio\">\(<a href=\"http://([^\"]+)\">Listen</a>\)</small>|m", "<div class=\"ebp_audio_player\">[audio src='http://$1.mp3' type='audio/mpeg']</div>", $txt);
				}
				if ($this->use_calendar) {
					$rtn_str  = '<span class="scriptures-date">'.$scriptures_date.'</span>'.$txt;
				} else {
					$rtn_str  = $txt;
				}
				$rtn_str .= '<div style="font-size: 0.8em; width: 50%; float: left; margin: 0;">'.$this->esv_copyright.'</div>';
				$rtn_str .= '<div style="font-size: 0.8em; width: 50%; float: left; margin: 0; text-align: right;">';
				if ($this->show_poweredby) {
					$rtn_str .= $this->powered_by;
				} else {
					$rtn_str .= '&nbsp;';
				}
				$rtn_str .= '</div>';
			} else {
				$rtn_str = "$error_message for $scriptures_date from <a href=\"http://www.gnpcb.org/esv/share/services/\" target=\"_blank\">Crossway Bibles Web Service</a>.";
			}
			$scriptures_div = '<div id="scriptures">'.$this->loading_image.'</div>';
			if ($query_string) {
				return $rtn_str; // with calendar, and loaded with calendar selection 
			} elseif ($this->use_calendar) {
				return '<div title="'.__('Click on a date to open the readings for that day.').'" id="datepicker"></div>'.$scriptures_div; // with calendar, but loaded without calendar selection
			} else {
				return $scriptures_div; // no calendar
			}
		}
	*/
	
}

if (class_exists('Fergcorp_ESV')) {
	$bible_passage = new Fergcorp_ESV();

}

?>