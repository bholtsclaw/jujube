<?php
$loader = new Twig_Loader_Filesystem(TEMPLATE_ROOT);
$twig = new Twig_Environment($loader);
$mongo = new Mongo(MONGO_HOST);
$col_charms = $mongo->juju->charms;

function notfound_controller() {
  global $loader,$twig;
  print $twig->render('default/404.html');
  //[ <a href="/raw">Mongo Debug Info</a> | <a href='/server-info'>Server Debug Info</a> ]
}

function get_hooks($series = DEFAULT_SERIES,$charm_name) {
  $directory = realpath(CHARMS_CONTENT."/$series/$charm_name/hooks/");
  $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::SELF_FIRST);
  $objects = iterator_to_array($objects);
  foreach($objects as $name=>&$object){
    if(!is_file($name)){
      unset($objects[$name]);
    } else {
      $object = basename($name);
    }
  }
  return $objects;
}

function get_metadata($series = DEFAULT_SERIES, $charm_name) {
  $yaml = @file_get_contents(CHARMS_CONTENT."/$series/$charm_name/metadata.yaml");
  $parsed_yaml = @yaml_parse($yaml);
  return $parsed_yaml;
}

function get_series_charms($series = DEFAULT_SERIES) {
  global $mongo,$col_charms;
  $find_charmers = new MongoRegex("/.*charmers.*/");
  $find_defseries = new MongoRegex("/cs:".$series.".*/");
  $cur_charms = $col_charms->find(array('urls.0' => $find_charmers,'urls.1' => $find_defseries))->sort(array('meta.name' => 1));

  $cur_charms->rewind();
  if ($cur_charms->valid()) {
      $charms = iterator_to_array($cur_charms);
  }

  $charm_names = array();
  foreach ($charms as $key=>&$charm) {
    $href_path = @explode("/",$charm['urls'][1]);
    $charm['meta']['series'] = substr($href_path[0], 3);
    if ( in_array( $charm['meta']['name'], $charm_names ) ) {
        unset($charms[$key]);
    }
    else {
        $charm_names[] = $charm['meta']['name'];
    }
  }
  return $charms;
}

function api_controller($apiver,$series = DEFAULT_SERIES,$charm_name,$file) {
  $yaml = @file_get_contents(CHARMS_CONTENT."/$series/$charm_name/$file");
  $parsed_yaml = @yaml_parse($yaml);

  if($format == 'json') {
    print json_encode($parsed_yaml,JSON_PRETTY_PRINT);
  } else {
    print $yaml;
  }
}

function default_controler() {
  global $loader,$twig;
  $charms = get_series_charms();
  $total = count($charms);
  print $twig->render('default/index.html', array('charms' => $charms, 'total' => $total));
};

function charm_details_controller($series,$charm_name = "") {
  global $loader,$twig,$m,$col_charms;
  $find['meta.name'] = new MongoRegex("/^$charm_name/");
  $charm = $col_charms->findOne($find);

  if(file_exists("/mnt/charms/$series/$charm_name/README")) {
    $charm['meta']['readme'] = file_get_contents("/mnt/charms/$series/$charm_name/README");
  } elseif (file_exists("/mnt/charms/$series/$charm_name/readme")) {
    $charm['meta']['readme'] = file_get_contents("/mnt/charms/$series/$charm_name/readme");
  }
  $charm['meta']['series'] = $series;
  $charm['hooks'] = get_hooks($charm['meta']['series'],$charm['meta']['name']);
  $c_meta = get_metadata($charm['meta']['series'],$charm['meta']['name']);
  $charm['meta']['maintainer'] = $c_meta['maintainer'];

  print $twig->render('default/charm-details.html', array('charm' => $charm));
  //print_r($charm);
}

function get_series_controller($series){
  global $loader,$twig;
  $charms = get_series_charms($series);
  print $twig->render('default/index.html', array('charms' => $charms, 'total' => '0'));
}
