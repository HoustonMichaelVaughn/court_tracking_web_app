<?php
// An explanation of what the hell I've done and is happening so I don't forget.
// Prerequisites for understanding this - index.php, defendant_controller.php
// get(), post(), and so on define a function that runs when a particular path is detected. 
// This function gets stored in a global array called $routes. 
// Then, when a request comes in, resolve() grabs the URI from the server, tidies it up, 
// and checks to see if there’s a hit in the $routes array. If it finds one, it doesn’t just run the function straight away
//  it needs to help it a bit first. That’s where compile_app() comes in. 
// It prepares the $app object and gives it useful tools like the ability to render a view or pass messages along to it. 
// Once that’s done, the function that was originally defined in get() now has everything it needs to complete its journey.
$routes = [];


function path($pattern, $callback)
{
    global $routes;
    $routes['PATH'][] = ['pattern' => $pattern, 'callback' => $callback];

}

function get($path, $callback)
{
    global $routes;
    $routes['GET'][$path] = $callback;
}

// Generic handler for POST
// I've left it in in case we need it.
function post($path, $callback)
{
    global $routes;
    $routes['POST'][$path] = $callback;
}

function compile_app(&$app)
{
    //  Used to finalise the app function. Defines the function render and set_message
    //  This are called when callback is trigged in resolve. 
    $app->render = function ($layout, $content, $data = []) use (&$app) {
        DEFINE("VIEWS", __DIR__ . '/../views');
        DEFINE("PARTIALS", VIEWS . '/partials');

        foreach (get_object_vars($app) as $key => $value) {
            $$key = $value;
        }

        extract($data);

        require VIEWS . "/{$layout}.layout.php";
    };

    $app->set_message = function ($key, $value) use (&$app) {
        $app->$key = $value;
    };
}

function resolve()
{
    global $routes;
    $method = $_SERVER['REQUEST_METHOD'];
    //  URI cleaning
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = str_replace('/index.php', '', $uri);
    $uri = rtrim($uri, '/');
    $uri = $uri ?: '/';
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base !== '/' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    $uri = $uri ?: '/';
    //  Can the URI be matched simply by method?
    if (isset($routes[$method][$uri])) {
        $app = new stdClass();
        compile_app($app);
        //  Belows is equivalent to $callback = $routes[$method][$uri]; $callback($app);
        $routes[$method][$uri]($app);
        return;
    } 
    //  If no simple match exists, is the URI a regex path?
    elseif (isset($routes['PATH'])) {
        foreach ($routes['PATH'] as $route) {
            $pattern = preg_replace('#\{(\w+)\}#', '(?P<\1>[^/]+)', $route['pattern']);
            $regex = "#^" . $pattern . "$#";
            if (preg_match($regex, $uri, $matches)) {
                $app = new stdClass();
                compile_app($app);
                // Drop the [0] from matches that is the full match.
                $namedParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // The ... is an unpacking operator, the same as * in Python
                // Below is equivalent to $route['callback']($app, 'n', 'n...'); where n is each parameter 
                $route['callback']($app, ...array_values($namedParams));
                return;
            }
        }
    }
    // If we reach here then no simple match and no PATH match exists
    $app = new stdClass();
    compile_app($app);
    http_response_code(404);
    ($app->render)('standard', '404');
}