import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: 'jankx-register-form-editor',
    });

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Form settings', 'jankx')} initialOpen={true}>
                    <TextControl
                        label={__('Title', 'jankx')}
                        value={attributes.registerTitle}
                        onChange={(val) => setAttributes({ registerTitle: val })}
                    />
                    <TextControl
                        label={__('Subtitle prefix', 'jankx')}
                        value={attributes.registerSubtitle}
                        onChange={(val) => setAttributes({ registerSubtitle: val })}
                    />
                    <TextControl
                        label={__('Subtitle link text', 'jankx')}
                        value={attributes.registerSubtitleLinkText}
                        onChange={(val) => setAttributes({ registerSubtitleLinkText: val })}
                    />
                    <TextControl
                        label={__('Button text', 'jankx')}
                        value={attributes.registerButtonText}
                        onChange={(val) => setAttributes({ registerButtonText: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Field labels', 'jankx')} initialOpen={false}>
                    <TextControl
                        label={__('Name label', 'jankx')}
                        value={attributes.nameLabel}
                        onChange={(val) => setAttributes({ nameLabel: val })}
                    />
                    <TextControl
                        label={__('Name placeholder', 'jankx')}
                        value={attributes.namePlaceholder}
                        onChange={(val) => setAttributes({ namePlaceholder: val })}
                    />
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
                        label={__('Phone label', 'jankx')}
                        value={attributes.phoneLabel}
                        onChange={(val) => setAttributes({ phoneLabel: val })}
                    />
                    <TextControl
                        label={__('Phone placeholder', 'jankx')}
                        value={attributes.phonePlaceholder}
                        onChange={(val) => setAttributes({ phonePlaceholder: val })}
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
                    <TextControl
                        label={__('Confirm password label', 'jankx')}
                        value={attributes.confirmPasswordLabel}
                        onChange={(val) => setAttributes({ confirmPasswordLabel: val })}
                    />
                    <TextControl
                        label={__('Confirm password placeholder', 'jankx')}
                        value={attributes.confirmPasswordPlaceholder}
                        onChange={(val) => setAttributes({ confirmPasswordPlaceholder: val })}
                    />
                </PanelBody>

                <PanelBody title={__('Options', 'jankx')} initialOpen={false}>
                    <ToggleControl
                        label={__('Show phone field', 'jankx')}
                        checked={attributes.showPhoneField}
                        onChange={(val) => setAttributes({ showPhoneField: val })}
                    />
                    <ToggleControl
                        label={__('Show terms checkbox', 'jankx')}
                        checked={attributes.showTermsCheckbox}
                        onChange={(val) => setAttributes({ showTermsCheckbox: val })}
                    />
                    <TextControl
                        label={__('Terms text', 'jankx')}
                        value={attributes.termsText}
                        onChange={(val) => setAttributes({ termsText: val })}
                    />
                    <TextControl
                        label={__('Terms link text', 'jankx')}
                        value={attributes.termsLinkText}
                        onChange={(val) => setAttributes({ termsLinkText: val })}
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
                        label={__('Login page URL', 'jankx')}
                        value={attributes.loginPageUrl}
                        onChange={(val) => setAttributes({ loginPageUrl: val })}
                    />
                    <TextControl
                        label={__('Terms page URL', 'jankx')}
                        value={attributes.termsPageUrl}
                        onChange={(val) => setAttributes({ termsPageUrl: val })}
                    />
                    <TextControl
                        label={__('Contact URL', 'jankx')}
                        value={attributes.contactUrl}
                        onChange={(val) => setAttributes({ contactUrl: val })}
                    />
                </PanelBody>
            </InspectorControls>

            <div className="jankx-editor-form-placeholder">
                <h3>{attributes.registerTitle || 'Đăng ký'}</h3>
                <p>Form đăng ký sẽ hiển thị ở đây trên trang thực tế.</p>
                <div className="jankx-editor-form-mock">
                    <div className="jankx-mock-field">
                        <label>{attributes.nameLabel || 'Họ và tên'}</label>
                        <input type="text" disabled placeholder={attributes.namePlaceholder || 'Nhập họ tên'} />
                    </div>
                    <div className="jankx-mock-field">
                        <label>{attributes.emailLabel || 'Email'}</label>
                        <input type="email" disabled placeholder={attributes.emailPlaceholder || 'Nhập địa chỉ email'} />
                    </div>
                    {attributes.showPhoneField && (
                        <div className="jankx-mock-field">
                            <label>{attributes.phoneLabel || 'Số điện thoại'}</label>
                            <input type="tel" disabled placeholder={attributes.phonePlaceholder || 'Nhập số điện thoại'} />
                        </div>
                    )}
                    <div className="jankx-mock-field">
                        <label>{attributes.passwordLabel || 'Mật khẩu'}</label>
                        <input type="password" disabled placeholder={attributes.passwordPlaceholder || 'Nhập mật khẩu'} />
                    </div>
                    <div className="jankx-mock-field">
                        <label>{attributes.confirmPasswordLabel || 'Nhập lại mật khẩu'}</label>
                        <input type="password" disabled placeholder={attributes.confirmPasswordPlaceholder || 'Nhập mật khẩu'} />
                    </div>
                    <div className="jankx-mock-btn" style={{ background: attributes.brandColor }}>
                        {attributes.registerButtonText || 'Đăng ký'}
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
