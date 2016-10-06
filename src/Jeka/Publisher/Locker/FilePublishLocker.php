<?php


namespace Jeka\Publisher\Locker;

use Jeka\Publisher\Model\Post;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class FilePublishLocker implements PublishLockerInterface
{
    private $publishedIds = [];
    private $dbFilename;
    private $idPrefix = '';

    public function __construct($fileName)
    {
        $this->dbFilename = $fileName;
        $data             = @unserialize(file_get_contents($fileName));
        if (is_array($data)) {
            $this->publishedIds = $data;
        }
    }

    public function isPublished(Post $post)
    {
        return in_array($this->getPostId($post), $this->publishedIds);
    }

    public function markAsPublished(Post $post)
    {
        $this->publishedIds[] = $this->getPostId($post);
        $this->save();
    }

    private function save()
    {
        // fixme: beee
        $stored = @unserialize(file_get_contents($this->dbFilename));
        if (!$stored) {
            $stored = [];
        }
        $this->publishedIds = array_merge($this->publishedIds, $stored);
        $this->publishedIds = array_unique($this->publishedIds);
        file_put_contents($this->dbFilename, serialize($this->publishedIds));
    }

    public function setIdPerfix($prefix)
    {
        $this->idPrefix = $prefix;
    }

    /**
     * @param Post $post
     *
     * @return string
     */
    protected function getPostId(Post $post)
    {
        return $this->idPrefix . $post->getId();
    }
}