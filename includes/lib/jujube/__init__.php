<?php
require_once(LIB_ROOT . "/codeguy/Slim/Slim.php");
require_once(LIB_ROOT . "/Twig/lib/Twig/Autoloader.php");
Twig_Autoloader::register();

require_once(LIB_ROOT . "/jujube/controlers.php");
require_once(LIB_ROOT . "/jujube/routes.php");

function e($inp)
{
    if(is_array($inp))

        return array_map(__METHOD__, $inp);

    if (!empty($inp) && is_string($inp)) {
        return str_replace(array("\n", "\r", "'", '"'), array('', '', '', ''), $inp);
    }

    return $inp;
}
