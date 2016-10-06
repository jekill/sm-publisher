<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
namespace Jeka\Publisher\Locker;

use Jeka\Publisher\Model\Post;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
interface PublishLockerInterface
{
    /**
     * @param Post $post
     *
     * @return bool
     */
    public function isPublished(Post $post);

    /**
     * @param Post $post
     *
     * @return bool
     */
    public function markAsPublished(Post $post);
}