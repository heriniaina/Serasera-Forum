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
    
    $routes->get('rss', 'RssController::index');
    $routes->get('rss/updates', 'RssController::updates');

    
    $routes->group('message', static function($routes) {

        $routes->get('/', 'MessageController::index');
        //$routes->post('image', 'MessageController::image', ['filter' => 'auth']); 
        $routes->get('delete/(:segment)(:any)', 'MessageController::delete/$1$2', ['filter' => 'auth']);
        $routes->get('history', 'MessageController::history');
        $routes->get('user', 'MessageController::user');
        $routes->get('move/(:segment)', 'MessageController::move/$1', ['filter' => 'auth']);
        $routes->match(['get', 'post'], 'new(:any)', 'MessageController::create$1', ['filter' => 'auth']);
        $routes->match(['get', 'post'],'edit/(:segment)', 'MessageController::edit/$1', ['filter' => 'auth']);
        
        $routes->match(['get', 'post'],'search', 'MessageController::search');
        $routes->match(['get', 'post'],'unsubscribe/(:segment)', 'MessageController::unsubscribe/$1', ['filter' => 'auth']);
        $routes->match(['get', 'post'],'(:segment)/reply(:any)', 'MessageController::reply/$1$2', ['filter' => 'auth']);
        $routes->get('(:segment)(:any)', 'MessageController::show/$1$2');
        
        
    });    
    
    $routes->get('user/(:segment)', 'UserController::show/$1');
    
});

$routes->post('api/forum/image','\Serasera\Forum\Controllers\MessageController::image', ['filter' => 'auth']); 
