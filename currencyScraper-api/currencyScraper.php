<?php
require_once ('webScraper.php');

class currencyScraper {
	const searchUrlStart = "http://www.riksbank.se/sv/Rantor-och-valutakurser/Manadsgenomsnitt-valutakurser/?y=";
	const searchUrlMiddle = "&m=";
	const searhUrlEnd = "&s=Dot#search";

	private $m_year;
	private $m_month;

	/*
	 *  Public function that is responsible to iniate all the other functions before returning a response.
	 *
	 * @access	publiv
	 * @param	string	the currency to look up
	 * @param	string	year to look at
	 * @param	string	month to look at
	 * @return	array
	 */
	public function get_exchange_rate_1month($currencyCode, $year, $month) {
		$year = intval($year);
		$month = intval($month);
		try {
			$this -> validateCurrency($currencyCode);
			try {
				$this -> validateDate($year, $month);
				$this -> m_year = $year;
				$this -> m_month = $month;
				$result = array();
				array_push($result, $this -> webScraper(CurrencyScraper::searchUrlStart . $this -> m_year . CurrencyScraper::searchUrlMiddle . $this -> m_month . CurrencyScraper::searhUrlEnd, $currencyCode));
				return $result;
			} catch(exception $e) {
				echo 'Invalid date format: ' . $e -> getMessage();
			}
		} catch(exception $e) {
			echo 'Invalid currency: ' . $e -> getMessage();
		}
	}

	/*
	 *  Public function that is responsible to iniate all the other functions before returning a response.
	 *
	 * @access	publiv
	 * @param	string	the currencies to look up
	 * @param	string	year to look at
	 * @param	string	month to look at
	 * @return	array
	 */
	public function get_exchange_rate_3months($currencyCode, $year, $startMonth) {
		$year = intval($year);
		$startMonth = intval($startMonth);
		try {
			$this -> validateCurrency($currencyCode);
			try {
				$this -> validateDate($year, $startMonth);
				$this -> m_year = $year;
				$this -> m_month = $startMonth;
				$result = array();
				for ($i = 0; $i < 3; $i++) {
					if ($this -> m_month == 13) {
						$this -> m_month = 01;
						$this -> m_year++;
					}
					array_push($result, $this -> webScraper(CurrencyScraper::searchUrlStart . $this -> m_year . CurrencyScraper::searchUrlMiddle . $this -> m_month . CurrencyScraper::searhUrlEnd, $currencyCode));
					$this -> m_month++;
				}
				return $result;
			} catch(exception $e) {
				echo 'Invalid date format: ' . $e -> getMessage();
			}
		} catch(exception $e) {
			echo 'Invalid currency: ' . $e -> getMessage();
		}

	}

	/*
	 * Function to return an array with valid currencies
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_valid_currencies() {
		$validCurrencies = array("AUD" => "Austrailian Dollar", "BRL" => "Brazil Real", "CAD" => "Canadian Dollar", "CHF" => "Swiss Franc", "CNY" => "Chinese Yuan Renminbi", "CZK" => "Czech Koruna", "DKK" => "Danish Krone", "EUR" => "Euro", "GBP" => "British Pound", "HKD" => "Hong Kong Dollar", "HUF" => "Hungarian Forint", "IDR" => "Indonesian Rupiah", "INR" => "Indian Rupee", "ISK" => "Icelandic Krona", "JPY" => "Japanese Yen", "KRW" => "South Korean Won", "LTL" => "Lithuanian Litas", "LVL" => "Latvian Lat", "MAD" => "Moroccan Dirham", "MXN" => "Mexican Peso", "NOK" => "Norwegian Krone", "NZD" => "New Zeeland Dollar", "PLN" => "Polish Zloty", "RUB" => "Russian Ruble", "SAR" => "Saudi Arabian Riyal", "SGD" => "Singapore Dollar", "THB" => "Thai Baht", "TRY" => "Turkish Lira", "USD" => "U.S Dollar", "ZAR" => "South African Rand");
		return $validCurrencies;
	}

	/*
	 * Function to match the inputed currencycode agains a set of valid currencycodes
	 *
	 * @access	private
	 * @param	string	currency code to validate
	 * @return	bool
	 */
	private function validateCurrency($currencyCode) {
		$currencyCode = strtoupper($currencyCode);
		$validCurrencies = $this -> get_valid_currencies();
		if (array_key_exists($currencyCode, $validCurrencies)) {
			return true;
		} else {
			$tmp = "";
			$validCurrenciesKey = array_keys($validCurrencies);
			for ($i = 0; $i < count($validCurrencies); $i++) {
				if ($i < count($validCurrencies) - 1) {
					$tmp .= $validCurrencies[$validCurrenciesKey[$i]] . ", ";
				} else {
					$tmp .= $validCurrencies[$validCurrenciesKey[$i]];
				}
			}
			throw new Exception("Valid currencies are: " . $tmp);
		}
	}

	/*
	 * Function to validate that the year and date is in a correct format
	 *
	 * @access	private
	 * @param	int		year to validate
	 * @param	int		month to validate
	 * @return	bool
	 */
	private function validateDate($year, $date) {
		if (is_int($year) && $year <= 2012 && is_int($date) && $date < 13 && $year >= 01) {
			return true;
		} else {
			throw new Exception("Invalid format of year(xxxx) or month(xx).");
		}
	}

	/*
	 * Function that iniate the spider to scrape a page
	 *
	 * @access	private
	 * @param	string	the URL to scrape
	 * @param	string	currency to look for
	 * @return	json
	 */
	private function webScraper($url, $currencyCode) {
		$myScraper = new webScraper();
		//// creates a new instance of the wSpider
		$myScraper -> fetchPage($url);
		/// fetches the home page
		$arrayOfCurrencyInfo = $this -> parse_array("<td", "</td>", $myScraper);
		$currency = array('currency' => "");
		for ($i = 0; $i < sizeof($arrayOfCurrencyInfo[0]); $i++) {
			$variableToCheckCurrencyCode = strip_tags($arrayOfCurrencyInfo[0][$i]);
			if ($variableToCheckCurrencyCode == strtoupper($currencyCode)) {
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i - 1]) . " ";
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i]) . " ";
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i + 1]) . " ";
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i + 2]) . " ";
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i + 3]) . " ";
				$currency['currency'] .= strip_tags($arrayOfCurrencyInfo[0][$i + 4]) . " ";
				$i = $i + 5;
				$JSON = $this -> dataToJSON($currency);
			}
		}
		return $JSON;
	}

	/*
	 * Function that extracts data from the scraping into JSON
	 *
	 * @access	private
	 * @param	array	array with currency info to convert
	 * @return	json
	 */
	private function dataToJSON($data) {
		$tmp = $data['currency'];
		$pieces = explode(" ", $tmp);
		$monthName = date("F", mktime(0, 0, 0, $this -> m_month, 10));
		$array = array('Year' => $this -> m_year, 'Month' => $monthName, 'Unit' => $pieces[0], 'Currency' => $pieces[1], 'Avg' => $pieces[2], 'Min' => $pieces[3], 'Max' => $pieces[4]);
		$JSON = json_encode($array);
		return $JSON;
	}

	private function parse_array($beg_tag, $close_tag, $data) {
		preg_match_all("($beg_tag.*$close_tag)siU", $data -> html, $matching_data);
		return $matching_data;
	}

}
?>