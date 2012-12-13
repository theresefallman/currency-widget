<?php
require_once (ABSPATH . WPINC . '/widgets.php');
require_once("currencyScraper-api/currencyScraper.php");

// Overrides WP_Widget (WP_Widget::widget, WP_Widget::form, WP_Widget::update)
class Currency_Widget extends WP_Widget {

	private $_default = array();
	private $_months = array();
	private $_api = null;
	
	public function __construct() {
		parent::__construct (
			"currency_widget",
			"Currency Widget",
			array( "description" => __( "Visar valutakurser från Riksbanken.", currency_widget ), )
		);
		
		// Default configuration (Currency for euro at todays date)
		$defMonth = date("n");
		$defYear = date("Y");
		$this->_default = array(
			"title" => "Valutakurser", 
			"year" => $defYear,
			"month" => $defMonth,
			"code" => "eur"
		);

		$this->_api = new currencyScraper();
	}

	/**
	*	Frontend for plugin
	*	@param array: $args Basic config.
	*	@param array: $instance Settings for specific instance
	*/
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
	
		echo $before_widget;

		$title = empty( $instance['title']) ? '' : apply_filters('widget_title', $instance['title'] );

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		// Gets currencies based on widgetinstance
		$result = $this->_getCurrencies( $instance );
		$year = $result["Year"];
		$month = $result["Month"];
		$curr = $result["Currency"];
		$avg = $result["Avg"];
		$min = $result["Min"];
		$max = $result["Max"];
	
		echo "<div class='currency-widget-inner'>";

		if ( empty( $result ) ) {
			echo "No currencies available.";
		} else {
			echo "<ul>
					<li class='currency-title'>$curr ($month, $year)</strong></li>
					<li>Average: $avg</li>
					<li>Minimum: $min</li>
					<li>Maximum: $max</li>
				</ul>";
		}
	
		echo "</div>";

		echo $after_widget;
	}

	/** 
	* 	Updates instance
	*	@param array: $new New values in instance
	*	@param array: $old Previously saved values in instance
	*	@return array: $instance Values to be saved
	*/
	public function update( $new, $old ) {

		$instance = $old;

		$instance["title"] = strip_tags( $new["title"] );

		if ( ! is_numeric( $new["year"] ) || $new["year"] > 2012 ) {
			$instance["year"] = $this->_default["year"];
		} else {
			$instance["year"] = $new["year"];
		}

		if ( ! is_numeric( $new["month"] ) ) {
			$instance["month"] = $this->_default["month"];
		} else {
			$instance["month"] = $new["month"];
		}

		$instance["code"] = $new["code"];

		return $instance;

	}
	

	/** 
	* 	Backend for plugin; Presents form
	*	@param array: $instance Settings for specific instance
	*/
	public function form( $instance ) {
	
		$instance = wp_parse_args( (array) $instance, $this->_default );
		$validCurrencies = $this->_getValidCodes();
	
		// Sets field for title
		$field = "title";
		$label = __( "Title:", "currency_widget" );
		$field_id = $this->get_field_id( $field );
		$field_name = $this->get_field_name( $field );
		$value = esc_attr( $instance[ $field ] );

		echo "<p> <label for='$field_id'>$label</label>" .
				"<input id='$field_id' type='text' name='$field_name' value='$value' class='widefat' /></p>";

		// Sets field for year
		$field = "year";
		$label = __( "Year:", "currency_widget" );
		$field_id = $this->get_field_id( $field) ;
		$field_name = $this->get_field_name( $field );
		$value = esc_attr( $instance[ $field ] );

		echo "<p> <label for='$field_id'>$label</label>" .
				"<input id='$field_id' type='text' name='$field_name' value='$value' class='widefat' /></p>";

		// Sets field for month
		$field = "month";
		$label = __( "Month:", "currency_widget" );
		$field_id = $this->get_field_id( $field );
		$field_name = $this->get_field_name( $field) ;
		$value = esc_attr( $instance[ $field ] );

		echo "<p><label for='{$field_id}'>{$label}</label><br />" .
				"<select id='{$field_id}' name='{$field_name}'>";

			foreach ( $this->_getMonths() as $key => $val ) {
				$select = ( $instance[ $field ] == $key ) ? "selected='selected'" : '';
				echo "<option value='{$key}' {$select}>{$val}</option>";
			}

		echo "</select></p>";
	
		// Sets field for currency code
		$field = "code";
		$label = __( "Choose currency:", "currency_widget" );
		$field_id = $this->get_field_id( $field );
		$field_name = $this->get_field_name( $field );
		$value = esc_attr( $instance[ $field ] );

		echo "<p> <label for='{$field_id}'>{$label}</label> <br />" .
				"<select id='{$field_id}' name='{$field_name}'>";

			foreach ( $validCurrencies as $key => $val ) {
				$select = ( $instance[ $field ] == $key ) ? "selected='selected'" : '';
				echo "<option value='{$key}' {$select}>{$val}</option>";
			}

		echo "</select></p>";

	}

	/** 
	* 	Gets currencies from CurrencyScraper-api
	*	@param array: $options Options including year, month and code
	*	@return array
	*/
	private function _getCurrencies( $options ) {
		$year = $options["year"];
		$month = $options["month"];
		$code = $options["code"];

		$result = $this->_api->get_exchange_rate_1month( $code, $year, $month );
		$currencies = $result[0];
		return json_decode( $currencies, true );
	}

	/** 
	* 	Gets valid currencycodes from CurrencyScraper
	*	@return array
	*/
	private function _getValidCodes() {
		return $this->_api->get_valid_currencies();
	}

	/** 
	* 	Holds valid months
	*	@return array
	*/
	private function _getMonths() {
		$this->_months = array(
			1 => "January",
			2 => "February",
			3 => "March",
			4 => "April",
			5 => "May",
			6 => "June",
			7 => "July",
			8 => "August",
			9 => "September",
			10 => "October",
			11 => "November",
			12 => "December"
		);

		return $this->_months;
	}

}

?>