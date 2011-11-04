<html>

<head>
<style type='text/css'>
.hide {
display:none;
}
</style>
<script>
function over(id,logo) {
document.getElementById(id).style.display = "block";
document.getElementById(logo).src = "img/batch.png";

}
function out(id,logo) {
document.getElementById(id).style.display = "none";
document.getElementById(logo).src = "img/banana.png";
}
</script>
</head>
<body>
<table cellspacing='0' cellspacing='0'>
<tr><td id='1'><img onmouseover="over('1a','1l')" onmouseout="out('1a','1l')" src="img/banana.png" id='1l'></td></tr>
<tr><td id='1a' class='hide'>Stuff 1<br>Stuff2<br>Stuff3<br></td></tr>
</table>
</body>
</html>