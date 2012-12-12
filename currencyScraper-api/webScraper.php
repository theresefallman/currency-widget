<?php
class webScraper {
	var $ch;
	/// going to used to hold our cURL instance
	var $html;
	/// used to hold resultant html data
	var $binary;
	/// used for binary transfers
	var $url;
	/// used to hold the url to be downloaded

	function wScraper() {
		$this -> html = "";
		$this -> binary = 0;
		$this -> url = "";
	}

	function fetchPage($url) {
		$this -> url = $url;
		try {
			$this -> ch = curl_init();
			/// open a cURL instance
			curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, 1);
			// tell cURL to return the data
			curl_setopt($this -> ch, CURLOPT_URL, $this -> url);
			/// set the URL to download
			curl_setopt($this -> ch, CURLOPT_FOLLOWLOCATION, true);
			/// Follow any redirects
			curl_setopt($this -> ch, CURLOPT_BINARYTRANSFER, $this -> binary);
			/// tells cURL if the data is binary data or not
			$this -> html = curl_exec($this -> ch);
			// pulls the webpage from the internet
			curl_close($this -> ch);
			/// closes the connection
		} catch(Exception $e) {
			exit('Connection to remote service failed : ' . $e);
		}
	}

}
?>
