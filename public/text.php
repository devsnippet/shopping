<?php
$str    = 'a:7:{s:8:"latitude";s:18:"22.514211744497086";s:9:"longitude";s:17:"113.8773192155331";s:8:"altitude";s:17:"19.39142417907715";s:8:"accuracy";s:4:"1414";s:16:"altitudeAccuracy";s:17:"20.98927469208046";s:7:"heading";s:0:"";s:5:"speed";s:0:"";}';
$string = unserialize($str);

print_r($string);
