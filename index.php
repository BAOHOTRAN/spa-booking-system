<?php
/**
 * SPA Booking System - API & Main Entry Point
 * Created by: BAOHOTRAN - tqbao200468@gmail.com
 * Version: 2.0 with CI/CD
 */

// Check if this is an API request
if (isset($_GET['api']) || strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // Simple database simulation
    class SpaDatabase {
        private $services;
        private $bookings;
        
        public function __construct() {
            $this->services = [
                ['id' => 1, 'name' => 'Massage Therapy', 'price' => 80, 'duration' => 60, 'available' => true],
                ['id' => 2, 'name' => 'Facial Treatment', 'price' => 60, 'duration' => 45, 'available' => true],
                ['id' => 3, 'name' => 'Body Wrap', 'price' => 100, 'duration' => 90, 'available' => true],
                ['id' => 4, 'name' => 'Hot Stone Massage', 'price' => 120, 'duration' => 75, 'available' => true],
                ['id' => 5, 'name' => 'Aromatherapy', 'price' => 90, 'duration' => 60, 'available' => true]
            ];
            
            $this->bookings = [];
        }
        
        public function getServices() {
            return array_filter($this->services, function($service) {
                return $service['available'];
            });
        }
        
        public function getService($id) {
            foreach ($this->services as $service) {
                if ($service['id'] == $id) {
                    return $service;
                }
            }
            return null;
        }
        
        public function createBooking($data) {
            $booking = [
                'id' => count($this->bookings) + 1,
                'customer_name' => $data['name'] ?? '',
                'customer_email' => $data['email'] ?? '',
                'service_id' => $data['service_id'] ?? 0,
                'booking_date' => $data['date'] ?? date('Y-m-d'),
                'booking_time' => $data['time'] ?? '10:00',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->bookings[] = $booking;
            return $booking;
        }
        
        public function getBookings() {
            return $this->bookings;
        }
    }

    // Initialize database
    $db = new SpaDatabase();

    // Get API endpoint
    $api_endpoint = $_GET['api'] ?? '';
    if (empty($api_endpoint)) {
        // Extract from URI
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (strpos($uri, '/api/') !== false) {
            $api_endpoint = str_replace('/api/', '', $uri);
        }
    }

    // Router
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($api_endpoint) {
        case 'services':
            if($method === 'GET') {
                $services = $db->getServices();
                echo json_encode([
                    'success' => true,
                    'data' => array_values($services),
                    'count' => count($services)
                ]);
            }
            break;
        
        case 'bookings':
            if($method === 'GET') {
                $bookings = $db->getBookings();
                echo json_encode([
                    'success' => true,
                    'data' => $bookings,
                    'count' => count($bookings)
                ]);
            } elseif($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (empty($input['name']) || empty($input['email']) || empty($input['service_id'])) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Missing required fields: name, email, service_id'
                    ]);
                    break;
                }
                
                if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid email format'
                    ]);
                    break;
                }
                
                $service = $db->getService($input['service_id']);
                if (!$service) {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Service not found'
                    ]);
                    break;
                }
                
                $booking = $db->createBooking($input);
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'data' => $booking,
                    'message' => 'Booking created successfully'
                ]);
            }
            break;
        
        case 'health':
            echo json_encode([
                'status' => 'healthy',
                'timestamp' => time(),
                'date' => date('Y-m-d H:i:s'),
                'version' => '2.0.0',
                'services_count' => count($db->getServices()),
                'bookings_count' => count($db->getBookings()),
                'developer' => 'BAOHOTRAN',
                'email' => 'tqbao200468@gmail.com'
            ]);
            break;
        
        case 'stats':
            $services = $db->getServices();
            $bookings = $db->getBookings();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'total_services' => count($services),
                    'total_bookings' => count($bookings),
                    'average_price' => count($services) > 0 ? array_sum(array_column($services, 'price')) / count($services) : 0,
                    'most_expensive_service' => count($services) > 0 ? max(array_column($services, 'price')) : 0,
                    'cheapest_service' => count($services) > 0 ? min(array_column($services, 'price')) : 0
                ]
            ]);
            break;
            
        case '':
        default:
            // API Documentation
            echo json_encode([
                'message' => 'SPA Booking API',
                'version' => '2.0.0',
                'developer' => 'BAOHOTRAN',
                'email' => 'tqbao200468@gmail.com',
                'endpoints' => [
                    'GET /?api=services' => 'Get all available services',
                    'GET /?api=bookings' => 'Get all bookings',
                    'POST /?api=bookings' => 'Create new booking',
                    'GET /?api=health' => 'Health check',
                    'GET /?api=stats' => 'Get statistics'
                ],
                'example_booking' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'service_id' => 1,
                    'date' => '2025-01-20',
                    'time' => '14:00'
                ]
            ]);
    }
    exit();
}

// If not API request, redirect to public directory
header("Location: public/index.php");
exit();
?>
