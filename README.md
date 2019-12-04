# S3 Secure Downloads plugin for Craft CMS

**NOTE** This plugin is being converted for Craft CMS3, and the README is being revised accordingly.

This plugin will return a [signed URL](http://docs.aws.amazon.com/AmazonS3/latest/dev/ShareObjectPreSignedURL.html) used to allow temporary access to private objects with an expiring URL. You can optionally allow file downloads only for logged in users and force file downloads (useful for PDF files).

This plugin was originally developed by [Jonathan Melville](https://github.com/jonathanmelville/s3securedownloads) for a client in the financial services industry who wanted to make sure only logged in users had access to downloads of financial reports, and download links couldn’t be shared.

Craft introduced the ability to have private S3 assets in [2.6.2771](https://craftcms.com/changelog#build2771). Now you can keep your S3 objects private but grant temporary access to them with an expiring link. 

![Screenshot of the plugin settings.](./src/resources/screenshots/screenshot.png)

## Installation

```sh
# Require the plugin with composer
composer require kennethormandy/craft-s3securedownloads

# Install the plugin via the Control Panel, or by running:
./craft install/plugin s3securedownloads
```

S3 Secure Downloads is built for Craft v3.x. For a version that runs on Craft v2.5.x, see [the original plugin](https://github.com/jonathanmelville/s3securedownloads).

## Usage

Pass in an asset's entry id and it will return a signed URL for that asset:

```twig
<a href="{{ getSignedUrl(entry.myAssetField[0].id) }}">{{ entry.myAssetField[0].title }}</a>
```

## License

[The MIT License (MIT)](./LICENSE.md)

Copyright © 2016–2019 [Jonathan Melville](https://github.com/jonathanmelville/s3securedownloads)<br/>
Copyright © 2019 [Kenneth Ormandy Inc.](https://kennethormandy.com)
