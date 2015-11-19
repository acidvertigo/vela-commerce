<?php

namespace Vela;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL|E_STRICT);

// define environment
$environment = 'development';

/**
* Register the error handler
*/
$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        echo 'Friendly error page and send an email to the developer';
    });
}
$whoops->register();

/**
 * Start Request REsponse Objects
 */
$request = \Sabre\HTTP\Sapi::getRequest();
$response = new \Sabre\HTTP\Response();

/**
 * Start session object if user is not a robot
 */
$userAgent = $request->getRawServerValue('HTTP_USER_AGENT');

$userIp = '';
if (getenv('HTTP_CLIENT_IP'))
        $userIp = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $userIp = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $userIp = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $userIp = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $userIp = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $userIp = getenv('REMOTE_ADDR');
    else
        $userIp = 'UNKNOWN';

$robots = array(
    'googlebot'     => 'Googlebot',
    'msnbot'        => 'MSNBot',
    'baiduspider'   => 'Baiduspider',
    'bingbot'       => 'Bing',
    'slurp'         => 'Inktomi Slurp',
    'yahoo'         => 'Yahoo',
    'askjeeves'     => 'AskJeeves',
    'fastcrawler'   => 'FastCrawler',
    'infoseek'      => 'InfoSeek Robot 1.0',
    'lycos'         => 'Lycos',
    'yandex'        => 'YandexBot'
);

$isRobot = false;
foreach ($robots as $robot) {
    if(strpos(strtolower($userAgent), $robot) !== false)
    {
       $isRobot = true;
       break;
    }
}

if (!$isRobot)
{ 
    $session_factory = new \Aura\Session\SessionFactory;
    $session = $session_factory->newInstance($_COOKIE);

    $segment = $session->getSegment('User');
    $segment->set('IPaddress', $userIp);
    $segment->set('userAgent', $userAgent);
    
    if(!$segment->get('IPaddress') || !$segment->get('userAgent'))
    {
       $session->regenerateId();
    }

    if ($segment->get('IPaddress') != $userIp)
    {
       $session->regenerateId();
    }

    if( $segment->get('userAgent') != $userAgent)
    {
       $session->regenerateId();
    }
}

/**
 * Start url parser
 */
$url = \Purl\Url::fromCurrent();
var_dump ($_SESSION);
/**
 * Start dic container
 */
$dic = new \Auryn\Injector;

// share dependencies
$dic->share($request);
$dic->share($response);
$dic->share($url);
if (isset($session))
{
    $dic->share($session);
}

/**
 * Initialize router
 */
$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};


$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);
$routeInfo = $dispatcher->dispatch($request->getMethod(), '/'.$request->getPath());
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setBody('404 - Page not found');
        $response->setStatus(404);
        \Sabre\HTTP\Sapi::sendResponse($response);
        exit;
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setBody('405 - Method not allowed');
        $response->setStatus(405);
        \Sabre\HTTP\Sapi::sendResponse($response);
        exit;
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];
        $class = $dic->make($className);
        $class->$method($vars);
        break;
}