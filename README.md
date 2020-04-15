# SM Publisher

A tool that helps to publish posts to different social networks (Facebook, VK.com)

It gets information from a single resource (e.g. an RSS feed) and publishes it to one or more social networks

## Installation

```bash
git clone git@github.com:jekill/sm-publisher.git
cd sm-publisher
composer install
```

By the way, you can use Docker. Follow the instructions below. 

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
php ./bin/publisher.php publish Rss https://your-site.com/rss-feed.xml
```

## Using docker 

### Build a docker image
```bash
docker build -t sm-publisher .
```
### Install dependencies
```bash
docker run -it --volume $PWD:/app sm-publisher /composer/composer install
```
### Using
```bash
docker run -it --volume $PWD:/app sm-publisher php ./bin/publisher.php publish Rss https://your-site.com/rss-feed.xml
```
