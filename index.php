<?php
// Session configuration must be set before session starts
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS

session_start();
require_once 'config/config.php';
require_once 'helpers/functions.php';
require_once 'helpers/jwt_helper.php';

// Router
$request = $_SERVER['REQUEST_URI'];
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($basePath, '', $request);

// Remove query string
if (strpos($route, '?') !== false) {
    $route = substr($route, 0, strpos($route, '?'));
}

// Default route
if ($route == '/' || $route == '') {
    $route = '/home';
}

// Auth middleware check
$publicRoutes = ['/home', '/login', '/register', '/api/auth/login', '/api/auth/register'];
$isApi = strpos($route, '/api/') === 0;

// Check for JWT on protected routes
if (!in_array($route, $publicRoutes) && !$isApi) {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login if not authenticated
        header('Location: ' . $basePath . '/login');
        exit;
    }
}

// API JWT check
if ($isApi && !in_array($route, ['/api/auth/login', '/api/auth/register'])) {
    $headers = getallheaders();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
    if (!$token || !validateJWT($token)) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// Admin Role Check for /admin routes
if (strpos($route, '/admin/') === 0) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // If not admin, check if logged in at all
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage('Veuillez vous connecter pour accéder à cette page.', 'danger');
            redirect('/login?redirect=' . urlencode($request));
        } else {
            // Logged in but not admin
            setFlashMessage('Accès non autorisé.', 'danger');
            redirect('/dashboard'); // Redirect non-admins away from /admin
        }
        exit;
    }
}

