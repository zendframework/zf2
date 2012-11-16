<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 25.Jul.2011
 */
return array(
    // Zend\Validator\Alnum
    "Invalid type given. String, integer or float expected" => "Ugyldig type angitt. Forventet streng, heltall eller flyt-tall",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' inneholder tegn som ikke er alfabetiske eller sifre",
    "'%value%' is an empty string" => "'%value%' er en tom streng",

    // Zend\Validator\Alpha
    "Invalid type given. String expected" => "Ugyldig type angitt. Forventet streng",
    "'%value%' contains non alphabetic characters" => "'%value%' inneholder ikke-alfabetiske tegn",
    "'%value%' is an empty string" => "'%value%' er en tom streng",

    // Zend\Validator\Barcode
    "'%value%' failed checksum validation" => "'%value%' feilet kontrollsumvalidering",
    "'%value%' contains invalid characters" => "'%value%' inneholder ugyldige tegn",
    "'%value%' should have a length of %length% characters" => "'%value%' må ha en lengde på %length% tegn",
    "Invalid type given. String expected" => "Ugyldig type er angitt. Forventet streng",

    // Zend\Validator\Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' er ikke mellom eller lik '%min%' og '%max%'",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' er ikke utelukkende mellom '%min%' og '%max%'",

    // Zend\Validator\Callback
    "'%value%' is not valid" => "'%value%' er ugyldig",
    "An exception has been raised within the callback" => "Et unntak ble reist i tilbakeringingen",

    // Zend\Validator\Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' må være mellom 13 og 19 siffer",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "Luhn-algoritmen (mod-10 sjekksum) feilet for '%value%'",

    // Zend\Validator\CreditCard
    "'%value%' seems to contain an invalid checksum" => "Det synes som at '%value%' har en ugyldig sjekksum",
    "'%value%' must contain only digits" => "'%value%' kan kun inneholde siffer",
    "Invalid type given. String expected" => "Ugyldig type angitt. Forventet streng",
    "'%value%' contains an invalid amount of digits" => "'%value%' inneholder ugyldig antall sifre",
    "'%value%' is not from an allowed institute" => "'%value%' er ikke fra et tillatt institutt",
    "'%value%' seems to be an invalid creditcard number" => "'%value%' synes å være et ugyldig kredittkortnummer",
    "An exception has been raised while validating '%value%'" => "Et unntak ble reist ved validering av '%value%'",

    // Zend\Validator\Date
    "Invalid type given. String, integer, array or Zend_Date expected" => "Ugyldig type angitt. Forventet streng, heltall, matrise eller Zend_Dat",
    "'%value%' does not appear to be a valid date" => "'%value%' synes ikke å være en gyldig dato",
    "'%value%' does not fit the date format '%format%'" => "'%value%' passer ikke datoformatet '%format%'",

    // Zend\Validator\Db_Abstract
    "No record matching '%value%' was found" => "Ingen poster ble funnet for '%value%'",
    "A record matching '%value%' was found" => "En post ble funnet for '%value%'",

    // Zend\Validator\Digits
    "Invalid type given. String, integer or float expected" => "Ugyldig type angitt. Forventet streng, heltall eller flyt-tall",
    "'%value%' must contain only digits" => "'%value%' kan bare inneholde sifre",
    "'%value%' is an empty string" => "'%value%' er en tom streng",

    // Zend\Validator\EmailAddress
    "Invalid type given. String expected" => "Ugyldig type angitt. Forventet streng",
    "'%value%' is not a valid email address in the basic format local-part@hostname" => "'%value%' er ikke, i det grunnleggende formatet bruker@vertsnavn, en gyldig e-postadresse.",
    "'%hostname%' is not a valid hostname for email address '%value%'" => "'%hostname%' er ikke et gyldig vertsnavn for e-postadressen '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' synes ikke å ha et gyldig MX oppslag for e-postadressen '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network" => "'%hostname%' er ikke i et rutbart nettverks segment. E-postadressen '%value%' kommer ikke fra et offentlig nett",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' stemmer ikke overens med dot-atom formatet",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' stemmer ikke overens med anførselstegn-streng formatet",
    "'%localPart%' is not a valid local part for email address '%value%'" => "'%localPart%' er ikke gyldig som lokal del for e-postadressen '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' overstiger tillatt lengde",

    // Zend\Validator\File\Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "For mange filer, maksimum '%max%' er tillatt, men '%count%' er angitt",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "for få filer, minimum '%min%' er forventet, men '%count%' er angitt",

    // Zend\Validator\File\Crc32
    "File '%value%' does not match the given crc32 hashes" => "Filen '%value%' samsvarer ikke med gitte crc32 hasher",
    "A crc32 hash could not be evaluated for the given file" => "En crc32 hash kunne ikke bli evaluert for den gitte filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\ExcludeExtension
    "File '%value%' has a false extension" => "Feil filtype for filen '%value%'",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en feil mimetype av '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mimetype for filen '%value%' ble ikke funnet",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\Exists
    "File '%value%' does not exist" => "Filen '%value%' finnes ikke",

    // Zend\Validator\File\Extension
    "File '%value%' has a false extension" => "Feil filtype for filen '%value%'",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Total filstørrelse skal ikke overstige '%max%'. Beregnet filstørrelse ('%size%') overstiger dette",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Total filstørrelse skal minimum overstige '%min%'. Beregnet sum er '%size%'",
    "One or more files can not be read" => "En eller flere filer kan ikke leses",

    // Zend\Validator\File\Hash
    "File '%value%' does not match the given hashes" => "Filen '%value%' samsvarer ikke med de gitte hasher",
    "A hash could not be evaluated for the given file" => "En hash kunne ikke bli evaulert for den gitte filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "Maksimal tillatt bredde for bilde '%value%' skulle være '%maxwidth%', men '%width%' ble oppdaget",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "Minimum forventet bredde for bilde '%value%' skulle være '%minwidth%', men '%width%' ble oppdaget",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "Maksimal tillatt høyde for bilde '%value%' skulle være '%maxheight%', men '%height%' ble oppdaget",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "Minimum forventet høyde for bilde '%value%' skulle være '%minheight%', men '%height%' ble oppdaget",
    "The size of image '%value%' could not be detected" => "Størrelsen på bildet '%value%' kunne ikke bli oppdaget",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "Filen '%value%' er ikke komprimert, filtype '%type%' ble funnet",
    "The mimetype of file '%value%' could not be detected" => "Mimetype for filen '%value%' ble ikke oppdaget",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\IsImage
    "File '%value%' is no image, '%type%' detected" => "Filen '%value%' er ikke et bilde, '%type%' ble funnet",
    "The mimetype of file '%value%' could not be detected" => "Mimetype for filen '%value%' fle ikke funnet",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\Md5
    "File '%value%' does not match the given md5 hashes" => "Filen '%value%' er ikke samsvarer med den angitte md5 hashen",
    "A md5 hash could not be evaluated for the given file" => "En md5 hash kunne ikke bli evaluert for den gitte filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\MimeType
    "File '%value%' has a false mimetype of '%type%'" => "Filen '%value%' har en feil mimetype for '%type%'",
    "The mimetype of file '%value%' could not be detected" => "Mimetypen for filen '%value%' ble ikke funnet",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\NotExists
    "File '%value%' exists" => "Filen '%value%' finnes",

    // Zend\Validator\File\Sha1
    "File '%value%' does not match the given sha1 hashes" => "Filen '%value%' samsvarer ikke med den angitte sha1 hashen",
    "A sha1 hash could not be evaluated for the given file" => "En sha1 hash kunne ikke bli evaluert for den gitte filen",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "Maksimal tillatt størrelse for filen '%value%' er '%max%', men '%size%' ble oppdaget",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "Minimum forventet størrelse for filen '%value%' er '%min%', men '%size%' ble oppdaget",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\File\Upload
    "File '%value%' exceeds the defined ini size" => "Filen '%value%' overskrider definert ini størrelse",
    "File '%value%' exceeds the defined form size" => "Filen '%value%' overskrider definert skjema størrelse",
    "File '%value%' was only partially uploaded" => "Filen '%value%' ble bare delvis lastet opp",
    "File '%value%' was not uploaded" => "Filen '%value%' ble ikke lastet opp",
    "No temporary directory was found for file '%value%'" => "Ingen midlertidig mappe ble funnet for filen '%value%'",
    "File '%value%' can't be written" => "Filen '%value%' kan ikke bli skrevet",
    "A PHP extension returned an error while uploading the file '%value%'" => "En PHP utvidelse returnerte en feil under opplasting av filen '%value%'",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "Filen '%value%' ble ulovlig lastet opp. Dette kan være en mulig angrep",
    "File '%value%' was not found" => "Filen '%value%' ble ikke funnet",
    "Unknown error while uploading file '%value%'" => "Ukjent feil under opplasting av filen '%value%'",

    // Zend\Validator\File\WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "For mange ord, maksimum '%max%' er tillatt, men '%count%' ble telt",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "For få ord, minimum '%min%' er forventet, men '%count%' ble telt",
    "File '%value%' is not readable or does not exist" => "Filen '%value%' er ikke lesbar eller finnes ikke",

    // Zend\Validator\Float
    "Invalid type given. String, integer or float expected" => "Ugyldig type gitt. Forvente streng, heltall eller flyt-tall",
    "'%value%' does not appear to be a float" => "'%value%' synes ikke å være flyt-tall",

    // Zend\Validator\GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' er ikke større enn '%min%'",

    // Zend\Validator\Hex
    "Invalid type given. String expected" => "Ugyldig type angitt. Forventet streng",
    "'%value%' has not only hexadecimal digit characters" => "'%value%' har ikke bare heksadesimale siffer tegn",

    // Zend\Validator\Hostname
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' synes å være en IP-adresse, men IP-adresser er ikke tillatt",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' synes å være et DNS-vertsnavn, men kunne ikke matche TLD mot kjent liste",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' synes å være et DNS-vertsnavn, men inneholder en bindestrek i en ugyldig posisjon",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' synes å være et DNS-vertsnavn, men kunne ikke sammenligne mot vertsnavn skjema for TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' synes å være et DNS-vertsnavn, men kan ikke trekke ut TLD del",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' samsvarer ikke med forventet struktur for et DNS vertsnavn",
    "'%value%' does not appear to be a valid local network name" => "'%value%' synes ikke å være et gyldig lokalt nettverksnavn",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' synes å være et lokalt nettverksnavn, men lokale nettverk er ikke tillatt",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' synes å være et DNS-vertsnavn, men den gitte punycode notasjonen ikke kan dekodes",
    "'%value%' does not appear to be a valid URI hostname" => "'%value%' synes ikke å være et gyldig URI vertsnavn",

    // Zend\Validator\Iban
    "Unknown country within the IBAN '%value%'" => "Ukjent land i IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' har feil IBAN-format",
    "'%value%' has failed the IBAN check" => "'%value%' har feilet IBAN-sjekk",

    // Zend\Validator\Identical
    "The two given tokens do not match" => "De to angitte tokenene stemmer ikke overens",
    "No token was provided to match against" => "Ingen token ble angitt for å matche mot",

    // Zend\Validator\InArray
    "'%value%' was not found in the haystack" => "'%value%' ble ikke funnet i høystakken",

    // Zend\Validator\Int
    "Invalid type given. String or integer expected" => "Ugyldig type gitt. Forventet streng eller heltall",
    "'%value%' does not appear to be an integer" => "'%value%' synes ikke å være et heltall",

    // Zend\Validator\Ip
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",
    "'%value%' does not appear to be a valid IP address" => "'%value%' synes ikke å være en gyldig IP-adresse",

    // Zend\Validator\Isbn
    "Invalid type given. String or integer expected" => "Ugyldig type gitt. Forventet streng eller heltall",
    "'%value%' is not a valid ISBN number" => "'%value%' er ikke et gyldig ISBN-nummer",

    // Zend\Validator\LessThan
    "'%value%' is not less than '%max%'" => "'%value%' er ikke mindre enn '%max%'",

    // Zend\Validator\NotEmpty
    "Invalid type given. String, integer, float, boolean or array expected" => "Ugyldig type gitt. Forventet streng, heltall, flyt-tall, boolean eller matrise", 
    "Value is required and can't be empty" => "Verdi er påkrevd, og kan ikke være tomt",

    // Zend\Validator\PostCode
    "Invalid type given. String or integer expected" => "Ugyldig type gitt. Forventet streng eller heltall",
    "'%value%' does not appear to be a postal code" => "'%value%' synes ikke å være et gyldig postnummer",

    // Zend\Validator\Regex
    "Invalid type given. String, integer or float expected" => "Ugyldig type angitt. Forventet streng, heltall eller flyt-tall",
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' stemmer ikke mot mønsteret '%pattern%'",
    "There was an internal error while using the pattern '%pattern%'" => "En intern feil oppsto ved bruk av mønsteret '%pattern%'",

    // Zend\Validator\Sitemap\Changefreq
    "'%value%' is not a valid sitemap changefreq" => "'%value%' er ikke en gyldig changefreq-verdi for sitemap",
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",

    // Zend\Validator\Sitemap\Lastmod
    "'%value%' is not a valid sitemap lastmod" => "'%value%' er ikke en gyldig lastmod-verdi for sitemap",
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",

    // Zend\Validator\Sitemap\Loc
    "'%value%' is not a valid sitemap location" => "'%value%' er ikke en gyldig sitemap sted",
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",

    // Zend\Validator\Sitemap\Priority
    "'%value%' is not a valid sitemap priority" => "'%value%' er ikke en gyldig prioritet-verdi for sitemap",
    "Invalid type given. Numeric string, integer or float expected" => "Ugyldig type angitt. Forventet numerisk streng, heltall eller flyt-tall",

    // Zend\Validator\StringLength
    "Invalid type given. String expected" => "Ugyldig type gitt. Forventet streng",
    "'%value%' is less than %min% characters long" => "'%value%' er mindre enn %min% tegn",
    "'%value%' is more than %max% characters long" => "'%value%' er mer enn %max% tegn",
);
