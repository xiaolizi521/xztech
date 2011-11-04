<?php
    include('CORE_app.php');
?>
<html>
<?php
    require_once("tools_body.php");
?>
<h1>Disabling Firebug on CORE</h1>
<img src="/images/disable-firebug.png" align="right">
<p><a href="https://addons.mozilla.org/en-US/firefox/addon/1843">Firebug</a> is a Firefox extension that provides many useful tools for developing and debugging web pages.  It's a very helpful extension that we use extensively ourselves.</p>
<p>However, as we move CORE toward a richer user interface including JavaScript and especially AJAX, we are receiving reports of performance problems associated with Firebug.  In general, debuggers negatively impact performance, and there's not really any way to work around this.</p>
<p>If you have Firebug installed, we suggest disabling it for the "core.rackspace.com" domain.  You can accomplish this by right-clicking on the Firebug icon in the Firefox status bar while on a CORE page (it will appear as a green check mark or red X toward the bottom-right corner of the Firefox window), then selecting "Disable Firebug for core.rackspace.com" (as depicted).  You may need to restart Firefox to gain the full benefit from this.</p>
<br>
<p>Thanks!</p>
<?php
    print page_stop();
?>
</html>

