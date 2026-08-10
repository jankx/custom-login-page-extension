import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: 'jankx-login-form-editor',
    });

    return (
        <div {...blockProps}>
            <div className="jankx-editor-form-placeholder">
                <h3>Đăng nhập</h3>
                <p>Form đăng nhập sẽ hiển thị ở đây trên trang thực tế.</p>
                <div className="jankx-editor-form-mock">
                    <div className="jankx-mock-field">
                        <label>Email/Tài khoản</label>
                        <input type="text" disabled placeholder="Nhập email hoặc số điện thoại" />
                    </div>
                    <div className="jankx-mock-field">
                        <label>Mật khẩu</label>
                        <input type="password" disabled placeholder="Nhập mật khẩu" />
                    </div>
                    <div className="jankx-mock-btn" style={{ background: attributes.brandColor }}>
                        Đăng nhập
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
