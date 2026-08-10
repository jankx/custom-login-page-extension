<?php
namespace Jankx\Extensions\CustomLoginPage;

use Jankx\Extensions\AbstractExtension;

class CustomLoginPageExtension extends AbstractExtension
{
    protected static $instance;

    const LOGIN_PAGE_OPTION = 'jankx_login_page_id';
    const REGISTER_PAGE_OPTION = 'jankx_register_page_id';

    public function __construct()
    {
        $this->register_autoloader();
        parent::__construct();
    }

    protected function register_autoloader()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Jankx\\Extensions\\CustomLoginPage\\';
            $base_dir = __DIR__ . '/src/';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function init(): void
    {
        self::$instance = $this;
    }

    public static function get_instance(): ?self
    {
        return self::$instance;
    }

    public function register_hooks(): void
    {
        $customizer = new LoginCustomizer();
        $customizer->register();

        $socialLogin = new SocialLogin();
        $socialLogin->register();

        if (is_admin()) {
            $settingsPage = new \Jankx\Extensions\CustomLoginPage\Admin\SettingsPage();
            $settingsPage->register();
        }

        add_action('init', [$this, 'registerBlocks']);
        add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);

        // Auto create pages on activation
        add_action('admin_init', [$this, 'maybeCreatePages']);
    }

    public function maybeCreatePages(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if pages already exist
        $loginPageId = get_option(self::LOGIN_PAGE_OPTION);
        $registerPageId = get_option(self::REGISTER_PAGE_OPTION);

        if (!$loginPageId || !$registerPageId) {
            $this->createPages();
        }
    }

    public function createPages(): void
    {
        $loginPageId = $this->createLoginPage();
        $registerPageId = $this->createRegisterPage();

        if ($loginPageId) {
            update_option(self::LOGIN_PAGE_OPTION, $loginPageId);
            // Set as WP login page
            update_option('jankx_custom_login_page', $loginPageId);
        }

        if ($registerPageId) {
            update_option(self::REGISTER_PAGE_OPTION, $registerPageId);
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    protected function createLoginPage(): int
    {
        // Check if page already exists
        $existingPage = get_page_by_path('dang-nhap');
        if ($existingPage) {
            return $existingPage->ID;
        }

        $pageData = [
            'post_title' => 'Đăng nhập',
            'post_name' => 'dang-nhap',
            'post_content' => $this->getLoginPageContent(),
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ];

        return wp_insert_post($pageData);
    }

    protected function createRegisterPage(): int
    {
        // Check if page already exists
        $existingPage = get_page_by_path('dang-ky');
        if ($existingPage) {
            return $existingPage->ID;
        }

        $pageData = [
            'post_title' => 'Đăng ký',
            'post_name' => 'dang-ky',
            'post_content' => $this->getRegisterPageContent(),
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ];

        return wp_insert_post($pageData);
    }

    protected function getLoginPageContent(): string
    {
        $registerPageUrl = home_url('/dang-ky/');
        $forgotPasswordUrl = wp_lostpassword_url();
        $contactUrl = home_url('/lien-he/');
        $redirectUrl = home_url('/');

        return '<!-- wp:jankx/login-page {"backgroundImage":"","pageType":"login","brandName":"NOBITOUR","brandColor":"#65A30D"} -->
<div class="wp-block-jankx-login-page jankx-login-page-wrapper" data-page-type="login"><!-- wp:jankx/login-form {"brandName":"NOBITOUR","brandColor":"#65A30D","showFacebookLogin":true,"showGoogleLogin":true,"showRememberMe":true,"showForgotPassword":true,"registerPageUrl":"' . esc_attr($registerPageUrl) . '","forgotPasswordUrl":"' . esc_attr($forgotPasswordUrl) . '","contactUrl":"' . esc_attr($contactUrl) . '","redirectUrl":"' . esc_attr($redirectUrl) . '"} /--></div>
<!-- /wp:jankx/login-page -->';
    }

    protected function getRegisterPageContent(): string
    {
        $loginPageUrl = home_url('/dang-nhap/');
        $contactUrl = home_url('/lien-he/');

        return '<!-- wp:jankx/login-page {"backgroundImage":"","pageType":"register","brandName":"NOBITOUR","brandColor":"#65A30D"} -->
<div class="wp-block-jankx-login-page jankx-login-page-wrapper" data-page-type="register"><!-- wp:jankx/register-form {"brandName":"NOBITOUR","brandColor":"#65A30D","showPhoneField":true,"showTermsCheckbox":true,"loginPageUrl":"' . esc_attr($loginPageUrl) . '","contactUrl":"' . esc_attr($contactUrl) . '"} /--></div>
<!-- /wp:jankx/login-page -->';
    }

    public function getLoginPageUrl(): string
    {
        $pageId = get_option(self::LOGIN_PAGE_OPTION);
        if ($pageId) {
            return get_permalink($pageId);
        }
        return home_url('/dang-nhap/');
    }

    public function getRegisterPageUrl(): string
    {
        $pageId = get_option(self::REGISTER_PAGE_OPTION);
        if ($pageId) {
            return get_permalink($pageId);
        }
        return home_url('/dang-ky/');
    }

    public function registerBlocks(): void
    {
        $blockPath = dirname(__DIR__, 2) . '/extensions/custom-login-page/blocks';

        $blocks = [
            'login-page' => \Jankx\Extensions\CustomLoginPage\Blocks\LoginPageBlock::class,
            'login-form' => \Jankx\Extensions\CustomLoginPage\Blocks\LoginFormBlock::class,
            'register-form' => \Jankx\Extensions\CustomLoginPage\Blocks\RegisterFormBlock::class,
        ];

        foreach ($blocks as $blockName => $blockClass) {
            $block = new $blockClass($blockPath . '/' . $blockName);
            $block->boot();
            $block->register();
        }
    }

    public function enqueueBlockAssets(): void
    {
        $extension = CustomLoginPageExtension::get_instance();
        if (!$extension) {
            return;
        }

        wp_enqueue_style(
            'jankx-login-blocks',
            $extension->get_extension_url() . '/blocks/login-page/build/style.css',
            [],
            '1.0.0'
        );
    }
}
