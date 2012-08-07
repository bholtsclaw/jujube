<?php

$twig->registerUndefinedFunctionCallback(function ($name) {
    if (function_exists($name)) {
        return new Twig_Function_Function($name);
    }

    return false;
});
