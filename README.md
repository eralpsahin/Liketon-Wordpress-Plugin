# LikeTon The Like Button plugin for Wordpress


## Description
There are lots of Like button plugins for wordpress, those do amazing things, but most of them either use another social network to store the likes, or too complex for a beginner who wants to have **just a like button** in their posts and let users like it. Liketon does that. This plugin may be also helpful to those who want to learn simple plugin development for wordpress, basic ajax use and wordpress api.

## Features

* Like posts or dislike previously liked posts
* See recent likes in liketon widget
* See all likes and dislike them in liketon statistics page
* Does not require any change in theme's files or core files of wordpress
* Most of the implementation is raw
* As simple as it gets


## Download
[Downlaod the liketon-plugin.zip file from the releases page](https://github.com/eralpsahin/LikeTon/releases)

##Installation
Under _Plugins_ admin page click _Add New_ then, click _Upload Plugin_
![picture alt](http://wowslider.com/engine/data/upload/2015/02/plugins_upload_plugin.jpg "Upload Plugin")

Browse the previously downloaded liketon-plugin.zip file and click _Install Now_ after successfull installation you can either activate plugin or return to plugins site without activating the liketon.

##Activation and Settings
You can activate or deactive liketon whenever you would like to from the _Plugins_ page.
Liketon will create a custom database on the wordpress database when it is activated. After activation all the posts will have like button and evey user can like posts. To like any post one has to be logged in first.

#### Liketon Widget

You can add the widget to the desired widget area from the _Widgets_ page under _Appearance_.
Just drag the "Liketon the Like Button" widget to the desired area.

By default widget shows most recent 10 likes (ordered by the most recent comments), that can be easily changed in the widgets page.

#### Shortcode
The Shortcode `[Liketon]` can be used to insert the liketon statistics to anywhere you can use shortcodes. This creates a statistics page on the inserted page and a pagination for all the likes.
You can find more on shortcodes here: https://codex.wordpress.org/Shortcode_API

By default the number of likes per page is 10, but on _Admin Menu_ hover _Settings_ and then click _Liketon_, you can change how many likes per page.

#### Notes
This plugin is mostly a tutorial and a resource to get help. You can always contact me via my e-mail: eralpsahin@sabanciuniv.edu
