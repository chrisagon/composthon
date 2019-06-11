<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: "Lato", sans-serif;
}

.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 50px;
  left: 0;
  background-color: #f7f6f6;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 100px;
}

.sidenav a {
  padding: 5px 5px 5px 5px;
  text-decoration: none;
  font-size: 16px;
  color: #000000;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #374bff;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 16px;
  margin-left: 50px;
}

#main {
  transition: margin-left .5s;
  padding: 16px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>
</head>
<body>

<div id="mySidenav" class="sidenav hidden-xs">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><h2><small>Fermer Menu &times;</small></h2></a>
			
				<ul class="nav navbar-nav hidden-xs scrolling="no"">
					<?php if(!$home_page){ ?>
						<?php echo NavMenus(); ?>
					<?php } ?>
				</ul>
</div>

<div id="main">
<!-- RON: Don't add a closing div to above to allow all page content to move to side to accommodate
the menu when visible. If you don't want content to move accross for menu, delete above <div id="main" or add </div> --> 



<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}
</script>