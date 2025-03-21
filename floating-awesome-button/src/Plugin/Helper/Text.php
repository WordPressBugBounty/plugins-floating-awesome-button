<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

trait Text {

    /**
     * Slugify
     */
    public function slugify($text){
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) { return 'n-a'; }
        return $text;
    }

    /**
     * Check string is json
     */
    public function isJson($string){
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Convert readme.txt to HTML.
     *
     * @param string $content The content.
     * @return string The HTML content.
     */
    function parse_readme_to_html($content) {

        // Replace * with <li> inside <ul>
        $content = preg_replace('/^\* (.*?)(?:\r?\n|$)/m', '<li>$1', $content);

        // Replace - with sub-<ul><li> inside <li>
        $content = preg_replace('/^\s*-\s+(.*?)(?:\r?\n|$)/m', '<ul><li>$1</li></ul>', $content);

        // Close <li> tag at the end of each block
        $content = preg_replace('/(<li>.*?)(?=\n\*|$)/s', '$1</li>', $content);

        // Handle paragraphs (wrap text between <p> tags)
        $content = preg_replace('/\n+/', '</p><p>', trim($content)); // Avoid empty lines

        // Handle h4 headings
        $content = preg_replace('/= (.*?) =/', '<h4>$1</h4>', $content);

        // Ensures that URLs inside Markdown links are not affected
        $content = preg_replace_callback(
            '/https?:\/\/www\.youtube\.com\/watch\?v=[a-zA-Z0-9_-]+(?:&[a-zA-Z0-9_=-]+)*/',
            function ($matches) {
                // Wrap raw URLs (not inside markdown) in a placeholder
                return '{{youtube:' . $matches[0] . '}}';
            },
            $content
        );

        // Convert {{youtube:URL}} placeholders back to markdown links
        $content = preg_replace_callback(
            '/\[([^\]]+)\]\({{youtube:(https?:\/\/www\.youtube\.com\/watch\?v=[a-zA-Z0-9_-]+(?:&[a-zA-Z0-9_=-]+)*)}}\)/',
            function ($matches) {
                // Return the markdown link with the URL, not the placeholder
                return '[' . $matches[1] . '](' . $matches[2] . ')';
            },
            $content
        );

        // Replace placeholders with iframe HTML for plain YouTube URLs
        $content = preg_replace_callback(
            '/{{youtube:(https?:\/\/www\.youtube\.com\/watch\?v=[a-zA-Z0-9_-]+)}}/',
            function ($matches) {
                // Extract the video ID from the URL and generate iframe HTML
                $video_id = substr($matches[1], 32); // Extract video ID after "v="
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . urlencode($video_id) . '" frameborder="0" allowfullscreen></iframe>';
            },
            $content
        );

        // Handle links
        $content = preg_replace(
            '/\[(.*?)\]\((.*?)\)/',
            '<a href="$2" rel="nofollow ugc">$1</a>',
            $content
        );

        // Wrap the content in a div container as required
        $wrapped_content = '<div class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained">
                                <div id="tab-description" class="plugin-description section">
                                    <p>' . $content . '</p>
                                </div>
                            </div>';

        return $wrapped_content;
    }

}
