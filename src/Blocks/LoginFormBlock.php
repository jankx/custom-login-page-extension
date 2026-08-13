<?php
namespace Jankx\Extensions\CustomLoginPage\Blocks;

use Jankx\Extensions\CustomLoginPage\SocialLogin;
use Jankx\Facades\Option;

class LoginFormBlock
{
    protected $blockPath;

    public function __construct($blockPath = null)
    {
        $this->blockPath = $blockPath;
    }

    public function setBlockPath(string $path): void
    {
        $this->blockPath = $path;
    }

    public function boot(): void
    {
        if (!$this->blockPath) {
            $this->blockPath = $this->resolveBlockPath();
        }
    }

    public function register(): void
    {
        register_block_type_from_metadata($this->blockPath, [
            'render_callback' => [$this, 'render'],
        ]);
    }

    public function render($attributes, $content = '', $block = null)
    {
        $brandName = $attributes['brandName'] ?? 'NOBITOUR';
        $brandColor = $attributes['brandColor'] ?? '#65A30D';
        $showFacebookLogin = $attributes['showFacebookLogin'] ?? true;
        $showGoogleLogin = $attributes['showGoogleLogin'] ?? true;
        $showRememberMe = $attributes['showRememberMe'] ?? true;
        $showForgotPassword = $attributes['showForgotPassword'] ?? true;
        $registerPageUrl = $attributes['registerPageUrl'] ?? '';
        $forgotPasswordUrl = $attributes['forgotPasswordUrl'] ?? '';
        $contactUrl = $attributes['contactUrl'] ?? '';
        $redirectUrl = $attributes['redirectUrl'] ?? '';

        // Text attributes
        $loginTitle = $attributes['loginTitle'] ?? 'Đăng nhập';
        $loginSubtitle = $attributes['loginSubtitle'] ?? 'Không có tài khoản? ';
        $loginSubtitleLinkText = $attributes['loginSubtitleLinkText'] ?? 'Tạo tài khoản';
        $loginButtonText = $attributes['loginButtonText'] ?? 'Đăng nhập';
        $emailLabel = $attributes['emailLabel'] ?? 'Email/Tài khoản';
        $emailPlaceholder = $attributes['emailPlaceholder'] ?? 'Nhập email hoặc số điện thoại';
        $passwordLabel = $attributes['passwordLabel'] ?? 'Mật khẩu';
        $passwordPlaceholder = $attributes['passwordPlaceholder'] ?? 'Nhập mật khẩu';
        $rememberMeLabel = $attributes['rememberMeLabel'] ?? 'Duy trì đăng nhập';
        $forgotPasswordLabel = $attributes['forgotPasswordLabel'] ?? 'Quên mật khẩu';
        $socialDividerText = $attributes['socialDividerText'] ?? 'Hoặc';
        $facebookButtonText = $attributes['facebookButtonText'] ?? 'Đăng nhập nhanh với Facebook';
        $googleButtonText = $attributes['googleButtonText'] ?? 'Đăng nhập nhanh với Google';
        $helpText = $attributes['helpText'] ?? 'Cần giúp đỡ? ';
        $helpLinkText = $attributes['helpLinkText'] ?? 'Liên hệ với chúng tôi';
        $loggedInMessage = $attributes['loggedInMessage'] ?? 'Bạn đã đăng nhập.';
        $loggedInButtonText = $attributes['loggedInButtonText'] ?? 'Về trang chủ';

        if (is_user_logged_in()) {
            return $this->renderLoggedInState($brandColor, $loggedInMessage, $loggedInButtonText);
        }

        // Check social login status
        $socialLogin = new SocialLogin();
        $facebookEnabled = get_option('jankx_social_enable_facebook', 0) && !empty(get_option('jankx_social_facebook_app_id'));
        $googleEnabled = get_option('jankx_social_enable_google', 0) && !empty(get_option('jankx_social_google_client_id'));

        // Get social error message
        $socialError = $socialLogin->getSocialErrorMessage();

        $output = '<div class="jankx-login-form-wrapper">';

        $output .= '<h2 class="jankx-login-title">' . esc_html($loginTitle) . '</h2>';

        $output .= '<p class="jankx-login-subtitle">';
        $output .= esc_html($loginSubtitle);
        $registerUrl = $registerPageUrl ?: wp_registration_url();
        $output .= sprintf(
            '<a href="%s" style="color: %s;">%s</a>',
            esc_url($registerUrl),
            esc_attr($brandColor),
            esc_html($loginSubtitleLinkText)
        );
        $output .= '</p>';

        // Show social error if any
        if ($socialError) {
            $output .= '<div class="jankx-form-error">' . esc_html($socialError) . '</div>';
        }

        $output .= '<form name="loginform" id="jankx-loginform" action="' . esc_url(home_url('/wp-login.php')) . '" method="post">';

        $loginRedirect = $redirectUrl ?: home_url('/');
        $output .= '<input type="hidden" name="redirect_to" value="' . esc_url($loginRedirect) . '" />';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="user_login">' . esc_html($emailLabel) . '</label>';
        $output .= '<input type="text" name="log" id="user_login" class="jankx-form-control" placeholder="' . esc_attr($emailPlaceholder) . '" autocomplete="username" required />';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="user_pass">' . esc_html($passwordLabel) . '</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="pwd" id="user_pass" class="jankx-form-control" placeholder="' . esc_attr($passwordPlaceholder) . '" autocomplete="current-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= sprintf(
            '<button type="submit" name="wp-submit" class="jankx-btn jankx-btn-primary" style="background: %s;">%s</button>',
            esc_attr($brandColor),
            esc_html($loginButtonText)
        );

