<?php
namespace Jankx\Extensions\CustomLoginPage\Blocks;

class RegisterFormBlock
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
        $showPhoneField = $attributes['showPhoneField'] ?? true;
        $showTermsCheckbox = $attributes['showTermsCheckbox'] ?? true;
        $termsPageUrl = $attributes['termsPageUrl'] ?? '';
        $loginPageUrl = $attributes['loginPageUrl'] ?? '';
        $contactUrl = $attributes['contactUrl'] ?? '';

        if (is_user_logged_in()) {
            return $this->renderLoggedInState($brandColor);
        }

        $output = '<div class="jankx-register-form-wrapper">';

        $output .= '<h2 class="jankx-register-title">Đăng ký</h2>';

        $output .= '<p class="jankx-register-subtitle">';
        $output .= 'Bạn đã có tài khoản? ';
        if ($loginPageUrl) {
            $output .= sprintf(
                '<a href="%s" style="color: %s;">Đăng nhập</a>',
                esc_url($loginPageUrl),
                esc_attr($brandColor)
            );
        } else {
            $output .= sprintf(
                '<a href="%s" style="color: %s;">Đăng nhập</a>',
                esc_url(wp_login_url()),
                esc_attr($brandColor)
            );
        }
        $output .= '</p>';

        $output .= '<form name="registerform" id="jankx-registerform" action="' . esc_url(home_url('/wp-login.php?action=register')) . '" method="post">';
        $output .= '<input type="hidden" name="redirect_to" value="' . esc_url(home_url('/')) . '" />';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_name">Họ và tên</label>';
        $output .= '<input type="text" name="user_name" id="reg_name" class="jankx-form-control" placeholder="Nhập họ tên" autocomplete="name" required />';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_email">Email</label>';
        $output .= '<input type="email" name="user_email" id="reg_email" class="jankx-form-control" placeholder="Nhập địa chỉ email" autocomplete="email" required />';
        $output .= '</div>';

        if ($showPhoneField) {
            $output .= '<div class="jankx-form-group">';
            $output .= '<label for="reg_phone">Số điện thoại</label>';
            $output .= '<input type="tel" name="user_phone" id="reg_phone" class="jankx-form-control" placeholder="Nhập số điện thoại" autocomplete="tel" />';
            $output .= '</div>';
        }

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_pass">Mật khẩu</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="user_pass" id="reg_pass" class="jankx-form-control" placeholder="Nhập mật khẩu" autocomplete="new-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_pass2">Nhập lại mật khẩu</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="user_pass2" id="reg_pass2" class="jankx-form-control" placeholder="Nhập mật khẩu" autocomplete="new-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= sprintf(
            '<button type="submit" name="wp-submit" class="jankx-btn jankx-btn-primary" style="background: %s;">Đăng ký</button>',
            esc_attr($brandColor)
        );

        wp_nonce_field('user-register');

        if ($showTermsCheckbox) {
            $output .= '<div class="jankx-terms-checkbox">';
            $output .= '<label class="jankx-checkbox-label">';
            $output .= '<input type="checkbox" name="agree_terms" value="1" required />';
            $output .= '<span class="jankx-checkbox-custom"></span>';
            $output .= 'Tôi đã đọc và đồng ý với ';
            if ($termsPageUrl) {
                $output .= '<a href="' . esc_url($termsPageUrl) . '" style="color: ' . esc_attr($brandColor) . ';">điều khoản dịch vụ</a>';
            } else {
                $output .= 'điều khoản dịch vụ';
            }
            $output .= '</label>';
            $output .= '</div>';
        }

        $output .= '</form>';

        if ($contactUrl) {
            $output .= '<div class="jankx-register-help">';
            $output .= 'Cần giúp đỡ? <a href="' . esc_url($contactUrl) . '" style="color: ' . esc_attr($brandColor) . ';">Liên hệ với chúng tôi</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    protected function renderLoggedInState(string $brandColor): string
    {
        $output = '<div class="jankx-register-form-wrapper">';
        $output .= '<div class="jankx-logged-in-message">';
        $output .= '<p>Bạn đã đăng nhập.</p>';
        $output .= sprintf(
            '<a href="%s" class="jankx-btn jankx-btn-primary" style="background: %s;">Vào trang quản trị</a>',
            esc_url(admin_url()),
            esc_attr($brandColor)
        );
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    protected function resolveBlockPath(): string
    {
        return dirname(__DIR__, 2) . '/blocks/register-form';
    }
}
