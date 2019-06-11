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

<h1>Guest Menu Info Page</h1>

<h3>Page & Info Access</h3>

<big>Anonymous users will land here if they select the active top menu item once the landing page has been loaded.</big>
<br>
<big>Anonymous users can return to landing page by selecting the Navbar home icon.</big>



<hr>

<?php	
	include_once("$hooks_dir/../footer.php");
?>
<!-- Ron: Important! DO NOT DELETE BELOW, YOU MUST NOT SHOW NAV IN THIS PAGE OTHERWISE WHEN IT LOADS IN IFRAME IT WILL 
CAUSE LOOP BY LOADING PAGE IN PAGE ETC. ALSO NO POINT IN SHOWING FOOTER EITHER-->
<style>nav {display:none;}  body {margin-top:-60px !important;}
</style>
<style>footer {display:none;}</style>
