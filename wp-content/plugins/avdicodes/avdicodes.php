<?php
/*
Plugin Name: avdi.codes site-specific plugin
Description: A site-specific plugin for avdi.codes
Version: 0.1
Author: Avdi Grimm
Author URI: https://avdi.codes
*/

add_filter('upload_mimes', 'avdicodes_allow_ebook_file_uploads', 1, 1);
function avdicodes_allow_ebook_file_uploads($mime_types)
{
    $mime_types['epub'] = 'application/epub+zip';
    $mime_types['mobi'] = 'application/x-mobipocket-ebook';
    $mime_types['html'] = 'text/html';
    return $mime_types;
}

add_filter('mepr-account-nav-subscriptions-label', 'avdicodes_rename_subscriptions_to_products');
function avdicodes_rename_subscriptions_to_products(): string
{
    return 'Products';
}

add_filter('webfinger_user_resources', 'avdicodes_add_hachyderm_mastodon_alias', 1, 2);
function avdicodes_add_hachyderm_mastodon_alias($resources, $user)
{
    if ('avdi' === $user->user_login) {
        $resources[] = 'https://hachyderm.io/@avdi';
        $resources[] = 'https://hachyderm.io/users/avdi';
        $resources[] = 'acct:avdi@hachyderm.io';
    }
    return $resources;
}

add_filter('activitypub_activity_user_object_array', 'avdicodes_add_aka_to_activity_stream', 10, 3);
function avdicodes_add_aka_to_activity_stream($array, $object_id, $object)
{
    $user_login = get_userdata($object->get__id())->user_login;
    if ('avdi' === $user_login) {
        $array['alsoKnownAs'] = ['https://hachyderm.io/users/avdi'];
    }
    return $array;
}

add_filter('breeze_custom_headers_allow', 'avdicodes_add_breeze_allowed_headers', 10, 1);
function avdicodes_add_breeze_allowed_headers($allowed_headers) {
    $allowed_headers[] = 'vary';
    $allowed_headers[] = 'content-type';
    return $allowed_headers;
}

add_filter('widget_posts_args', 'avdicodes_filter_recent_posts_widget', 10, 2);
function avdicodes_filter_recent_posts_widget($args, $instance) {
    // Exclude posts with any post format
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'post_format',
            'operator' => 'NOT EXISTS',
        ),
    );

    return $args;
}

add_filter('activitypub_object_content_template', 'avdicodes_activitypub_content_template', 10, 2);
function avdicodes_activitypub_content_template($template, $object) {
    $post_format = get_post_format($object) ?: 'standard';
    if('standard' === $post_format) {
        return "[ap_excerpt]\n\n[ap_permalink type=\"html\"]\n\n[ap_hashtags]";
    } else {
        return "[ap_content]\n\n[ap_hashtags]";
    }
}

add_action('send_headers', 'avdicodes_suppress_cookies_for_non_html_content');
function avdicodes_suppress_cookies_for_non_html_content() {
    if(isset($_SERVER['HTTP_ACCEPT']) && !str_contains($_SERVER['HTTP_ACCEPT'], 'text/html') ) {
        header_remove('Set-Cookie');
    }
}

add_action('fluent_crm/email_header', 'avdicodes_fluentcrm_add_code_styles_to_emails', 10, 1);
function avdicodes_fluentcrm_add_code_styles_to_emails($design_name)
{
    ?>
    <style>
        .red {
            color: red;
        }

        pre code.hljs {
            display: block;
            overflow-x: auto;
            padding: 1em
        }

        code.hljs {
            padding: 3px 5px
        }

        .hljs {
            background: #fefefe;
            color: #545454
        }

        .hljs-comment,
        .hljs-quote {
            color: #696969
        }

        .hljs-deletion,
        .hljs-name,
        .hljs-regexp,
        .hljs-selector-class,
        .hljs-selector-id,
        .hljs-tag,
        .hljs-template-variable,
        .hljs-variable {
            color: #d91e18
        }

        .hljs-attribute,
        .hljs-built_in,
        .hljs-link,
        .hljs-literal,
        .hljs-meta,
        .hljs-number,
        .hljs-params,
        .hljs-type {
            color: #aa5d00
        }

        .hljs-addition,
        .hljs-bullet,
        .hljs-string,
        .hljs-symbol {
            color: green
        }

        .hljs-section,
        .hljs-title {
            color: #007faa
        }

        .hljs-keyword,
        .hljs-selector-tag {
            color: #7928a1
        }

        .hljs-emphasis {
            font-style: italic
        }

        .hljs-strong {
            font-weight: 700
        }

        @media screen and (-ms-high-contrast:active) {

            .hljs-addition,
            .hljs-attribute,
            .hljs-built_in,
            .hljs-bullet,
            .hljs-comment,
            .hljs-link,
            .hljs-literal,
            .hljs-meta,
            .hljs-number,
            .hljs-params,
            .hljs-quote,
            .hljs-string,
            .hljs-symbol,
            .hljs-type {
                color: highlight
            }

            .hljs-keyword,
            .hljs-selector-tag {
                font-weight: 700
            }
        }
    </style>
    <?php
}
