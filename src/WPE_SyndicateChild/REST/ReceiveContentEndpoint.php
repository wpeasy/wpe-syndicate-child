<?php

namespace WPE_SyndicateChild\REST;

use WP_REST_Server;
use WP_REST_Request;

class ReceiveContentEndpoint {
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register']);
    }

    public static function register() {
        register_rest_route('wpe-syndicate-child/v1', '/receive', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [__CLASS__, 'receive_content'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function receive_content(WP_REST_Request $request) {
        $uuid = $request->get_header('X-SYNDICATE-KEY');
        $stored = get_option('wpe_syndicate_master');

        if (!$uuid || $uuid !== ($stored['uuid'] ?? '')) {
            return new \WP_REST_Response(['error' => 'Invalid UUID'], 403);
        }

        $data = $request->get_json_params();

        if (!isset($data['item-uuid'], $data['content-type'])) {
            return new \WP_REST_Response(['error' => 'Invalid payload'], 400);
        }

        // Handle post/page sync (basic example)
        if ($data['content-type'] === 'post') {
            $existing = get_posts([
                'post_type' => $data['post-type'],
                'meta_key' => 'syndicated_item_uuid',
                'meta_value' => $data['item-uuid'],
                'post_status' => 'any',
            ]);

            $post_data = [
                'post_title'   => $data['content']['post_title'],
                'post_content' => $data['content']['post_content'],
                'post_status'  => 'publish',
                'post_type'    => $data['post-type'],
            ];

            if ($existing) {
                $post_id = $existing[0]->ID;
                wp_update_post(array_merge(['ID' => $post_id], $post_data));
            } else {
                $post_id = wp_insert_post($post_data);
                update_post_meta($post_id, 'syndicated_item_uuid', $data['item-uuid']);
            }

            foreach ($data['meta'] as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }

            return new \WP_REST_Response(['status' => 'success', 'post_id' => $post_id]);
        }

        return new \WP_REST_Response(['status' => 'not-handled'], 200);
    }
}
