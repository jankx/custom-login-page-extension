import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({
        className: 'jankx-register-form-editor',
    });

    return (
        <div {...blockProps}>
            <div className="jankx-editor-form-placeholder">
                <h3>Đăng ký</h3>
                <p>Form đăng ký sẽ hiển thị ở đây trên trang thực tế.</p>
                <div className="jankx-editor-form-mock">
                    <div className="jankx-mock-field">
                        <label>Họ và tên</label>
                        <input type="text" disabled placeholder="Nhập họ tên" />
                    </div>
                    <div className="jankx-mock-field">
                        <label>Email</label>
                        <input type="email" disabled placeholder="Nhập địa chỉ email" />
                    </div>
                    {attributes.showPhoneField && (
                        <div className="jankx-mock-field">
                            <label>Số điện thoại</label>
                            <input type="tel" disabled placeholder="Nhập số điện thoại" />
                        </div>
                    )}
                    <div className="jankx-mock-field">
                        <label>Mật khẩu</label>
                        <input type="password" disabled placeholder="Nhập mật khẩu" />
                    </div>
                    <div className="jankx-mock-field">
                        <label>Nhập lại mật khẩu</label>
                        <input type="password" disabled placeholder="Nhập mật khẩu" />
                    </div>
                    <div className="jankx-mock-btn" style={{ background: attributes.brandColor }}>
                        Đăng ký
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
