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

add_filter('the_content', 'avdicodes_rename_subscriptions_to_products_in_content', 10, 1);
function avdicodes_rename_subscriptions_to_products_in_content($content) {
    // Only run this filter on the main query to avoid unnecessary processing
    if (!is_main_query()) {
        return $content;
    }

    libxml_use_internal_errors(true); // Suppress libXML errors for HTML5, etc.

    $dom = new DOMDocument();
    // Load the content. UTF-8 encoding is specified to handle special characters.
    $dom->loadHTML($content);

    $xpath = new DOMXPath($dom);
    // Find h1 tags with the class 'mepr_page_header'
    $h1s = $xpath->query("//h1[contains(@class, 'mepr_page_header')]");

    foreach ($h1s as $h1) {
        // Replace the content of the h1 tag
        $h1->nodeValue = "Products";
    }

    // Save the changes and return the modified HTML
    $newContent = $dom->saveHTML();
    libxml_clear_errors(); // Clear any libXML errors

    return $newContent;
}

