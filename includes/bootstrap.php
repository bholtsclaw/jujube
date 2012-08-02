<?php
require_once('config.php');
define('APP_ROOT', rtrim(getcwd(),'/'));
define('INCLUDE_ROOT', APP_ROOT . "/includes");
define('LIB_ROOT', INCLUDE_ROOT . "/lib");
define('TEMPLATE_ROOT', INCLUDE_ROOT . "/templates");

require_once(LIB_ROOT . "/jujube/__init__.php");
