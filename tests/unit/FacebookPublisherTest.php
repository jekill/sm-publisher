<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */

use Jeka\Publisher\ServicePubliser\FacebookPublisher;

class FacebookPublisherTest extends PHPUnit_Framework_TestCase
{
    public function testPublish()
    {
        $post = new \Jeka\Publisher\Model\Post();
        $post->setId('test:id');
        $post->setMessage('Hello <b>world</b>!!!');

        $lockerFile = __DIR__ . '/../_data/db/locker.serialize';
        file_put_contents($lockerFile, '');

        $locker = $this->getMockBuilder(\Jeka\Publisher\Locker\FilePublishLocker::class)
                       ->setConstructorArgs([$lockerFile])
                       ->setMethods(['markAsPublished'])
                       ->getMock()
        ;

        $locker->expects($this->once())->method('markAsPublished')
               ->with($post)
        ;

        $publisher = $this->getMockBuilder(FacebookPublisher::class)
                          ->setConstructorArgs([$locker, 'sess', '1'])
                          ->setMethods(['doFacebookRequest'])
                          ->getMock()
        ;

        $publisher->expects($this->once())->method('doFacebookRequest');

        $publisher->publish($post);
    }
}
