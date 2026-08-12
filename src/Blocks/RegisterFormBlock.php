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

        // Text attributes
        $registerTitle = $attributes['registerTitle'] ?? 'Đăng ký';
        $registerSubtitle = $attributes['registerSubtitle'] ?? 'Bạn đã có tài khoản? ';
        $registerSubtitleLinkText = $attributes['registerSubtitleLinkText'] ?? 'Đăng nhập';
        $registerButtonText = $attributes['registerButtonText'] ?? 'Đăng ký';
        $nameLabel = $attributes['nameLabel'] ?? 'Họ và tên';
        $namePlaceholder = $attributes['namePlaceholder'] ?? 'Nhập họ tên';
        $emailLabel = $attributes['emailLabel'] ?? 'Email';
        $emailPlaceholder = $attributes['emailPlaceholder'] ?? 'Nhập địa chỉ email';
        $phoneLabel = $attributes['phoneLabel'] ?? 'Số điện thoại';
        $phonePlaceholder = $attributes['phonePlaceholder'] ?? 'Nhập số điện thoại';
        $passwordLabel = $attributes['passwordLabel'] ?? 'Mật khẩu';
        $passwordPlaceholder = $attributes['passwordPlaceholder'] ?? 'Nhập mật khẩu';
        $confirmPasswordLabel = $attributes['confirmPasswordLabel'] ?? 'Nhập lại mật khẩu';
        $confirmPasswordPlaceholder = $attributes['confirmPasswordPlaceholder'] ?? 'Nhập mật khẩu';
        $termsText = $attributes['termsText'] ?? 'Tôi đã đọc và đồng ý với ';
        $termsLinkText = $attributes['termsLinkText'] ?? 'điều khoản dịch vụ';
        $helpText = $attributes['helpText'] ?? 'Cần giúp đỡ? ';
        $helpLinkText = $attributes['helpLinkText'] ?? 'Liên hệ với chúng tôi';
        $loggedInMessage = $attributes['loggedInMessage'] ?? 'Bạn đã đăng nhập.';
        $loggedInButtonText = $attributes['loggedInButtonText'] ?? 'Vào trang quản trị';

        if (is_user_logged_in()) {
            return $this->renderLoggedInState($brandColor, $loggedInMessage, $loggedInButtonText);
        }

        $output = '<div class="jankx-register-form-wrapper">';

        $output .= '<h2 class="jankx-register-title">' . esc_html($registerTitle) . '</h2>';

        $output .= '<p class="jankx-register-subtitle">';
        $output .= esc_html($registerSubtitle);
        $loginUrl = $loginPageUrl ?: wp_login_url();
        $output .= sprintf(
            '<a href="%s" style="color: %s;">%s</a>',
            esc_url($loginUrl),
            esc_attr($brandColor),
            esc_html($registerSubtitleLinkText)
        );
        $output .= '</p>';

        $output .= '<form name="registerform" id="jankx-registerform" action="' . esc_url(home_url('/wp-login.php?action=register')) . '" method="post">';
        $output .= '<input type="hidden" name="redirect_to" value="' . esc_url(home_url('/')) . '" />';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_name">' . esc_html($nameLabel) . '</label>';
        $output .= '<input type="text" name="user_name" id="reg_name" class="jankx-form-control" placeholder="' . esc_attr($namePlaceholder) . '" autocomplete="name" required />';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_email">' . esc_html($emailLabel) . '</label>';
        $output .= '<input type="email" name="user_email" id="reg_email" class="jankx-form-control" placeholder="' . esc_attr($emailPlaceholder) . '" autocomplete="email" required />';
        $output .= '</div>';

        if ($showPhoneField) {
            $output .= '<div class="jankx-form-group">';
            $output .= '<label for="reg_phone">' . esc_html($phoneLabel) . '</label>';
            $output .= '<input type="tel" name="user_phone" id="reg_phone" class="jankx-form-control" placeholder="' . esc_attr($phonePlaceholder) . '" autocomplete="tel" />';
            $output .= '</div>';
        }

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_pass">' . esc_html($passwordLabel) . '</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="user_pass" id="reg_pass" class="jankx-form-control" placeholder="' . esc_attr($passwordPlaceholder) . '" autocomplete="new-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="jankx-form-group">';
        $output .= '<label for="reg_pass2">' . esc_html($confirmPasswordLabel) . '</label>';
        $output .= '<div class="jankx-password-wrapper">';
        $output .= '<input type="password" name="user_pass2" id="reg_pass2" class="jankx-form-control" placeholder="' . esc_attr($confirmPasswordPlaceholder) . '" autocomplete="new-password" required />';
        $output .= '<button type="button" class="jankx-password-toggle" onclick="jankxTogglePassword(this)">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= sprintf(
            '<button type="submit" name="wp-submit" class="jankx-btn jankx-btn-primary" style="background: %s;">%s</button>',
            esc_attr($brandColor),
            esc_html($registerButtonText)
        );

        wp_nonce_field('user-register');

        if ($showTermsCheckbox) {
            $output .= '<div class="jankx-terms-checkbox">';
            $output .= '<label class="jankx-checkbox-label">';
            $output .= '<input type="checkbox" name="agree_terms" value="1" required />';
            $output .= '<span class="jankx-checkbox-custom"></span>';
            $output .= esc_html($termsText);
            if ($termsPageUrl) {
                $output .= '<a href="' . esc_url($termsPageUrl) . '" style="color: ' . esc_attr($brandColor) . ';">' . esc_html($termsLinkText) . '</a>';
            } else {
                $output .= esc_html($termsLinkText);
            }
            $output .= '</label>';
            $output .= '</div>';
        }

        $output .= '</form>';

        if ($contactUrl) {
            $output .= '<div class="jankx-register-help">';
            $output .= esc_html($helpText) . ' <a href="' . esc_url($contactUrl) . '" style="color: ' . esc_attr($brandColor) . ';">' . esc_html($helpLinkText) . '</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    protected function renderLoggedInState(string $brandColor, string $loggedInMessage, string $loggedInButtonText): string
    {
        $output = '<div class="jankx-register-form-wrapper">';
        $output .= '<div class="jankx-logged-in-message">';
        $output .= '<p>' . esc_html($loggedInMessage) . '</p>';
        $output .= sprintf(
            '<a href="%s" class="jankx-btn jankx-btn-primary" style="background: %s;">%s</a>',
            esc_url(admin_url()),
            esc_attr($brandColor),
            esc_html($loggedInButtonText)
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
