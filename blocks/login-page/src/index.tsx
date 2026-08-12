import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { Button, TextControl, PanelBody, SelectControl, ColorPicker } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

function Save({ attributes }) {
    const blockProps = useBlockProps.save({
        className: 'jankx-login-page-wrapper',
    });

    return (
        <div {...blockProps} data-page-type={attributes.pageType || 'login'}>
            <InnerBlocks.Content />
        </div>
    );
}

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: 'jankx-login-page-editor',
    });

    const {
        backgroundImage,
        pageType,
        brandName,
        brandColor,
    } = attributes;

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Settings', 'jankx')} initialOpen={true}>
                    <SelectControl
                        label={__('Page Type', 'jankx')}
                        value={pageType}
                        options={[
                            { label: __('Login', 'jankx'), value: 'login' },
                            { label: __('Register', 'jankx'), value: 'register' },
                        ]}
                        onChange={(value) => setAttributes({ pageType: value })}
                    />

                    <TextControl
                        label={__('Brand Name', 'jankx')}
                        value={brandName}
                        onChange={(value) => setAttributes({ brandName: value })}
                    />

                    <div className="jankx-color-field">
                        <label>{__('Brand Color', 'jankx')}</label>
                        <ColorPicker
                            color={brandColor}
                            onChangeComplete={(value) => setAttributes({ brandColor: value.hex })}
                        />
                    </div>
                </PanelBody>

                <PanelBody title={__('Background Image', 'jankx')} initialOpen={false}>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(media) => setAttributes({ backgroundImage: media.url, backgroundImageId: media.id })}
                            allowedTypes={['image']}
                            value={attributes.backgroundImageId}
                            render={({ open }) => (
                                <Button
                                    onClick={open}
                                    variant="secondary"
                                    isPrimary
                                >
                                    {backgroundImage ? __('Change Image', 'jankx') : __('Select Image', 'jankx')}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                    {backgroundImage && (
                        <Button
                            onClick={() => setAttributes({ backgroundImage: '', backgroundImageId: 0 })}
                            isDestructive
                            variant="link"
                        >
                            {__('Remove Image', 'jankx')}
                        </Button>
                    )}
                </PanelBody>
            </InspectorControls>

            <div className="jankx-login-page-editor-preview">
                <div className="jankx-editor-bg-preview" style={backgroundImage ? { backgroundImage: `url(${backgroundImage})` } : {}}>
                    <div className="jankx-editor-bg-overlay"></div>
                </div>
                <div className="jankx-editor-form-preview">
                    <div className="jankx-editor-brand">
                        <span style={{ color: brandColor }}>{brandName || 'NOBITOUR'}</span>
                    </div>
                    {pageType === 'login' ? (
                        <div className="jankx-editor-form-placeholder">
                            <h3>Đăng nhập</h3>
                            <p>Form login sẽ hiển thị ở đây</p>
                        </div>
                    ) : (
                        <div className="jankx-editor-form-placeholder">
                            <h3>Đăng ký</h3>
                            <p>Form register sẽ hiển thị ở đây</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: Edit,
    save: Save,
});
