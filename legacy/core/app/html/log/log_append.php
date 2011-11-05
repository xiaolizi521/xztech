<?
$PAGE_LOAD_TIME_time = microtime();
$PAGE_LOAD_TIME_time = explode(" ", $PAGE_LOAD_TIME_time);
$PAGE_LOAD_TIME_time = $PAGE_LOAD_TIME_time[1] + $PAGE_LOAD_TIME_time[0];
$PAGE_LOAD_TIME_finish = $PAGE_LOAD_TIME_time;

$PAGE_LOAD_TIME_load_time = ($PAGE_LOAD_TIME_finish - $PAGE_LOAD_TIME_start);
$PAGE_LOAD_TIME_date = date("D M  j H:i:s Y");
$PAGE_LOAD_TIME_url = $_SERVER["REQUEST_URI"];
$PAGE_LOAD_TIME_url = explode("?",$PAGE_LOAD_TIME_url,2);
$PAGE_LOAD_TIME_data = "";
if (!empty($PAGE_LOAD_TIME_url[1])) {
  $PAGE_LOAD_TIME_data = $PAGE_LOAD_TIME_url[1]; 
}
$PAGE_LOAD_TIME_url = $PAGE_LOAD_TIME_url[0];
$PAGE_LOAD_TIME_ip = $_SERVER["REMOTE_ADDR"];
$PAGE_LOAD_TIME_port = $_SERVER["REMOTE_PORT"];
$PAGE_LOAD_TIME_string = sprintf("[%s] [%s:%s] PROFILER php:page (%f) '%s' (%s)",
				 $PAGE_LOAD_TIME_date,
				 $PAGE_LOAD_TIME_ip,
				 $PAGE_LOAD_TIME_port,
				 $PAGE_LOAD_TIME_load_time,
				 $PAGE_LOAD_TIME_url,
				 $PAGE_LOAD_TIME_data);
error_log($PAGE_LOAD_TIME_string);
?>

