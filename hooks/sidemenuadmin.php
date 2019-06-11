<?php
	define('PREPEND_PATH', '../');
	$hooks_dir = dirname(__FILE__);
	include("$hooks_dir/../defaultLang.php");
	include("$hooks_dir/../language.php");
	include("$hooks_dir/../lib.php");

	include_once("$hooks_dir/../header.php");
	
	/* grant access to the groups 'Admins' */
	$mi = getMemberInfo();
	if(!in_array($mi['group'], array('Admins'))){
		echo "Access denied";
		exit;
	}
?>

<!-- Ron: This </div> is to force full page width instead of normal layout -->
</div>

<!-- Ron: For button to jump to top of page when scrolling down on iframe content -->
<style>

#myBtn {
  display: none;
  position: fixed;
  top: 60px;
  right: 5px;
  z-index: 99;
  font-size: 20px;
  border: none;
  outline: none;
  background-color: red;
  color: white;
  cursor: pointer;
  padding: 10px;
  border-radius: 4px;
}

#myBtn:hover {
  background-color: #555;
}

</style>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<script>
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
    document.getElementById("myBtn").style.display = "block";
  } else {
    document.getElementById("myBtn").style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>


<!-- Ron: Settings for side bar, menu area -->
<style>
body {
  margin: 0;
  font-family: "Lato", sans-serif;
}

.sidebar {
  margin: 0;
  padding: 0;
  width: 200px;
  background-color: #fffff;
  position: fixed;
  height: 100%;
  overflow: auto;
}

.sidebar a {
  display: block;
  color: #333333;
  padding: 16px;
  text-decoration: none;
}
 
.sidebar a.active {
  background-color: red;
  color: white;
}

.sidebar a:hover:not(.active) {
  background-color: #555;
  color: white;
}

div.content {
  margin-left: 200px;
  padding: 1px 16px;
  height: 1000px;
}

@media screen and (max-width: 700px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .sidebar a {float: left;}
  div.content {margin-left: 0;}
}

@media screen and (max-width: 400px) {
  .sidebar a {
    text-align: center;
    float: none;
  }
}


.dropdown .dropbtn {
  font-size: 20px;  
  border: none;
  outline: none;
  color: white;
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}



.dropdown-content {
  display: none;
  position: sticky;
  width: 100%;
  background-color: #ffc4c4;
  min-width: 200px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  float: none;
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #ddd;
}

.dropdown:hover .dropdown-content {
  display: block;
}

</style>

<body>
<!-- Ron: Selecting the menu item will show the corresponding link in the iframe. Set an alternative for the first "active" menu item such as a note, intro text page etc. etc. -->
<!-- Experiment with any vertical menu type/styles (https://www.w3schools.com/howto/howto_css_fixed_sidebar.asp) and use the iframe method as below to show content / AG tables, pages etc. -->
<div class="sidebar">

  <a class="active" href="/sidemenu/hooks/guestinfo.php" target="iframe_a">ADMIN MENU</a>
  <a href="/sidemenu/hooks/landingpage.php" target="iframe_a">A Main Page</a>
  <div class="dropdown">
    <a href="#">Add a submenu 
      <i class="fa fa-caret-down"></i></a>
     <div class="dropdown-content">
      <a href="/sidemenu/table1_view.php" target="iframe_a">Table Example 1</a>
      <a href="/sidemenu/table2_view.php" target="iframe_a">Table Example 2</a>
	  <a href="/sidemenu/movies_view.php" target="iframe_a">Movies List</a>
	  </div>
  </div> 
  <a href="/sidemenu/linkpage_view.php" target="iframe_a">Link Page</a>
  <a href="/sidemenu/table1_view.php" target="iframe_a">Table Example 1</a>
  <a href="/sidemenu/table2_view.php" target="iframe_a">Table Example 2</a>
  <a href="/sidemenu/movies_view.php" target="iframe_a">Movies List</a>
  <div class="dropdown">
    <a href="/sidemenu/downloads_view.php" target="iframe_a">Downloads List 
      <i class="fa fa-caret-down"></i></a>
     <div class="dropdown-content">
      <a href="#">Link 1</a>
      <a href="#">Link 2</a>
      <a href="#">Link 3</a>
    </div>
  </div> 
  </div>

<div class="content">

<!-- You can add social icons etc. here to line up with side menu, or use to place a heading, logo, header etc. above the iframe -->
<h1>Web App Title</h1>
<div class="pull-left"
&nbsp;&nbsp;&nbsp;<p><a class="fa fa-youtube fa-2x social-icon" href="/sidemenu/hooks/landingpage.php" target="_blank"></a>&nbsp;
<a class="fa fa-facebook fa-2x social-icon" href="/sidemenu/hooks/landingpage.php" target="_blank"></a>&nbsp;
<a class="fa fa-google fa-2x social-icon" href="/sidemenu/hooks/landingpage.php" target="_blank"></a>&nbsp;</p>
</div>

<!-- Ron When first visiting the page the below src link will be shown in the initially loaded iframe. If the "active" first menu is then selected it will then show the associatted url of that first link.
If you want the same content to show then use the same src for below and the top 'active' iframe link -->

<iframe src="/sidemenu/hooks/landingpage.php" name="iframe_a" style="border: 0" width="100%" height="1950px" frameborder="0" scrolling="no"><p>Your browser does not support iframes.</p></iframe>



</div>

</body>
<?php	
	include_once("$hooks_dir/../footer.php");
?>
<script>
jQuery(document).ready(function(){
jQuery(this).scrollTop(0);
});
</script>