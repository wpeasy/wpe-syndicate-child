<?php

namespace WPE_SyndicateChild;

class Plugin {
    private static $instance;

    public static function init() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->boot();
        }
    }

    private function boot() {
        Settings\SettingsPage::init();
        REST\ReceiveContentEndpoint::init();
    }
}
