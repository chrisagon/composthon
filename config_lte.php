<?php
//change to FALSE if you want back to appgini default
$LTE_globals =[
    "app-title-prefix" => "Composthon | ", //window bar prfix title or browser tab
    "logo-mini" => "glyphicon glyphicon-home", //mini logo for sidebar mini 50x50 pixels
    "logo-mini-text" => "Composthon", // text for side bar
    "navbar-text" => "Les Brickodeurs",
    "footer-left-text" => "<strong>LTE © ". date("Y") ." <a href=\"#\">LTE Admin</a>.</strong>",
    "footer-right-text" => "Ville de St-Gratien"
];

 //changue this for tablename icon
 $ico_menu = '{
    "Orders":"fa fa-table",
    "Gift":"fa fa-gift",
    "Pencil":"fa fa-pencil-square-o",
    "Cog":"fa fa-cog",
    "Plus":"fa fa-plus",
    "slash":"fa fa-eye-slash"
}';

function getLteStatus($LTE_enable = true){
    if(!function_exists('getMemberInfo')){
        $LTE_enable = false;
    } 
    return $LTE_enable ;
}
