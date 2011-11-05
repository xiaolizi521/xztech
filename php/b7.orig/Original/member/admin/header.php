<style type="text/css">
a:link { 
text-decoration: none; 
font-family: Verdana; 
font-size: 11px; 
color: #000000  
}

a:active { 
text-decoration: none; 
font-family: Verdana; 
font-size: 11px; 
color: #000000 
}

a:visited { 
text-decoration: none; 
font-family: Verdana; 
font-size: 11px; 
color: #000000 
}

a:hover { 
text-decoration: underline; 
font-family: Verdana; 
font-size: 11px; 
color: #000000 
}

.main { 
text-decoration: none; 
font-family: Verdana; 
font-size: 11px; 
color: #000000 
}

.form { 
text-decoration: none; 
font-family: Verdana; 
font-size: 10px; 
color: #000000 
}

.secondary { 
text-decoration: none; 
font-family: Verdana, Tahoma; 
font-size: 11px; 
color: #000000 
}

.secondary a:link { 
text-decoration: none; 
font-family: Verdana, Tahoma; 
font-size: 11px; 
color: #000000 
}

.secondary a:active { 
text-decoration: none; 
font-family: Verdana, Tahoma; 
font-size: 11px; 
color: #000000 
}

.secondary a:visited { 
text-decoration: none; 
font-family: Verdana, Tahoma; 
font-size: 11px; 
color: #000000 
}

.secondary a:hover { 
text-decoration: underline; 
font-family: Verdana, Tahoma; 
font-size: 11px; 
color: #000000 
}
</style>

<script>
function DeleteNews( URL ) {
if (confirm('Are you sure you want to delete this news post?')) {
document.location=URL
return true;
} else {
return false;
}
}

function DeleteMessage( URL ) {
if (confirm('Are you sure you want to delete this message post?')) {
document.location=URL
return true;
} else {
return false;
}
}

function DeleteDonator( URL ) {
if (confirm('Are you sure you want to delete this donator?')) {
document.location=URL
return true;
} else {
return false;
}
}

function DeleteMember( username ) {
if (confirm('Are you sure you want to permanetly delete member: '+username+'?')) {
document.form_member.submit();
return true;
} else {
return false;
}
}

function InsertSmile( expression ) {
document.form_news.news.value += ':'+expression+' ';
}
</script>

<?php
$nid = time();
$file_categories = array (
"main" => "Main",
"info" => "Information",
"media" => "Media",
);
?>


