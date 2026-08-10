<?php
namespace Jankx\Extensions\CustomLoginPage\Blocks;

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

        if (is_user_logged_in()) {
            return $this->renderLoggedInState($brandColor);
        }

        $output = '<div class="jankx-login-form-wrapper">';

        $output .= '<h2 class="jankx-login-title">Đăng nhập</h2>';

        $output .= '<p class="jankx-login-subtitle">';
        $output .= 'Không có tài khoản? ';
        if ($registerPageUrl) {
            $output .= sprintf(
                '<a href="%s" style="color: %s;">Tạo tài khoản</a>',
                esc_url($registerPageUrl),
                esc_attr($brandColor)
            );
        } else {
            $output .= sprintf(
                '<a href="%s" style="color: %s;">Tạo tài khoản</a>',
                esc_url(wp_registration_url()),
                esc_attr($brandColor)
            );
        }
        $output .= '</p>';

        $output .= '<form name="loginform" id="jankx-loginform" action="' . esc_url(home_url('/wp-login.php')) . '" method="post">';

        $loginRedirect = $redirectUrl ? $redirectUrl : home_url('/');
        $output .= '<input type="hidden" name="redirect_to" value="' . esc_url($loginRedirect) . '" />';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="user_login">Email/Tài khoản</label>';
        $output .= '<input type="text" name="log" id="user_login" class="jankx-form-control" placeholder="Nhập email hoặc số điện thoại" autocomplete="username" required />';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="user_pass">Mật khẩu</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="pwd" id="user_pass" class="jankx-form-control" placeholder="Nhập mật khẩu" autocomplete="current-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= sprintf(
            '<button type="submit" name="wp-submit" class="jankx-btn jankx-btn-primary" style="background: %s;">Đăng nhập</button>',
            esc_attr($brandColor)
        );

        wp_nonce_field('jankx_login_form', 'jankx_login_nonce');

        if ($showRememberMe || $showForgotPassword) {
            $output .= '<div class="jankx-login-options">';
            if ($showRememberMe) {
                $output .= '<label class="jankx-checkbox-label">';
                $output .= '<input type="checkbox" name="rememberme" value="forever" checked />';
                $output .= '<span class="jankx-checkbox-custom"></span>';
                $output .= 'Duy trì đăng nhập';
                $output .= '</label>';
            }
            if ($showForgotPassword) {
                $output .= '<a href="' . esc_url($forgotPasswordUrl ? $forgotPasswordUrl : wp_lostpassword_url()) . '" class="jankx-forgot-password" style="color: ' . esc_attr($brandColor) . ';">Quên mật khẩu</a>';
            }
            $output .= '</div>';
        }

        $output .= '</form>';

        if ($showFacebookLogin || $showGoogleLogin) {
            $output .= '<div class="jankx-social-divider">';
            $output .= '<span>Hoặc</span>';
            $output .= '</div>';

            if ($showFacebookLogin) {
                $output .= '<a href="#" class="jankx-btn jankx-btn-facebook">';
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
                $output .= 'Đăng nhập nhanh với Facebook';
                $output .= '</a>';
            }

            if ($showGoogleLogin) {
                $output .= '<a href="#" class="jankx-btn jankx-btn-google">';
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>';
                $output .= 'Đăng nhập nhanh với Google';
                $output .= '</a>';
            }
        }

        if ($contactUrl) {
            $output .= '<div class="jankx-login-help">';
            $output .= 'Cần giúp đỡ? <a href="' . esc_url($contactUrl) . '" style="color: ' . esc_attr($brandColor) . ';">Liên hệ với chúng tôi</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    protected function renderLoggedInState(string $brandColor): string
    {
        $output = '<div class="jankx-login-form-wrapper">';
        $output .= '<div class="jankx-logged-in-message">';
        $output .= '<p>Bạn đã đăng nhập.</p>';
        $output .= sprintf(
            '<a href="%s" class="jankx-btn jankx-btn-primary" style="background: %s;">Về trang chủ</a>',
            esc_url(home_url('/')),
            esc_attr($brandColor)
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
