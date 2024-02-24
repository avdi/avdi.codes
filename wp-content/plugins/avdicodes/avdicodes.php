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