// Route to controller/action
switch (true) { // Use switch(true) for more complex matching like preg_match
    // Public routes
    case $route === '/home':
        require_once 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
    case $route === '/login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
        $controller->loginForm();
        }
        break;
    case $route === '/register':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
        $controller->registerForm();
        }
        break;
        
    // API routes
    case $route === '/api/auth/login':
        require_once 'controllers/api/AuthApiController.php';
        $controller = new AuthApiController();
        $controller->login();
        break;
    case $route === '/api/auth/register':
        require_once 'controllers/api/AuthApiController.php';
        $controller = new AuthApiController();
        $controller->register();
        break;
    case $route === '/api/flights':
        require_once 'controllers/api/FlightApiController.php';
        $controller = new FlightApiController();
        $controller->index();
        break;
    case $route === '/api/hotels':
        require_once 'controllers/api/HotelApiController.php';
        $controller = new HotelApiController();
        $controller->index();
        break;
    case $route === '/api/cruises':
        require_once 'controllers/api/CruiseApiController.php';
        $controller = new CruiseApiController();
        $controller->index();
        break;
    case $route === '/api/bookings':
        require_once 'controllers/api/BookingApiController.php';
        $controller = new BookingApiController();
        $controller->index();
        break;
        
    // Protected User routes (already checked for session['user_id'])
    case $route === '/dashboard':
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
    case $route === '/flights':
        require_once 'controllers/FlightController.php';
        $controller = new FlightController();
        $controller->index();
        break;
    case $route === '/hotels':
        require_once 'controllers/HotelController.php';
        $controller = new HotelController();
        $controller->index();
        break;
    case $route === '/cruises':
        require_once 'controllers/CruiseController.php';
        $controller = new CruiseController();
        $controller->index();
        break;
    case $route === '/bookings':
        require_once 'controllers/BookingController.php';
        $controller = new BookingController();
        $controller->index();
        break;
    case preg_match('#^/book/(flight|hotel|cruise)/(\d+)$#', $route, $matches):
        require_once 'controllers/BookingController.php';
        $controller = new BookingController();
        $_GET['type'] = $matches[1];
        $_GET['id'] = $matches[2];
        $controller->process();
        break;
    case $route === '/booking/confirm':
        require_once 'controllers/BookingController.php';
        $controller = new BookingController();
        $controller->confirm();
        break;
    case $route === '/profile':
        require_once 'controllers/ProfileController.php';
        $controller = new ProfileController();
        $controller->index(); // Assuming index shows profile, add update route later
        break;
    case $route === '/logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
    
    // Admin Routes (already checked for session['user_role'] === 'admin')
    case $route === '/admin/dashboard':
        require_once 'controllers/admin/AdminDashboardController.php';
        $controller = new AdminDashboardController();
        $controller->index();
        break;

    // Admin Flights CRUD
    case $route === '/admin/flights':
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->index();
        break;
    case $route === '/admin/flights/create':
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->create();
        break;
    case $route === '/admin/flights/store':
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->store();
        break;
    case preg_match('#^/admin/flights/edit/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->edit($matches[1]); // Pass the ID
        break;
    case preg_match('#^/admin/flights/update/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->update($matches[1]); // Pass the ID
        break;
    case preg_match('#^/admin/flights/delete/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminFlightController.php';
        $controller = new AdminFlightController();
        $controller->destroy($matches[1]); // Pass the ID
        break;

    // Admin Airlines CRUD
    case $route === '/admin/airlines':
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->index();
        break;
    case $route === '/admin/airlines/create':
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->create();
        break;
    case $route === '/admin/airlines/store':
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->store();
        break;
    case preg_match('#^/admin/airlines/edit/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->edit($matches[1]); // Pass the ID
        break;
    case preg_match('#^/admin/airlines/update/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->update($matches[1]); // Pass the ID
        break;
    case preg_match('#^/admin/airlines/delete/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminAirlineController.php';
        $controller = new AdminAirlineController();
        $controller->destroy($matches[1]); // Pass the ID
        break;

    // TODO: Add Admin Cruises CRUD routes here
    // Admin Cruises CRUD
    case $route === '/admin/cruises':
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->index();
        break;
    case $route === '/admin/cruises/create':
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->create();
        break;
    case $route === '/admin/cruises/store':
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->store();
        break;
    case preg_match('#^/admin/cruises/edit/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->edit($matches[1]);
        break;
    case preg_match('#^/admin/cruises/update/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->update($matches[1]);
        break;
    case preg_match('#^/admin/cruises/delete/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminCruiseController.php';
        $controller = new AdminCruiseController();
        $controller->delete($matches[1]);
        break;

    // Admin Bookings Management
    case $route === '/admin/bookings':
        require_once 'controllers/admin/AdminBookingController.php';
        $controller = new AdminBookingController();
        $controller->index();
        break;
    case preg_match('#^/admin/bookings/cancel/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminBookingController.php';
        $controller = new AdminBookingController();
        $controller->cancel($matches[1]);
        break;

    // Admin Users Management
    case $route === '/admin/users':
        require_once 'controllers/admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->index();
        break;
    case preg_match('#^/admin/users/edit/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->edit($matches[1]);
        break;
    case preg_match('#^/admin/users/update/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->update($matches[1]);
        break;
    case preg_match('#^/admin/users/delete/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->destroy($matches[1]);
        break;

    // Admin Hotels CRUD
    case $route === '/admin/hotels':
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->index();
        break;
    case $route === '/admin/hotels/create':
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->create();
        break;
    case $route === '/admin/hotels/store':
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->store();
        break;
    case preg_match('#^/admin/hotels/edit/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->edit($matches[1]);
        break;
    case preg_match('#^/admin/hotels/update/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->update($matches[1]);
        break;
    case preg_match('#^/admin/hotels/delete/(\d+)$#', $route, $matches):
        require_once 'controllers/admin/AdminHotelController.php';
        $controller = new AdminHotelController();
        $controller->destroy($matches[1]);
        break;

    // Currency Switching
    case $route === '/switch-currency':
        require_once 'controllers/CurrencyController.php';
        $controller = new CurrencyController();
        $controller->switchCurrency();
        break;

    default:
        http_response_code(404);
        include 'views/404.php'; // Make sure this 404 view exists
        break;
}
?>