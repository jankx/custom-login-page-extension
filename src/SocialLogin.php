<?php
namespace Jankx\Extensions\CustomLoginPage;

class SocialLogin
{
    const OPTION_PREFIX = 'jankx_social_';

    const FACEBOOK_AUTH_URL = 'https://www.facebook.com/v18.0/dialog/oauth';
    const FACEBOOK_TOKEN_URL = 'https://graph.facebook.com/v18.0/oauth/access_token';
    const FACEBOOK_USER_URL = 'https://graph.facebook.com/v18.0/me';

    const GOOGLE_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    const GOOGLE_USER_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';

    public function register(): void
    {
        add_action('init', [$this, 'addRewriteRules']);
        add_action('template_redirect', [$this, 'handleCallback']);
    }

    public function addRewriteRules(): void
    {
        add_rewrite_endpoint('social-login', EP_ROOT);
    }

    public function handleCallback(): void
    {
        $action = get_query_var('social-login');
        if (empty($action)) {
            return;
        }

        $provider = sanitize_text_field($action);

        if ($provider === 'facebook') {
            $this->handleFacebookCallback();
        } elseif ($provider === 'google') {
            $this->handleGoogleCallback();
        }
    }

    public function getFacebookLoginUrl(): string
    {
        $appId = $this->getOption('facebook_app_id');
        if (empty($appId)) {
            return '#';
        }

        $redirectUri = home_url('/social-login/facebook');
        $state = wp_create_nonce('jankx_social_login');

        return add_query_arg([
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => 'email,public_profile',
        ], self::FACEBOOK_AUTH_URL);
    }

    public function getGoogleLoginUrl(): string
    {
        $clientId = $this->getOption('google_client_id');
        if (empty($clientId)) {
            return '#';
        }

        $redirectUri = home_url('/social-login/google');
        $state = wp_create_nonce('jankx_social_login');

        return add_query_arg([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => 'openid email profile',
            'response_type' => 'code',
        ], self::GOOGLE_AUTH_URL);
    }

    protected function handleFacebookCallback(): void
    {
        if (is_wp_error($this->verifyState())) {
            wp_die('Security verification failed.');
        }

        $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
        if (empty($code)) {
            $this->redirectWithError('Facebook login failed. No code received.');
            return;
        }

        $appId = $this->getOption('facebook_app_id');
        $appSecret = $this->getOption('facebook_app_secret');
        $redirectUri = home_url('/social-login/facebook');

        // Get access token
        $tokenUrl = add_query_arg([
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ], self::FACEBOOK_TOKEN_URL);

        $response = wp_remote_get($tokenUrl);
        if (is_wp_error($response)) {
            $this->redirectWithError('Failed to get access token.');
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $accessToken = $body['access_token'] ?? '';

        if (empty($accessToken)) {
            $this->redirectWithError('Failed to get access token.');
            return;
        }

        // Get user info
        $userUrl = add_query_arg([
            'fields' => 'id,name,email,first_name,last_name',
            'access_token' => $accessToken,
        ], self::FACEBOOK_USER_URL);

        $response = wp_remote_get($userUrl);
        if (is_wp_error($response)) {
            $this->redirectWithError('Failed to get user info.');
            return;
        }

        $userData = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($userData['email'])) {
            $this->redirectWithError('Could not get email from Facebook. Please grant email permission.');
            return;
        }

        $this->loginOrRegisterUser([
            'email' => $userData['email'],
            'name' => $userData['name'] ?? $userData['first_name'] . ' ' . $userData['last_name'],
            'provider' => 'facebook',
            'provider_id' => $userData['id'],
        ]);
    }

    protected function handleGoogleCallback(): void
    {
        if (is_wp_error($this->verifyState())) {
            wp_die('Security verification failed.');
        }

        $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
        if (empty($code)) {
            $this->redirectWithError('Google login failed. No code received.');
            return;
        }

        $clientId = $this->getOption('google_client_id');
        $clientSecret = $this->getOption('google_client_secret');
        $redirectUri = home_url('/social-login/google');

        // Get access token
        $response = wp_remote_post(self::GOOGLE_TOKEN_URL, [
            'body' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ],
        ]);

        if (is_wp_error($response)) {
            $this->redirectWithError('Failed to get access token.');
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $accessToken = $body['access_token'] ?? '';

        if (empty($accessToken)) {
            $this->redirectWithError('Failed to get access token.');
            return;
        }

        // Get user info
        $response = wp_remote_get(self::GOOGLE_USER_URL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if (is_wp_error($response)) {
            $this->redirectWithError('Failed to get user info.');
            return;
        }

        $userData = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($userData['email'])) {
            $this->redirectWithError('Could not get email from Google.');
            return;
        }

        $this->loginOrRegisterUser([
            'email' => $userData['email'],
            'name' => $userData['name'] ?? '',
            'provider' => 'google',
            'provider_id' => $userData['id'],
        ]);
    }

    protected function loginOrRegisterUser(array $data): void
    {
        $email = $data['email'];
        $name = $data['name'];
        $provider = $data['provider'];
        $providerId = $data['provider_id'];

        // Check if user exists by email
        $user = get_user_by('email', $email);

        if (!$user) {
            // Check if registration is allowed
            if (!get_option('users_can_register')) {
                $this->redirectWithError('Registration is not enabled. Please contact administrator.');
                return;
            }

            // Create new user
            $username = sanitize_user(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)));
            if (empty($username)) {
                $username = 'user_' . $providerId;
            }

            // Ensure unique username
            $originalUsername = $username;
            $counter = 1;
            while (username_exists($username)) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            $userId = wp_create_user($username, wp_generate_password(), $email);

            if (is_wp_error($userId)) {
                $this->redirectWithError('Failed to create account: ' . $userId->get_error_message());
                return;
            }

            $user = get_userdata($userId);

            // Update user meta
            update_user_meta($user->ID, 'first_name', explode(' ', $name)[0] ?? '');
            update_user_meta($user->ID, 'last_name', implode(' ', array_slice(explode(' ', $name), 1)) ?? '');
            update_user_meta($user->ID, 'jankx_social_provider', $provider);
            update_user_meta($user->ID, 'jankx_social_provider_id', $providerId);

            // Send new user notification
            wp_send_new_user_notifications($user->ID);
        } else {
            // Update social provider meta
            update_user_meta($user->ID, 'jankx_social_provider', $provider);
            update_user_meta($user->ID, 'jankx_social_provider_id', $providerId);
        }

        // Login user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);

        do_action('jankx_social_login_success', $user, $data);

        // Redirect to homepage
        wp_redirect(home_url('/'));
        exit;
    }

    protected function verifyState()
    {
        $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
        return wp_verify_nonce($state, 'jankx_social_login');
    }

    protected function redirectWithError(string $message): void
    {
        $loginUrl = home_url('/dang-nhap/');
        wp_redirect(add_query_arg('social_error', urlencode($message), $loginUrl));
        exit;
    }

    public function getSocialErrorMessage(): string
    {
        return isset($_GET['social_error']) ? sanitize_text_field(urldecode($_GET['social_error'])) : '';
    }

    protected function getOption(string $key, string $default = ''): string
    {
        return get_option(self::OPTION_PREFIX . $key, $default);
    }
}
