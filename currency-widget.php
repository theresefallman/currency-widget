<?php
/*
	Plugin Name: Currency Widget
	Plugin URI: https://github.com/tess-andersson/currency-widget
	Description: A widget-based plugin that displays currencies for a date; including min, max and average. This plugin uses CurrencyScraper: http://code.google.com/p/currencyscraper/ by ?
	Version: 1.0
	Author: Therese Andersson
	License: GPL2

	***************************************************************************
 	Copyright 2012  Therese Andersson  (email : hello@thereseandersson.me)

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

require_once("currency-widget-show.php");

$currencyWidget = new CurrencyWidget();

class CurrencyWidget {
	
	public function __construct() {
		add_action('widgets_init', array(&$this, 'register_widgets') );
		add_action('wp_enqueue_scripts', array(&$this, 'front_scripts') );
	}
	
	// Register currency widget
	public function register_widgets() {
		register_widget('Currency_Widget');
	}

	// Register and enqueue some style-scripts
	public function front_scripts() {
		wp_register_style('currency_widget_style', plugins_url('currency-widget.css', __FILE__));
		wp_enqueue_style('currency_widget_style');
	}
}


?>
