<?php
namespace Jankx\Extensions\CustomLoginPage\Blocks;

class LoginPageBlock
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
        $bgImage = $attributes['backgroundImage'] ?? '';
        $pageType = $attributes['pageType'] ?? 'login';
        $brandName = $attributes['brandName'] ?? 'NOBITOUR';
        $brandColor = $attributes['brandColor'] ?? '#65A30D';

        $wrapperAttrs = get_block_wrapper_attributes([
            'class' => 'jankx-login-page-wrapper',
            'data-page-type' => $pageType,
        ]);

        $bgStyle = '';
        if ($bgImage) {
            $bgStyle = sprintf('background-image: url("%s");', esc_url($bgImage));
        }

        $output = sprintf('<div %s>', $wrapperAttrs);

        $output .= '<div class="jankx-login-page-bg" style="' . esc_attr($bgStyle) . '">';
        $output .= '<div class="jankx-login-page-bg-overlay"></div>';
        $output .= '</div>';

        $output .= '<div class="jankx-login-page-content">';
        $output .= '<div class="jankx-login-page-form-area">';

        $output .= '<div class="jankx-login-page-brand">';
        $output .= sprintf(
            '<span class="jankx-brand-name" style="color: %s;">%s</span>',
            esc_attr($brandName),
            esc_html($brandName)
        );
        $output .= '</div>';

        $output .= $content;

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    protected function resolveBlockPath(): string
    {
        return dirname(__DIR__, 2) . '/blocks/login-page';
    }
}
