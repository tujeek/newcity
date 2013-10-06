<?php
header('Content-type: text/html; charset=utf-8');
// лог запросов
function selfURL(){
    if(!isset($_SERVER['REQUEST_URI']))    $suri = $_SERVER['PHP_SELF'];
    else $suri = $_SERVER['REQUEST_URI'];
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp=strtolower($_SERVER["SERVER_PROTOCOL"]);
    $pr =  substr($sp,0,strpos($sp,"/")).$s;
    $pt = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $pr."://".$_SERVER['SERVER_NAME'].$pt.$suri;
}
 $fp = fopen("log.txt", "a"); // Открываем файл в режиме записи 
 $mytext = selfURL()." ; "; // Исходная строка
 $mytext = $mytext."POST: ".serialize($_POST)."\r\n";
 $test = fwrite($fp, $mytext); // Запись в файл
 if (!$test) echo 'Ошибка при записи в файл.';
 fclose($fp); //Закрытие файла
// конец лога
$dbtype = 'mysqli';
$host = 'localhost';
$user = 'metsyscom_admin';
$password = '7723833de';
$db = 'metsyscom_main';
// Попытка установить соединение с MySQL:
if (!mysql_connect($host, $user, $password)) {
echo "Ошибка подключения к серверу MySQL";
exit;
}
// Соединились, теперь выбираем базу данных:
if (!mysql_select_db($db)) {
echo "Ошибка подключения к серверу MySQL";
exit;
}
?>