# WordPress (Phoenix) Developer Snippets

Collection of developer php classes and or functions to assist in commonly requested features.

## Custom Post Formats

Creating custom post formats is hard. Some of the WordPress core team doesn't beleive in allowing customization of post formats, and have not offered to assist us in the process of hooking and building our own. However, we have been able to work around the code and provide a way to create and use custom post formats of your own.

You can follow along in the WordPress trac ticket here: https://core.trac.wordpress.org/ticket/31399

You can use the following snippet library to assess and build your own custom post formats:
[Custom Post Formats](snippets/custom_post_formats/)

Start by investigating the primary class
[class-custom-post-formats.php](snippets/custom_post_formats/class-custom-post-formats.php)

## What Next?

Have something to contribute? send us a PR and we will pull in anything we deem valuable and generic enough for the community as a whole.
