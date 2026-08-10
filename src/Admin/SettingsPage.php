<?php
namespace Jankx\Extensions\CustomLoginPage\Admin;

use Jankx\Extensions\CustomLoginPage\CustomLoginPageExtension;

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

        register_setting(self::OPTION_GROUP, 'jankx_custom_login_page', [
            'default' => 0,
            'sanitize_callback' => 'absint',
        ]);

        register_setting(self::OPTION_GROUP, 'jankx_custom_register_page', [
            'default' => 0,
            'sanitize_callback' => 'absint',
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

        // Handle manual page creation
        if (isset($_POST['jankx_create_login_pages']) && check_admin_referer('jankx_create_login_pages')) {
            $extension = CustomLoginPageExtension::get_instance();
            if ($extension) {
                $extension->createPages();
                echo '<div class="updated"><p>' . esc_html__('Pages created successfully!', 'jankx') . '</p></div>';
            }
        }

        $loginPageId = get_option(CustomLoginPageExtension::LOGIN_PAGE_OPTION, 0);
        $registerPageId = get_option(CustomLoginPageExtension::REGISTER_PAGE_OPTION, 0);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Custom Login Page Settings', 'jankx'); ?></h1>
            <p class="description"><?php esc_html_e('Tùy chỉnh giao diện trang đăng nhập và đăng ký WordPress.', 'jankx'); ?></p>

            <!-- Quick Setup Section -->
            <div style="margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #e2e4e7; border-radius: 8px; max-width: 700px;">
                <h2 style="margin-top: 0;"><?php esc_html_e('Quick Setup', 'jankx'); ?></h2>
                <p><?php esc_html_e('Tạo nhanh 2 trang Đăng nhập và Đăng ký với Gutenberg blocks.', 'jankx'); ?></p>

                <?php if ($loginPageId && $registerPageId): ?>
                    <div class="notice notice-success inline">
                        <p><strong><?php esc_html_e('Pages already exist:', 'jankx'); ?></strong></p>
                        <ul style="margin: 10px 0; list-style: disc; padding-left: 20px;">
                            <li>
                                <?php esc_html_e('Login:', 'jankx'); ?>
                                <a href="<?php echo esc_url(get_edit_post_link($loginPageId)); ?>" target="_blank">
                                    <?php echo esc_html(get_the_title($loginPageId)); ?>
                                </a>
                                (<a href="<?php echo esc_url(get_permalink($loginPageId)); ?>" target="_blank"><?php esc_html_e('View', 'jankx'); ?></a>)
                            </li>
                            <li>
                                <?php esc_html_e('Register:', 'jankx'); ?>
                                <a href="<?php echo esc_url(get_edit_post_link($registerPageId)); ?>" target="_blank">
                                    <?php echo esc_html(get_the_title($registerPageId)); ?>
                                </a>
                                (<a href="<?php echo esc_url(get_permalink($registerPageId)); ?>" target="_blank"><?php esc_html_e('View', 'jankx'); ?></a>)
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <form method="post">
                        <?php wp_nonce_field('jankx_create_login_pages'); ?>
                        <p>
                            <button type="submit" name="jankx_create_login_pages" class="button button-primary">
                                <?php esc_html_e('Create Login & Register Pages', 'jankx'); ?>
                            </button>
                        </p>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Settings Form -->
            <form method="post" action="options.php" style="max-width: 700px; margin-top: 20px;">
                <?php settings_fields(self::OPTION_GROUP); ?>

                <h2><?php esc_html_e('Page Settings', 'jankx'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="jankx_custom_login_page"><?php esc_html_e('Login Page', 'jankx'); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_pages([
                                'name' => 'jankx_custom_login_page',
                                'id' => 'jankx_custom_login_page',
                                'selected' => get_option('jankx_custom_login_page', 0),
                                'show_option_none' => __('— Select —', 'jankx'),
                                'option_none_value' => 0,
                            ]);
                            ?>
                            <p class="description"><?php esc_html_e('Chọn trang hiển thị form đăng nhập.', 'jankx'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="jankx_custom_register_page"><?php esc_html_e('Register Page', 'jankx'); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_pages([
                                'name' => 'jankx_custom_register_page',
                                'id' => 'jankx_custom_register_page',
                                'selected' => get_option('jankx_custom_register_page', 0),
                                'show_option_none' => __('— Select —', 'jankx'),
                                'option_none_value' => 0,
                            ]);
                            ?>
                            <p class="description"><?php esc_html_e('Chọn trang hiển thị form đăng ký.', 'jankx'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Appearance Settings', 'jankx'); ?></h2>

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
                    <?php if ($loginPageId): ?>
                        <a href="<?php echo esc_url(get_permalink($loginPageId)); ?>"
                           target="_blank"
                           class="button button-primary">
                            <?php esc_html_e('View Login Page', 'jankx'); ?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo esc_url(wp_login_url()); ?>"
                           target="_blank"
                           class="button button-primary">
                            <?php esc_html_e('View Login Page (WP Default)', 'jankx'); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($registerPageId): ?>
                        <a href="<?php echo esc_url(get_permalink($registerPageId)); ?>"
                           target="_blank"
                           class="button">
                            <?php esc_html_e('View Register Page', 'jankx'); ?>
                        </a>
                    <?php endif; ?>
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
