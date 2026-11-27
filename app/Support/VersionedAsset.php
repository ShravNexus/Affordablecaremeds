<?php

namespace App\Support;

/**
 * Builds the URL of a file in public/ with its last-modified time attached.
 *
 * The storefront's stylesheets and scripts are plain files served at a fixed
 * URL, so a browser, the host's page cache or a CDN keeps handing out the copy
 * it already has after a deploy changes them - new rules simply never arrive.
 * A version that changes whenever the file does gives every deploy a fresh URL.
 */
final class VersionedAsset
{
    public static function url(string $path): string
    {
        $file = public_path($path);

        if (! is_file($file)) {
            return asset($path);
        }

        return asset($path).'?v='.filemtime($file);
    }
}
