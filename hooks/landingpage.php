<?php
	define('PREPEND_PATH', '../');
	$hooks_dir = dirname(__FILE__);
	include("$hooks_dir/../defaultLang.php");
	include("$hooks_dir/../language.php");
	include("$hooks_dir/../lib.php");

	include_once("$hooks_dir/../header.php");
	
	/* grant access to the groups 'Admins' and 'anonymous' */
	$mi = getMemberInfo();
	if(!in_array($mi['group'], array('Admins', 'anonymous'))){
		echo "Access denied";
		exit;
	}
?>

<h1>Your Welcome Page</h1>

<h3>This page loads first with the App</h3>

<h4><i class="fa fa-quote-left fa-2x fa-pull-left fa-border" aria-hidden="true"></i> Selecting the top left navbar HOME icon will show this landing page (because of edit made to the <b>incCommon.php file line 341</b> in demo version)<h4>
<br>
<h4>You can have whatever content you want here! Just create and link menu to your created hook file. Be sure to grant view access in admin setting for Anonymous users!</h4>
<hr>
<h4>I have duplicated the guest/anonymous menu under an admin restricted menu to demo a simple way you can have a group menu (i.e. change 'jump to' to 'group' and set permissions on your group sidemenugroup.php hooks file) - log in as admin to see jump to navbar menu. </h4>
<hr>
 <h4>I have made notes in the amended core files highlighting the changes made to them. </h4>
<hr>

<?php	
	include_once("$hooks_dir/../footer.php");
?>
<!-- Ron: Important! DO NOT DELETE BELOW, YOU MUST NOT SHOW NAV IN THIS PAGE OTHERWISE WHEN IT LOADS IN IFRAME IT WILL 
CAUSE LOOP BY LOADING PAGE IN PAGE ETC. ALSO NO POINT IN SHOWING FOOTER EITHER.
Also needs to be added to every created pages hook files under table + detail view headers.-->

<style>nav {display:none;}  body {margin-top:-60px !important;}
</style>
<style>footer {display:none;}</style>
