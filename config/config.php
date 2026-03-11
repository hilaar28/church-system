<?php
/**
 * Main Configuration File
 */

// Site Configuration
define('SITE_NAME', 'Church Management System');
define('SITE_URL', 'http://localhost/church-system');
define('SITE_PATH', __DIR__);

// Path Configuration (use guards to prevent redefinition)
if (!defined('APP_PATH')) define('APP_PATH', __DIR__ . '/../app');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', __DIR__ . '/../public');
if (!defined('VIEW_PATH')) define('VIEW_PATH', __DIR__ . '/../app/Views');
if (!defined('CONTROLLER_PATH')) define('CONTROLLER_PATH', __DIR__ . '/../app/Controllers');
if (!defined('MODEL_PATH')) define('MODEL_PATH', __DIR__ . '/../app/Models');
if (!defined('LIB_PATH')) define('LIB_PATH', __DIR__ . '/../app/Libraries');
if (!defined('HELPER_PATH')) define('HELPER_PATH', __DIR__ . '/../helpers');

// Timezone
date_default_timezone_set('Africa/Harare');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security Configuration
define('HASH_ALGO', 'bcrypt');
define('CSRF_TOKEN_NAME', 'csrf_token');
define('REMEMBER_ME_COOKIE', 'church_remember');
define('REMEMBER_ME_EXPIRY', 86400 * 30); // 30 days

// Pagination
define('ITEMS_PER_PAGE', 20);

// File Upload
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd M Y');
define('DISPLAY_DATETIME_FORMAT', 'd M Y H:i');

// Currency
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');
