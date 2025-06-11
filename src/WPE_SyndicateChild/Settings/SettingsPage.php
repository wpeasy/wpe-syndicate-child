<?php

namespace WPE_SyndicateChild\Settings;

class SettingsPage {
    const OPTION_KEY = 'wpe_syndicate_master';

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function add_menu() {
        add_options_page(
            'Syndicate Master Connection',
            'Syndicate Master',
            'manage_options',
            'wpe-syndicate-child',
            [__CLASS__, 'render']
        );
    }

    public static function register_settings() {
        register_setting(self::OPTION_KEY, self::OPTION_KEY);
    }

    public static function enqueue_assets($hook) {
        if ($hook !== 'settings_page_wpe-syndicate-child') return;

        wp_enqueue_script('alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], null, true);
        wp_enqueue_script('wpe-child-settings', plugin_dir_url(__FILE__) . '../../../assets/js/settings.js', [], null, true);
        wp_localize_script('wpe-child-settings', 'wpeSyndicateChild', [
            'rest_url' => esc_url_raw(rest_url('wpe-syndicate-child/v1/')),
            'nonce'    => wp_create_nonce('wp_rest')
        ]);
    }

    public static function render() {
        $options = get_option(self::OPTION_KEY, ['master_url' => '', 'uuid' => '']);
        ?>
        <div class="wrap" x-data="syndicateChildSettings">
            <h1>Syndicate Master Connection</h1>

            <label>Master URL:</label>
            <input type="url" x-model="url" class="regular-text" />

            <template x-if="status">
                <p>Status: <strong x-text="status"></strong></p>
            </template>

            <button class="button button-primary" x-on:click="toggleConnection()" x-text="buttonLabel"></button>

            <input type="hidden" id="wpe-child-uuid" value="<?php echo esc_attr($options['uuid'] ?? ''); ?>" />
        </div>
        <?php
    }
}
