import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: 'jankx-login-form-editor',
    });

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Form settings', 'jankx')} initialOpen={true}>
                    <TextControl
                        label={__('Title', 'jankx')}
                        value={attributes.loginTitle}
                        onChange={(val) => setAttributes({ loginTitle: val })}
                    />
                    <TextControl
                        label={__('Subtitle prefix', 'jankx')}
                        value={attributes.loginSubtitle}
                        onChange={(val) => setAttributes({ loginSubtitle: val })}
                    />
                    <TextControl
                        label={__('Subtitle link text', 'jankx')}
                        value={attributes.loginSubtitleLinkText}
                        onChange={(val) => setAttributes({ loginSubtitleLinkText: val })}
                    />
                    <TextControl
                        label={__('Button text', 'jankx')}
                        value={attributes.loginButtonText}
                        onChange={(val) => setAttributes({ loginButtonText: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Field labels', 'jankx')} initialOpen={false}>
                    <TextControl
                        label={__('Email label', 'jankx')}
                        value={attributes.emailLabel}
                        onChange={(val) => setAttributes({ emailLabel: val })}
                    />
                    <TextControl
                        label={__('Email placeholder', 'jankx')}
                        value={attributes.emailPlaceholder}
                        onChange={(val) => setAttributes({ emailPlaceholder: val })}
                    />
                    <TextControl
                        label={__('Password label', 'jankx')}
                        value={attributes.passwordLabel}
                        onChange={(val) => setAttributes({ passwordLabel: val })}
                    />
                    <TextControl
                        label={__('Password placeholder', 'jankx')}
                        value={attributes.passwordPlaceholder}
                        onChange={(val) => setAttributes({ passwordPlaceholder: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Options', 'jankx')} initialOpen={false}>
                    <ToggleControl
                        label={__('Show "Remember me"', 'jankx')}
                        checked={attributes.showRememberMe}
                        onChange={(val) => setAttributes({ showRememberMe: val })}
                    />
                    <ToggleControl
                        label={__('Show "Forgot password"', 'jankx')}
                        checked={attributes.showForgotPassword}
                        onChange={(val) => setAttributes({ showForgotPassword: val })}
                    />
                    <TextControl
                        label={__('Remember me label', 'jankx')}
                        value={attributes.rememberMeLabel}
                        onChange={(val) => setAttributes({ rememberMeLabel: val })}
                    />
                    <TextControl
                        label={__('Forgot password label', 'jankx')}
                        value={attributes.forgotPasswordLabel}
                        onChange={(val) => setAttributes({ forgotPasswordLabel: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Social login', 'jankx')} initialOpen={false}>
                    <ToggleControl
                        label={__('Show Facebook login', 'jankx')}
                        checked={attributes.showFacebookLogin}
                        onChange={(val) => setAttributes({ showFacebookLogin: val })}
                    />
                    <ToggleControl
                        label={__('Show Google login', 'jankx')}
                        checked={attributes.showGoogleLogin}
                        onChange={(val) => setAttributes({ showGoogleLogin: val })}
                    />
                    <TextControl
                        label={__('Divider text', 'jankx')}
                        value={attributes.socialDividerText}
                        onChange={(val) => setAttributes({ socialDividerText: val })}
                    />
                    <TextControl
                        label={__('Facebook button text', 'jankx')}
                        value={attributes.facebookButtonText}
                        onChange={(val) => setAttributes({ facebookButtonText: val })}
                    />
                    <TextControl
                        label={__('Google button text', 'jankx')}
                        value={attributes.googleButtonText}
                        onChange={(val) => setAttributes({ googleButtonText: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Help & messages', 'jankx')} initialOpen={false}>
                    <TextControl
                        label={__('Help text', 'jankx')}
                        value={attributes.helpText}
                        onChange={(val) => setAttributes({ helpText: val })}
                    />
                    <TextControl
                        label={__('Help link text', 'jankx')}
                        value={attributes.helpLinkText}
                        onChange={(val) => setAttributes({ helpLinkText: val })}
                    />
                    <TextControl
                        label={__('Logged in message', 'jankx')}
                        value={attributes.loggedInMessage}
                        onChange={(val) => setAttributes({ loggedInMessage: val })}
                    />
                    <TextControl
                        label={__('Logged in button text', 'jankx')}
                        value={attributes.loggedInButtonText}
                        onChange={(val) => setAttributes({ loggedInButtonText: val })}
                    />
                </PanelBody>

                <PanelBody title={__('URLs', 'jankx')} initialOpen={false}>
                    <TextControl
                        label={__('Register page URL', 'jankx')}
                        value={attributes.registerPageUrl}
                        onChange={(val) => setAttributes({ registerPageUrl: val })}
                    />
                    <TextControl
                        label={__('Forgot password URL', 'jankx')}
                        value={attributes.forgotPasswordUrl}
                        onChange={(val) => setAttributes({ forgotPasswordUrl: val })}
                    />
                    <TextControl
                        label={__('Contact URL', 'jankx')}
                        value={attributes.contactUrl}
                        onChange={(val) => setAttributes({ contactUrl: val })}
                    />
                    <TextControl
                        label={__('Redirect URL', 'jankx')}
                        value={attributes.redirectUrl}
                        onChange={(val) => setAttributes({ redirectUrl: val })}
                    />
                </PanelBody>
            </InspectorControls>

            <div className="jankx-editor-form-placeholder">
                <h3>{attributes.loginTitle || 'Đăng nhập'}</h3>
                <p>Form đăng nhập sẽ hiển thị ở đây trên trang thực tế.</p>
                <div className="jankx-editor-form-mock">
                    <div className="jankx-mock-field">
                        <label>{attributes.emailLabel || 'Email/Tài khoản'}</label>
                        <input type="text" disabled placeholder={attributes.emailPlaceholder || 'Nhập email hoặc số điện thoại'} />
                    </div>
                    <div className="jankx-mock-field">
                        <label>{attributes.passwordLabel || 'Mật khẩu'}</label>
                        <input type="password" disabled placeholder={attributes.passwordPlaceholder || 'Nhập mật khẩu'} />
                    </div>
                    <div className="jankx-mock-btn" style={{ background: attributes.brandColor }}>
                        {attributes.loginButtonText || 'Đăng nhập'}
                    </div>
                </div>
            </div>
        </div>
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: Edit,
    save: () => null,
});
