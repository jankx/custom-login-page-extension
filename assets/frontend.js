/**
 * Nobitour Custom Login Page - Frontend Scripts
 */
(function() {
    'use strict';

    // Password toggle functionality
    window.jankxTogglePassword = function(button) {
        var wrapper = button.closest('.jankx-password-wrapper');
        var input = wrapper.querySelector('.jankx-form-control');
        var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);

        // Update icon
        var svg = button.querySelector('svg');
        if (type === 'text') {
            svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    };

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        // Login form
        var loginForm = document.getElementById('jankx-loginform');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                var email = document.getElementById('user_login');
                var password = document.getElementById('user_pass');

                if (!email.value.trim()) {
                    e.preventDefault();
                    showFormError(email, 'Vui lòng nhập email hoặc tài khoản');
                    return;
                }

                if (!password.value.trim()) {
                    e.preventDefault();
                    showFormError(password, 'Vui lòng nhập mật khẩu');
                    return;
                }
            });
        }

        // Register form
        var registerForm = document.getElementById('jankx-registerform');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                var name = document.getElementById('reg_name');
                var email = document.getElementById('reg_email');
                var password = document.getElementById('reg_pass');
                var password2 = document.getElementById('reg_pass2');
                var terms = document.querySelector('input[name="agree_terms"]');

                if (!name.value.trim()) {
                    e.preventDefault();
                    showFormError(name, 'Vui lòng nhập họ và tên');
                    return;
                }

                if (!email.value.trim() || !isValidEmail(email.value)) {
                    e.preventDefault();
                    showFormError(email, 'Vui lòng nhập địa chỉ email hợp lệ');
                    return;
                }

                if (!password.value.trim()) {
                    e.preventDefault();
                    showFormError(password, 'Vui lòng nhập mật khẩu');
                    return;
                }

                if (password.value.length < 6) {
                    e.preventDefault();
                    showFormError(password, 'Mật khẩu phải có ít nhất 6 ký tự');
                    return;
                }

                if (password.value !== password2.value) {
                    e.preventDefault();
                    showFormError(password2, 'Mật khẩu không khớp');
                    return;
                }

                if (terms && !terms.checked) {
                    e.preventDefault();
                    showFormError(terms, 'Vui lòng đồng ý với điều khoản dịch vụ');
                    return;
                }
            });
        }
    });

    function showFormError(field, message) {
        // Remove existing error
        var existingError = field.closest('.jankx-form-group').querySelector('.jankx-form-error');
        if (existingError) {
            existingError.remove();
        }

        // Add error
        var errorDiv = document.createElement('div');
        errorDiv.className = 'jankx-form-error';
        errorDiv.textContent = message;
        field.closest('.jankx-form-group').appendChild(errorDiv);

        // Focus field
        field.focus();

        // Remove error after 3 seconds
        setTimeout(function() {
            errorDiv.remove();
        }, 3000);
    }

    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
})();
