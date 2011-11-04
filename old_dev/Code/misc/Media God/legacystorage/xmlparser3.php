<?php
require_once('rss/rss_fetch.inc');
define('MAGPIE_CACHE_DIR', '/tmp/magpie_cache');
define('MAGPIE_CACHE_AGE', 60 * 10);

$replace = array();

$rss = fetch_rss("http://pulse.offbeat-zero.net/sigs.xml");

foreach ($rss->items as $item) {
  $user = $item['username'];
  
  echo $user . '<br /';
}

?>
