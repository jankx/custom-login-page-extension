<?php
namespace Jankx\Extensions\CustomLoginPage;

class LoginCustomizer
{
    const OPTION_PREFIX = 'jankx_login_';

    public function register(): void
    {
        add_action('login_enqueue_scripts', [$this, 'enqueueAssets'], 5);
        add_action('login_head', [$this, 'addCustomStyles'], 99);
        add_filter('login_headerurl', [$this, 'setLogoUrl']);
        add_filter('login_headertext', [$this, 'setLogoTitle']);
        add_filter('login_title', [$this, 'setLoginPageTitle']);
        add_action('login_footer', [$this, 'renderCustomFooter']);
    }

    public function enqueueAssets(): void
    {
        $extension = CustomLoginPageExtension::get_instance();
        if (!$extension) {
            return;
        }

        wp_enqueue_style(
            'jankx-custom-login',
            $extension->get_extension_url() . '/assets/login.css',
            [],
            '1.0.0'
        );

        $logoUrl = $this->getLogoUrl();
        if ($logoUrl) {
            wp_add_inline_style('jankx-custom-login', sprintf(
                '.login h1 a { background-image: url("%s") !important; }',
                esc_url($logoUrl)
            ));
        }
    }

    public function addCustomStyles(): void
    {
        $primaryColor = $this->getOption('primary_color', '#65A30D');
        $bgColor = $this->getOption('bg_color', '#FFFEF5');
        $textColor = $this->getOption('text_color', '#1A1F71');

        printf(
            '<style>
                body { background-color: %s !important; }
                #login { width: 100%%; max-width: 400px; }
                .login h1 a { width: 320px !important; height: 80px !important; background-size: contain !important; background-position: center !important; }
                .wp-core-ui .button-primary { background: %s !important; border-color: %s !important; }
                .wp-core-ui .button-primary:hover { background: %s !important; border-color: %s !important; }
                #loginform { border-radius: 12px !important; box-shadow: 0 4px 24px rgba(0,0,0,0.08) !important; }
                .login .message { border-left: 4px solid %s !important; }
                .login #login_error { border-left: 4px solid #E00501 !important; }
                .login form .input, .login input[type="text"] { border-radius: 8px !important; }
            </style>',
            esc_attr($bgColor),
            esc_attr($primaryColor),
            esc_attr($primaryColor),
            esc_attr($this->darkenColor($primaryColor, 15)),
            esc_attr($this->darkenColor($primaryColor, 15)),
            esc_attr($primaryColor)
        );
    }

    public function setLogoUrl(): string
    {
        return home_url('/');
    }

    public function setLogoTitle(): string
    {
        return get_bloginfo('name');
    }

    public function setLoginPageTitle(string $title): string
    {
        return $this->getOption('page_title', sprintf('%s - Đăng nhập', get_bloginfo('name')));
    }

    public function renderCustomFooter(): void
    {
        $footerText = $this->getOption('footer_text', '');
        if (empty($footerText)) {
            return;
        }

        printf(
            '<div id="jankx-login-footer" style="text-align:center;margin-top:20px;color:#666;font-size:13px;">%s</div>',
            wp_kses_post($footerText)
        );
    }

    protected function getOption(string $key, string $default = ''): string
    {
        return get_option(self::OPTION_PREFIX . $key, $default);
    }

    protected function getLogoUrl(): string
    {
        return $this->getOption('logo_url', '');
    }

    protected function darkenColor(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = max(0, hexdec(substr($hex, 0, 2)) - intval(255 * $percent / 100));
        $g = max(0, hexdec(substr($hex, 2, 2)) - intval(255 * $percent / 100));
        $b = max(0, hexdec(substr($hex, 4, 2)) - intval(255 * $percent / 100));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
