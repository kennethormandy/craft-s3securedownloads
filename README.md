# S3 Secure Downloads plugin for Craft CMS

This plugin will return a signed URL used to allow temporary S3 downloads. It also proxies the request and ensures there is a valid user session.

![Alt text](resources/screenshots/screenshot.png?raw=true "Screenshot")

## Installation

To install S3 Secure Downloads, follow these steps:

1. Download & unzip the file and place the `s3securedownloads` directory into your `craft/plugins` directory
2.  -OR- do a `git clone git@github.com:jonathanmelville/s3-secure-downloads.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3. Install plugin in the Craft Control Panel under Settings > Plugins
4. The plugin folder should be named `s3securedownloads` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

S3 Secure Downloads works on Craft 2.5.x.

## Usage

Pass in an asset's entry id and it will return a signed URL for that asset:

`<a href="{{ getSignedUrl(entry.myFile[0].id }}">{{ entry.myFile[0].title }}</a>`

## S3 Secure Downloads Changelog

### 1.0.0 -- 2016.02.25

* Initial release

Brought to you by [Jonathan Melville](http://jonathanmelville.com)