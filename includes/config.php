<?php
/**
 * Application Configuration
 * Contains API keys and global settings
 */

// Database & Environment Initialization
require_once __DIR__ . '/dbconnection.php';

// OpenAI API Configuration
define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY'] ?? 'YOUR_OPENAI_API_KEY_HERE');
define('OPENAI_MODEL', $_ENV['OPENAI_MODEL'] ?? 'gpt-5.4-nano');
define('OPENAI_API_URL', $_ENV['OPENAI_API_URL'] ?? 'https://api.openai.com/v1/chat/completions');
