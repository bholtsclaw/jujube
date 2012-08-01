<?php
define('MONGO_HOST','api.websitedevops.com');
define('DEFAULT_SERIES', 'precise');

define('APP_ROOT', rtrim(dirname(__FILE__),'/'));
define('INCLUDE_ROOT', APP_ROOT . "/includes");
define('LIB_ROOT', INCLUDE_ROOT . "/lib");
define('TEMPLATE_ROOT', INCLUDE_ROOT . "/templates");

require_once(LIB_ROOT . "/jujube/__init__.php");




