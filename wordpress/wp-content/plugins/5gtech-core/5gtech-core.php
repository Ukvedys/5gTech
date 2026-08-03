<?php
/**
 * Plugin Name: 5G TECH Core
 * Description: 5G TECH paslaugų turinio struktūra ir svetainės blokai.
 * Version: 0.14.0
 * Requires at least: 6.7
 * Requires PHP: 8.1
 * Author: Object
 * Text Domain: 5gtech-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'G5TECH_CORE_VERSION', '0.14.0' );
define( 'G5TECH_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'G5TECH_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once G5TECH_CORE_DIR . 'includes/i18n.php';
require_once G5TECH_CORE_DIR . 'includes/repeaters.php';
require_once G5TECH_CORE_DIR . 'includes/settings.php';
require_once G5TECH_CORE_DIR . 'includes/partners.php';
require_once G5TECH_CORE_DIR . 'includes/modules.php';
require_once G5TECH_CORE_DIR . 'includes/admin-ui.php';
require_once G5TECH_CORE_DIR . 'includes/structured-content.php';
require_once G5TECH_CORE_DIR . 'includes/module-layouts.php';
require_once G5TECH_CORE_DIR . 'includes/i18n-admin.php';
require_once G5TECH_CORE_DIR . 'includes/services.php';
require_once G5TECH_CORE_DIR . 'includes/projects.php';
require_once G5TECH_CORE_DIR . 'includes/faqs.php';
require_once G5TECH_CORE_DIR . 'includes/team.php';
require_once G5TECH_CORE_DIR . 'includes/jobs.php';
require_once G5TECH_CORE_DIR . 'includes/news.php';
require_once G5TECH_CORE_DIR . 'includes/homepage.php';
require_once G5TECH_CORE_DIR . 'includes/forms.php';
require_once G5TECH_CORE_DIR . 'includes/legal.php';
require_once G5TECH_CORE_DIR . 'includes/content-pages.php';
require_once G5TECH_CORE_DIR . 'includes/service-blocks.php';
require_once G5TECH_CORE_DIR . 'includes/site-blocks.php';
require_once G5TECH_CORE_DIR . 'includes/admin.php';
require_once G5TECH_CORE_DIR . 'includes/seo.php';
require_once G5TECH_CORE_DIR . 'includes/content-blocks.php';
require_once G5TECH_CORE_DIR . 'includes/editor-curation.php';
require_once G5TECH_CORE_DIR . 'includes/admin-redirects.php';
require_once G5TECH_CORE_DIR . 'includes/migrations.php';

function g5tech_core_activate() {
	g5tech_sync_editor_roles();
	g5tech_register_service_type();
	g5tech_register_project_type();
	g5tech_register_job_type();
	g5tech_register_content_module_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'g5tech_core_activate' );

function g5tech_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'g5tech_core_deactivate' );
