<?php
require_once 'controllers/BaseController.php';

class HomeController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Featured destinations data with French descriptions
        $featured_destinations = [
            [
                'city' => 'Paris',
                'country' => 'France',
                'description' => 'Experience the city of love', // Keep original for potential fallback/other languages
                'description_fr' => 'Découvrez la ville de l\'amour',
                'image' => 'paris.jpg'
            ],
            [
                'city' => 'Tokyo',
                'country' => 'Japan',
                'description' => 'Discover the blend of tradition and innovation',
                'description_fr' => 'Découvrez le mélange de tradition et d\'innovation',
                'image' => 'tokyo.jpg'
            ],
            [
                'city' => 'New York',
                'country' => 'USA',
                'description' => 'The city that never sleeps',
                'description_fr' => 'La ville qui ne dort jamais',
                'image' => 'newyork.jpg'
            ]
        ];
        
        $this->view('home/index', [
            'featured_destinations' => $featured_destinations
        ]);
    }
} 