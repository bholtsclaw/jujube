<?php
$app = new Slim();

$app->notFound('notfound_controler');

$app->get('/', 'default_controler');

$app->get('/charms', 'default_controler');

$app->get('/charms/:series', 'get_series_controller');

$app->get('/charms/:series/:charm_name', 'charm_details_controller');

$app->get('/api/v0/charms/:series/:charm_name/contents/:file/:format', 'api_controller');
$app->get('/api/latest/charms/:series/:charm_name/contents/:file/:format', 'api_controller');

$app->get('/server-info', function () { phpinfo(); });

$app->get('/raw', function () {
        $m = new Mongo("mongodb://".MONGO_HOST);
        $c_charms = $m->juju->charms;
        $charms = $c_charms->find();
        print '<pre>';
        foreach ($charms as $id => $charm) {
          print_r($charm);
        }
        print '</pre>';
});

