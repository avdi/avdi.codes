<?php
/*
Plugin Name: avdi.codes site-specific plugin
Description: A site-specific plugin for avdi.codes
Version: 0.1
Author: Avdi Grimm
Author URI: https://avdi.codes
*/

add_filter('upload_mimes', 'avdicodes_allow_ebook_file_uploads', 1, 1);
function avdicodes_allow_ebook_file_uploads($mime_types){
    $mime_types['epub'] = 'application/epub+zip'; 
    $mime_types['mobi'] = 'application/x-mobipocket-ebook';
    $mime_types['html'] = 'text/html';
    return $mime_types;
}

add_filter('mepr-account-nav-subscriptions-label', 'avdicodes_rename_subscriptions_to_products');
function avdicodes_rename_subscriptions_to_products(): string {
    return 'Products';
}

add_filter('webfinger_user_resources', 'avdicodes_add_hachyderm_mastodon_alias', 1, 2);
function avdicodes_add_hachyderm_mastodon_alias($resources, $user) {
    if('avdi' === $user->user_login) {
        $resources[] = 'https://hachyderm.io/@avdi';
        $resources[] = 'https://hachyderm.io/users/avdi';
        $resources[] = 'acct:avdi@hachyderm.io';
    }
    return $resources;
}

add_filter('activitypub_activity_user_object_array', 'avdicodes_add_aka_to_activity_stream', 10, 3);
function avdicodes_add_aka_to_activity_stream($array, $object_id, $object) {
    $user_login = get_userdata($object->get__id())->user_login;
    if('avdi' === $user_login) {
        $array['alsoKnownAs'] = ['https://hachyderm.io/users/avdi'];
    }
    return $array;
}