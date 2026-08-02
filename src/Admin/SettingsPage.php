<?php
namespace Jankx\Extensions\CustomLoginPage\Admin;

class SettingsPage
{
    const PAGE_SLUG = 'jankx-custom-login';
    const OPTION_GROUP = 'jankx_login_settings';

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenu'], 25);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'jankx-theme-options',
            __('Custom Login Page', 'jankx'),
            __('Login Page', 'jankx'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderPage']
        );
    }

    public function registerSettings(): void
    {
        register_setting(self::OPTION_GROUP, 'jankx_login_logo_url', [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_login_primary_color', [
            'default' => '#65A30D',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_login_bg_color', [
            'default' => '#FFFEF5',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_login_text_color', [
            'default' => '#1A1F71',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_login_page_title', [
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_login_footer_text', [
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        ]);
    }

    public function enqueueAssets(string $hook): void
    {
        if (strpos($hook, self::PAGE_SLUG) === false) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Custom Login Page Settings', 'jankx'); ?></h1>
            <p class="description"><?php esc_html_e('Tùy chỉnh giao diện trang đăng nhập WordPress.', 'jankx'); ?></p>

            <form method="post" action="options.php" style="max-width: 700px; margin-top: 20px;">
                <?php settings_fields(self::OPTION_GROUP); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="jankx_login_logo_url"><?php esc_html_e('Logo URL', 'jankx'); ?></label>
                        </th>
                        <td>
                            <input type="url"
                                   id="jankx_login_logo_url"
                                   name="jankx_login_logo_url"
                                   value="<?php echo esc_attr(get_option('jankx_login_logo_url', '')); ?>"
                                   class="regular-text"
                                   placeholder="https://example.com/logo.png">
                            <button type="button" class="button" id="jankx-upload-logo"><?php esc_html_e('Upload', 'jankx'); ?></button>
                            <p class="description"><?php esc_html_e('Để trống sẽ dùng logo mặc định của WordPress.', 'jankx'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_login_primary_color"><?php esc_html_e('Primary Color', 'jankx'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="jankx_login_primary_color"
                                   name="jankx_login_primary_color"
                                   value="<?php echo esc_attr(get_option('jankx_login_primary_color', '#65A30D')); ?>"
                                   class="jankx-color-picker">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_login_bg_color"><?php esc_html_e('Background Color', 'jankx'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="jankx_login_bg_color"
                                   name="jankx_login_bg_color"
                                   value="<?php echo esc_attr(get_option('jankx_login_bg_color', '#FFFEF5')); ?>"
                                   class="jankx-color-picker">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_login_text_color"><?php esc_html_e('Text Color', 'jankx'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="jankx_login_text_color"
                                   name="jankx_login_text_color"
                                   value="<?php echo esc_attr(get_option('jankx_login_text_color', '#1A1F71')); ?>"
                                   class="jankx-color-picker">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_login_page_title"><?php esc_html_e('Page Title', 'jankx'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="jankx_login_page_title"
                                   name="jankx_login_page_title"
                                   value="<?php echo esc_attr(get_option('jankx_login_page_title', '')); ?>"
                                   class="regular-text"
                                   placeholder="Nobitour - Đăng nhập">
                            <p class="description"><?php esc_html_e('Tiêu đề hiển thị trên tab trình duyệt.', 'jankx'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_login_footer_text"><?php esc_html_e('Footer Text', 'jankx'); ?></label>
                        </th>
                        <td>
                            <textarea id="jankx_login_footer_text"
                                      name="jankx_login_footer_text"
                                      class="large-text"
                                      rows="3"
                                      placeholder="<?php esc_attr_e('© 2024 Nobitour. All rights reserved.', 'jankx'); ?>"><?php echo esc_textarea(get_option('jankx_login_footer_text', '')); ?></textarea>
                            <p class="description"><?php esc_html_e('Hiển thị dưới form đăng nhập. Hỗ trợ HTML.', 'jankx'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'jankx')); ?>
            </form>

            <div style="margin-top: 40px; padding: 20px; background: #fff; border: 1px solid #e2e4e7; border-radius: 8px; max-width: 700px;">
                <h2 style="margin-top: 0;"><?php esc_html_e('Preview', 'jankx'); ?></h2>
                <p>
                    <a href="<?php echo esc_url(wp_login_url()); ?>"
                       target="_blank"
                       class="button button-primary">
                        <?php esc_html_e('View Login Page', 'jankx'); ?>
                    </a>
                </p>
            </div>
        </div>

        <script>
        (function($) {
            $('.jankx-color-picker').wpColorPicker();

            $('#jankx-upload-logo').on('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: '<?php echo esc_js(__('Select Logo', 'jankx')); ?>',
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#jankx_login_logo_url').val(attachment.url);
                });

                frame.open();
            });
        })(jQuery);
        </script>
        <?php
    }
}
