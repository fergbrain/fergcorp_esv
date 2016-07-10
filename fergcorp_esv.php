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
	private $ESV_KEY		= "IP";
	private $NO_RESULT		= "ERROR: No results were found for your search.";
	
	/**
	 * Default construct to initialize settings required no matter what
	 *
	 * @since 0.1
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function __construct(){
		add_shortcode('esv', array ( &$this, 'shortcode_esv' ) );
		add_action( 'admin_print_footer_scripts', array(&$this, 'add_quicktags' ));
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
								'include_passage_references' => TRUE,
								'copyright' => 'long'
								
								),
								$atts));
		return $this->get_passage($passage, $include_passage_references, $copyright);
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
	function get_passage($passage, $include_passage_references, $copyright){
		if (FALSE === $passage) {
			return;
		}
		
		$get_passage = $this->query_source($this->build_query($passage, $include_passage_references, $copyright), "passageQuery");
		if($get_passage != -1){
			return $get_passage;
		}
		else{
			return $passage;
		}
	}
	
	
	function build_query($passage, $include_passage_references, $copyright){
		
		if($copyright != "short"){
			$include_copyright = true;
			$include_short_copyright = false;
		}
		else{
			$include_copyright = false;
			$include_short_copyright = true;
		}
		
		$query = array(	'key'=>$this->ESV_KEY,
						'passage'=>$passage,
						'include-passage-references' => $include_passage_references,
						'include-audio-link'=>'false',
						'include-copyright'=> $include_copyright,
						'include-short-copyright'=> $include_short_copyright
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
		
		if($body == $this->NO_RESULT)
			return -1;
		else{
			return $body;
		}
		
		
	}
	
	// add more buttons to the html editor
	function add_quicktags() {
	    if (wp_script_is('quicktags')){
	?>
	    <script type="text/javascript">
		// http://wordpress.stackexchange.com/questions/41422/functional-quicktag
		// https://gist.github.com/zoerooney/755226cc8271f468037e
		// Add a button called 'abbr' with a callback function
		    QTags.addButton( 'esv', 'ESV', esv_prompt );
		    // and this is the callback function
		    function esv_prompt(e, c, ed) {
		        var prmt, t = this;
		        if ( ed.canvas.selectionStart !== ed.canvas.selectionEnd ) {
		            // if we have a selection in the editor define out tagStart and tagEnd to wrap around the text
		            // prompt the user for the abbreviation and return gracefully on a null input
		            selection = ed.canvas.value.substring  (ed.canvas.selectionStart, ed.canvas.selectionEnd);
		            if ( selection === null ) return;
		            t.tagStart = '[esv passage="';
		            t.tagEnd = '"]';
		        } else if ( ed.openTags ) {
		            // if we have an open tag, see if it's ours
		            var ret = false, i = 0, t = this;
		            while ( i < ed.openTags.length ) {
		                ret = ed.openTags[i] == t.id ? i : false;
		                i ++;
		            }
		            if ( ret === false ) {
		                // if the open tags don't include 'abbr' prompt for input
		                prmt = prompt('Enter Passage (e.g. Luke 1:15-20)');
		                if ( prmt === null ) return;
		            	t.tagStart = '[esv passage="' + prmt + '"]';
		                t.tagEnd = false;
		                if ( ! ed.openTags ) {
		                    ed.openTags = [];
		                }
		                ed.openTags.push(t.id);
		                e.value = '/' + e.value;
		            } else {
		                // otherwise close the 'abbr' tag
		                ed.openTags.splice(ret, 1);
		                t.tagStart = '</abbr>';
		                e.value = t.display;
		            }
		        } else {
		            // last resort, no selection and no open tags
		            // so prompt for input and just open the tag
		            prmt = prompt('Enter Passage (e.g. Luke 1:15-20)');
		            if ( prmt === null ) return;
	            	t.tagStart = '[esv passage="' + prmt + '"]';
	                t.tagEnd = false;
		            if ( ! ed.openTags ) {
		                ed.openTags = [];
		            }
		            ed.openTags.push(t.id);
		            e.value = '/' + e.value;
		        }
		        // now we've defined all the tagStart, tagEnd and openTags we process it all to the active window
		        QTags.TagButton.prototype.callback.call(t, e, c, ed);
		    };
		</script>
	<?php
	    }
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