=currencyScraper=
 Detta API skrapar valutakurser från www.riksbanken.se. Genom att skicka in år och månad får man tillbaka en array med JSON objekt med information om år, månad, valuta, min/max/medel växlingskurs.


==Information==
===Användningsområde?===
 Ett API skrivet i PHP som erbjuder möjligheter att skrapa och hämta information om växlingskurser för valutor. Det finns stöd för ett 30-tal valutor. Information hämtas från www.riksbanken.se som i sin tur sammanställer information utifrån storbankernas olika kurser.

----

===Vilken funktionalitet finns?===
 Hämta växlingskurser, medelkurs, maxkurs samt minstakurs. Information gäller för en kurs, för en månad alternativt tre månader.

----

===Vilka krav ställs?===
 API'et är testat och utvecklat i PHP 5.4.6

----

===Vilka andra API:er används/beroenden finns?===
 cURL används för att hämta data från www.riksbanken.se.

----

===Vilka metoder?===
 Det finns tre publika metoder:
 * get_exchange_rate_1month($valuta, $år, $månad)
 Den här metoden tar in tre parametrar, valutakod(sträng), år(sträng eller int), datum(sträng eller int).
 * get_exchange_rate_3months($valuta, $år, $månad)
 Den här metoden tar in tre parametrar, valutakod(sträng), år(sträng eller int), startdatum(sträng eller int)

 Metoderna retunerar en array med ett eller flera JSON objekt

 Den tredje metoden:
 * get_valid_currencies()
 Den här metoden returnerar en array som innehåller de valutakoder som är gilltiga att använda i applikationen.

----

===Vad skall man kunna styra? Vilka argument skickar man med?===
 I dagsläget kan man styra vilken valuta man vill ha samt vilket år och månad/3månads period man vill ha information om. Valutan bestämms av en valutakod på 3bokstäver medan år bestämms genom ett nummer (xxxx) och månad också genom siffror (xx).

----

===Hur gör man och vad får man som svar?===
 För att kunna använda måste inkludera följande kod och instansiera ett objekt av klassen


require_once ('currencyScraper.php');
$currencyScraper = new currencyScraper();


----


$currencyCodes = $currencyScraper -> get_valid_currencies();


 Vid ett korrekt anrop får man följande svar:


array(30) {
           ["AUD"]=> string(18) "Austrailian Dollar" 
           ["BRL"]=> string(11) "Brazil Real" 
           ["CAD"]=> string(15) "Canadian Dollar" 
           ["CHF"]=> string(11) "Swiss Franc" 
           ["CNY"]=> string(21) "Chinese Yuan Renminbi" 
           ["CZK"]=> string(12) "Czech Koruna" 
           ["DKK"]=> string(12) "Danish Krone" 
           ["EUR"]=> string(4) "Euro" 
           ["GBP"]=> string(13) "British Pound" 
           ["HKD"]=> string(16) "Hong Kong Dollar" 
           ["HUF"]=> string(16) "Hungarian Forint" 
           ["IDR"]=> string(17) "Indonesian Rupiah" 
           ["INR"]=> string(12) "Indian Rupee" 
           ["ISK"]=> string(15) "Icelandic Krona" 
           ["JPY"]=> string(12) "Japanese Yen" 
           ["KRW"]=> string(16) "South Korean Won" 
           ["LTL"]=> string(16) "Lithuanian Litas" 
           ["LVL"]=> string(11) "Latvian Lat" 
           ["MAD"]=> string(15) "Moroccan Dirham" 
           ["MXN"]=> string(12) "Mexican Peso" 
           ["NOK"]=> string(15) "Norwegian Krone" 
           ["NZD"]=> string(18) "New Zeeland Dollar" 
           ["PLN"]=> string(12) "Polish Zloty" 
           ["RUB"]=> string(13) "Russian Ruble" 
           ["SAR"]=> string(19) "Saudi Arabian Riyal" 
           ["SGD"]=> string(16) "Singapore Dollar" 
           ["THB"]=> string(9) "Thai Baht" 
           ["TRY"]=> string(12) "Turkish Lira" 
           ["USD"]=> string(10) "U.S Dollar" 
           ["ZAR"]=> string(18) "South African Rand" 
}


----


$currencyRate = $currencyScraper -> get_exchange_rate_1month("aud", 2011, 10);


 Vid en korrekt förfrågan får man tillbaka en array med JSON objekt:


array(1) { [0]=> string(103) "{
                               "Year":2010,
                               "Month":"November",
                               "Unit":"1",
                               "Currency":"AUD",
                               "Avg":"6.7464",
                               "Min":"6.575",
                               "Max":"6.835"
                              }" 
}


----

 Metoden get_exchange_rate_3months:


$currencyRates = $currencyScraper -> get_exchange_rate_3months("aud", 2011, 10);


 Vid en korrekt förfrågan får man tillbaka en array med JSON objekt:


array(3) { 
[0]=> string(103) "{
                    "Year":2010,
                    "Month":"November",
                    "Unit":"1",
                    "Currency":"AUD",
                    "Avg":"6.7464",
                    "Min":"6.575",
                    "Max":"6.835"
                   }" 
[1]=> string(105) "{
                    "Year":2010,
                    "Month":"December",
                    "Unit":"1",
                    "Currency":"AUD",
                    "Avg":"6.7995",
                    "Min":"6.6975",
                    "Max":"6.9375"
                   }" 
[2]=> string(103) "{
                    "Year":2011,
                    "Month":"January",
                    "Unit":"1",
                    "Currency":"AUD",
                    "Avg":"6.6469",
                    "Min":"6.4075",
                    "Max":"6.875"
                   }" 
}


----

===Hur får man tillgång till API:et?=== 
 API:et finns publikt på: http://code.google.com/p/currencyscraper

----

===Hur sker felhantering?===
 Felhantering sker genom exceptions, som kastas när något går fel.