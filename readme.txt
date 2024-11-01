=== Vkontakte Cut Post ===
Contributors: zorge
Tags: filter, hide, hidden, post, privacy, vkontakte
Requires at least: 3.2
Tested up to: 3.3
Stable tag: 1.0.1

This plugin allows you to hide part of any posts, which will be available to the user after he clicks a button "I like" (vKontakte).

== Description ==

This plugin allows you to hide part of any posts (or pages), which will be available to the user after he clicks a button "I like" (vKontakte) on this post (page). Instead, this text user see the link, click that opens a window with instructions to be executed to see the hidden text.

**How to hide the text**

* The text that you want to hide you must enclose the tag: `[vcut]` hidden text `[/vcut]`. You can also use the button in the visual editor or quicktag.
* An optional parameter *text* to specify the text to be displayed instead of hidden. For example, `[vcut text = "Click Me"]` hidden text `[/vcut]`.

== Installation ==

1. Upload all files to the `/wp-content/plugins/vkontakte-cut-post/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Option Page.
2. Pop-Up Window.

== Changelog ==

= 1.0.1 =
* Added missing `vcp_section_text()`.
* Fix some minor errors.

= 1.0 =
* First stable release.
