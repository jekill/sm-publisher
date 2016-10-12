<?php
use Jeka\Publisher\SourceHandler\RssSourceHandler;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class RssSourceHandlerTest extends \PHPUnit_Framework_TestCase
{

    /** @var  RssSourceHandler */
    private $handler;
    /** @var  \Jeka\Publisher\Locker\PublishLockerInterface */
    private $locker;

    protected function setUp()
    {
        $lockerFile = __DIR__ . '/../_data/db/locker.serialize';
        file_put_contents($lockerFile, '');
        $locker        = new \Jeka\Publisher\Locker\FilePublishLocker($lockerFile);
        $this->handler = new RssSourceHandler();
        $this->handler->setSourceContent(file_get_contents(__DIR__ . '/../_data/rss-feed.xml'));
        $this->handler->setLocker($locker);
        $this->locker = $locker;
    }


    public function testGetAllPosts()
    {
        $handler = $this->handler;
        $posts = $handler->getAllPosts();
        $this->assertCount(10, $posts);
    }


    public function testGetunpublishedPosts()
    {
        $handler = $this->handler;

        $posts = $handler->getUnpublishedPosts();
        $this->assertCount(10, $posts);

        $this->locker->markAsPublished($posts[0]);
        $this->assertCount(9, $handler->getUnpublishedPosts());
        $this->locker->markAsPublished($posts[1]);
        $this->assertCount(8, $handler->getUnpublishedPosts());
    }

}