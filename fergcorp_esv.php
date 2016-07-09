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

Class Fergcorp_ESV{
	
	
	add_shortcode('esv', array ( &$this, 'shortcode_esv' ) );
	
	/**
	 * Processes [esv passage="text"] shortcode
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 0.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return Bible passage
	*/
	function shortcode_showTimer($atts) {
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
		if FALSE === $passage{
			return;
		}
	}
}


?>