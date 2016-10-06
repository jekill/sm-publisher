<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
namespace Jeka\Publisher\ServicePubliser;

use Jeka\Publisher\Model\Post;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
interface PublisherInterface
{
    public function publish(Post $post);
}