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

function default_controler() {
  global $loader,$twig,$mongo,$col_charms;
  $find_charmers = new MongoRegex("/.*charmers.*/");
  $find_defseries = new MongoRegex("/cs:".DEFAULT_SERIES.".*/");
  $cur_charms = $col_charms->find(array('urls.0' => $find_charmers,'urls.1' => $find_defseries))->sort(array('meta.name' => 1));

  $total_charms = $cur_charms->count();
  $cur_charms->rewind();
  if ($cur_charms->valid()) {
      $charms = iterator_to_array($cur_charms);
  }

  foreach ($charms as &$charm) {
    $href_path = @explode("/",$charm['urls'][1]);
    $charm['meta']['series'] = substr($href_path[0], 3);
  }

  print $twig->render('default/index.html', array('charms' => $charms, 'total' => $total_charms));
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

  print $twig->render('default/charm-details.html', array('charm' => $charm));
}

function get_series_controller($series){
  global $loader,$twig,$mongo,$col_charms;
  $find_defseries = new MongoRegex("/cs:".$series.".*/");
  $cur_charms = $col_charms->find(array('urls.1' => $find_defseries))->sort(array('meta.name' => 1));

  $total_charms = $cur_charms->count();
  $cur_charms->rewind();
  if ($cur_charms->valid()) {
      $charms = iterator_to_array($cur_charms);
  }

  foreach ($charms as $charm) {
    $href_path = @explode("/",$charm['urls'][1]);
    $charm_series[$charm['meta']['name']] = substr($href_path[0], 3);
  }

  print $twig->render('default/index.html', array('charms' => $charms, 'total' => $total_charms,'series' => $charm_series));
}
