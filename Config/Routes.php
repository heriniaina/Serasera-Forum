<?php 
$routes->group('forum', ['namespace' => '\Serasera\Forum\Controllers'], static function($routes) {
    $routes->get('topics', 'TopicController::index');
    $routes->group('topic', static function($routes) { 
        $routes->get('/', 'TopicController::index');

        $routes->add('add', 'TopicController::create', ['filter' => 'auth']);
        $routes->add('create', 'TopicController::create', ['filter' => 'auth']);
        $routes->add('delete/(:segment)(:any)', 'TopicController::delete/$1$2', ['filter' => 'auth']);
        $routes->add('edit/(:segment)', 'TopicController::edit/$1', ['filter' => 'auth']);
        $routes->get('(:segment)', 'TopicController::show/$1');   
        
    });
    
    
    $routes->group('message', static function($routes) {

        $routes->add('/', 'MessageController::index');
        $routes->get('history', 'MessageController::history');
        $routes->get('user', 'MessageController::user');
        $routes->add('move/(:segment)', 'MessageController::move/$1', ['filter' => 'auth']);
        $routes->add('reply/(:segment)(:any)', 'MessageController::reply/$1$2', ['filter' => 'auth']);
        $routes->add('new(:any)', 'MessageController::create', ['filter' => 'auth']);
        $routes->add('edit/(:segment)', 'MessageController::edit/$1', ['filter' => 'auth']);
        $routes->add('delete/(:segment)', 'MessageController::delete/$1', ['filter' => 'auth']);
        $routes->add('search', 'MessageController::search');
        $routes->get('unsubscribe/(:segment)', 'MessageController::unsubscribe/$1', ['filter' => 'auth']);
        $routes->get('(:segment)(:any)', 'MessageController::show/$1$2');
        
    });    
    
});
