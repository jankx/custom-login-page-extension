/**
 * webpack.config.js – custom-login-page extension
 *
 * Extends the default @wordpress/scripts webpack config to build all
 * Gutenberg blocks inside this extension.
 *
 * Build:  npm run build
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const EXTENSION_DIR = __dirname;

const filteredPlugins = (defaultConfig.plugins || []).filter((plugin) => {
    const name = plugin.constructor?.name ?? '';
    return name !== 'CopyPlugin' && name !== 'CleanWebpackPlugin';
});

module.exports = {
    ...defaultConfig,
    context: EXTENSION_DIR,

    entry: {
        'blocks/login-page/build/index': './blocks/login-page/src/index.tsx',
        'blocks/login-form/build/index': './blocks/login-form/src/index.tsx',
        'blocks/register-form/build/index': './blocks/register-form/src/index.tsx',
    },

    output: {
        ...defaultConfig.output,
        path: EXTENSION_DIR,
        filename: '[name].js',
        clean: false,
    },

    plugins: filteredPlugins,
};
