# SM Publisher

A tool that helps to publish posts to different social networks (Facebook, VK.com)

It gets information from a single resource (e.g. a RSS feed) and publishes it to one or more social networks


## Usage:

```bash
cp ./config/config.example.php ./config/config.php
```

Edit config/config.php file
```php
<?php
return [
    'vk' => [
        'owner_id' => '',
        'group_id' => '',
        'token'    => ''
    ],
    'fb'=>[
        'app_id'            => '',
        'app_secret'        => '',
        'page_id'           => '',
        'page_access_token' => '',
    ]
];
```

### Publish
```bash
php ./bin/publisher.php publish Rss http://feeds.feedburner.com/symfony/blog
```
