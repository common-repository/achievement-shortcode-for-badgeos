=== Achievement Shortcode Add-On for BadgeOS ===

Contributors: konnektiv, chherbst
Tags: badge, badges, restrict, shortcode, access, hide, content, openbadges, shortcode, credly, OBI, mozilla, open badges, achievement, award, reward, engagement, submission, nomination, community, API, open credit, credit, plugin
Requires at least: 3.6.0
Tested up to: 5.5.3
Requires PHP: 5.5.9
Stable tag: 1.1.0
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

This BadgeOS Add-on adds a shortcode to show or hide content depending on the user having earned a specific achievement.

== Description ==

This BadgeOS Add-on adds a shortcode to show or hide content depending on the user having earned a specific achievement.

Any content in a post or page enclosed in the shortcode [user_earned_achievement id="achievement_id"][/user_earned_achievement] will only be shown if the current user has already earned the achievement with the specified id. This shortcode is fully integrated with the BadgeOS shortcode insert button.

**Note:** You will need to install the free [BadgeOS plugin](http://wordpress.org/extend/plugins/badgeos/ "BadgeOS")&trade; (version 1.4 or higher) to use the BadgeOS Community Add-on. This Plugin was tested up to BadgeOS 3.6.7.

[Get the BadgeOS plugin](http://wordpress.org/extend/plugins/badgeos/ "BadgeOS").

This plugin was originally developed for the [globe - Community of Digital Learning](https://quality4digitallearning.org/) on behalf of [GIZ](https://www.giz.de/).

= Contact =

* [Konnektiv](http://konnektiv.de/)

* [BadgeOS Achievement Shortcode Add-on on GitHub](https://github.com/konnektiv/badgeos-achievement-shortcode-add-on) - Report issues, contribute code


== Installation ==

1. Upload, activate and configure the free [BadgeOS plugin](http://wordpress.org/extend/plugins/badgeos/ "BadgeOS") to WordPress.
1. Upload 'Achievement-Shortcode-Add-on' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. BadgeOS Help/Support: Description
2. Shortcode Pop-up Window
3. Page description
4. Front-end View when achievement has not yet been earned
5. Front-end view when achievement has been earned

== Changelog ==

= 1.1.0 =
test with newer versions, adjust to WordPress publishing, add screenshots

= 1.0.7 =
* Fix deactivation when BadgeOS is deactivated

= 1.0.5 =
* Add 'before' shortcode parameter to only accept achievements earned before a specific date

= 1.0.4 =
* Add closing shortcode surrounding current selection when using the BadgeOS shortcode insert button
* Use select2 for the achievement id shortcode attribute

= 1.0.3 =
* Finally fix translation domain

= 1.0.2 =
* Fix translation domain & add gettext calls

= 1.0.1 =
* Fix shortcode description & error message

= 1.0.0 =
* Initial release
