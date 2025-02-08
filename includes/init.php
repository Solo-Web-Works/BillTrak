<?php
// Set up error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the base path for includes
define('BASE_PATH', __DIR__ . '/');

// Autoload classes from the includes directory
spl_autoload_register(function ($className) {
  $filePath = BASE_PATH . '/' . strtolower($className) . '.php';

  if (file_exists($filePath)) {
    require_once $filePath;
  }
});

// Start session management
session_start();

// Additional configuration can be added here if needed
