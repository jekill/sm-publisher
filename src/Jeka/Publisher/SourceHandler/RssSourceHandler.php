<?php

namespace Jeka\Publisher\SourceHandler;

use Jeka\Publisher\Locker\PublishLockerInterface;
use Jeka\Publisher\Model\Post;
use Zend\Feed\Reader\Entry\EntryInterface;
use Zend\Feed\Reader\Feed\FeedInterface;
use Zend\Feed\Reader\Reader;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class RssSourceHandler implements SourceHandlerInterface
{
    /** @var  PublishLockerInterface */
    private $locker;
    /** @var  FeedInterface */
    private $feed;

    /**
     * @var Post[]|null
     */
    private $posts = null;


    public function __construct(){
    }

    /**
     * @return Post[]
     */
    function getUnpublishedPosts()
    {
        $unpublishedPosts = [];
        foreach ($this->getAllPosts() as $post) {
            if ($this->locker && !$this->locker->isPublished($post)) {
                $unpublishedPosts[] = $post;
            }
        }

        return $unpublishedPosts;
    }

    /**
     * @return Post[]
     */
    function getAllPosts()
    {
        if ($this->posts === null) {
            $this->posts = [];
            /** @var EntryInterface $item */
            foreach ($this->feed as $item) {
                $post = new Post();
                $post->setId('rss:' . $item->getId());
                $post->setMessage($item->getContent());
                $post->setDate($item->getDateModified());
                $post->setUrl($item->getPermalink());
                $this->posts[] = $post;
            }
        }

        return $this->posts;
    }

    /**
     * @param $locker PublishLockerInterface
     */
    function setLocker($locker)
    {
        $this->locker = $locker;
    }

    function setSourceContent($xmlString)
    {
        $this->feed = Reader::importString($xmlString);
    }


}