        wp_nonce_field('login');

        if ($showRememberMe || $showForgotPassword) {
            $output .= '<div class="jankx-login-options">';
            if ($showRememberMe) {
                $output .= '<label class="jankx-checkbox-label">';
                $output .= '<input type="checkbox" name="rememberme" value="forever" checked />';
                $output .= '<span class="jankx-checkbox-custom"></span>';
                $output .= esc_html($rememberMeLabel);
                $output .= '</label>';
            }
            if ($showForgotPassword) {
                $forgotUrl = $forgotPasswordUrl ?: wp_lostpassword_url();
                $output .= '<a href="' . esc_url($forgotUrl) . '" class="jankx-forgot-password" style="color: ' . esc_attr($brandColor) . ';">' . esc_html($forgotPasswordLabel) . '</a>';
            }
            $output .= '</div>';
        }

        $output .= '</form>';

        if (($showFacebookLogin && $facebookEnabled) || ($showGoogleLogin && $googleEnabled)) {
            $output .= '<div class="jankx-social-divider">';
            $output .= '<span>' . esc_html($socialDividerText) . '</span>';
            $output .= '</div>';

            if ($showFacebookLogin && $facebookEnabled) {
                $facebookUrl = $socialLogin->getFacebookLoginUrl();
                $output .= '<a href="' . esc_url($facebookUrl) . '" class="jankx-btn jankx-btn-facebook">';
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
                $output .= esc_html($facebookButtonText);
                $output .= '</a>';
            }

            if ($showGoogleLogin && $googleEnabled) {
                $googleUrl = $socialLogin->getGoogleLoginUrl();
                $output .= '<a href="' . esc_url($googleUrl) . '" class="jankx-btn jankx-btn-google">';
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>';
                $output .= esc_html($googleButtonText);
                $output .= '</a>';
            }
        }

        if ($contactUrl) {
            $output .= '<div class="jankx-login-help">';
            $output .= esc_html($helpText) . ' <a href="' . esc_url($contactUrl) . '" style="color: ' . esc_attr($brandColor) . ';">' . esc_html($helpLinkText) . '</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    protected function renderLoggedInState(string $brandColor, string $loggedInMessage, string $loggedInButtonText): string
    {
        if (Option::get('logged_in_redirect', true)) {
            wp_safe_redirect(site_url());
            exit();
        }

        $output = '<div class="jankx-login-form-wrapper">';
        $output .= '<div class="jankx-logged-in-message">';
        $output .= '<p>' . esc_html($loggedInMessage) . '</p>';
        $output .= sprintf(
            '<a href="%s" class="jankx-btn jankx-btn-primary" style="background: %s;">%s</a>',
            esc_url(home_url('/')),
            esc_attr($brandColor),
            esc_html($loggedInButtonText)
        );
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    protected function resolveBlockPath(): string
    {
        return dirname(__DIR__, 2) . '/blocks/login-form';
    }
}
