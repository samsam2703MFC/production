<?php
require_once __DIR__ . '/../../public/vendor/autoload.php'; // Wczytaj autoloader Composera
use Dotenv\Dotenv;


if($_SERVER['SERVER_NAME'] == 'slfood.local')
{
    /** database config **/
    define('DBNAME', 'erp_10');
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');
    define('DBPASS', '');
    define('DBDRIVER', '');
    //define('APIPREFIXE', 'https://franchiseatelier.com/api/public/Production/');
    define('APIPREFIXE', 'http://localhost/Intranet/api/public/Production/');
//    define('APIPREFIXE', 'https://franchiseatelier.com/api/public/Production/');

    define('ATELIER_RESOURCES_PREFIXE', 'http://localhost/Intranet/resources/');

    define('ROOT',
        (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
            ? 'https://'
            : 'http://') . $_SERVER['SERVER_NAME']);

}else
{
    /** database config **/
    define('DBNAME', 'erp');
    define('DBHOST', '192.168.1.150');
    define('DBUSER', 'sam');
    define('DBPASS', 'NhoQyQbKRSPh4Ubg3sR7DMjs5');
    define('DBDRIVER', '');
    define('APIPREFIXE', 'https://franchiseatelier.com/api/public/Production/');
    define('ATELIER_RESOURCES_PREFIXE', 'https://franchiseatelier.com/resources/');
    //define('APIPREFIXE', 'http://localhost/Intranet/api/public/Production/');

    define('ROOT', 'http://192.168.1.150/erp');
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../', null, false);
$dotenv->load();

define('APP_NAME', "My Webiste");
define('APP_DESC', "Best website on the planet");

define('WDT_PRINT_COUNT', 1);
define('DIFF_PRINT_COUNT', 1);
define('WZ_PRINT_COUNT', 3);
define('CMR_PRINT_COUNT', 3);
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', "zł");
}//define('CURRENCY_SYMBOL', "zł");

/** true means show errors **/
define('DEBUG', true);

