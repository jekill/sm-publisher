<?php
/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */

namespace Jeka\Publisher\SourceHandler;

use Jeka\Publisher\Model\Post;

interface SourceHandlerInterface
{
    /**
     * @param $data string
     */
    function setSourceContent($data);

    /**
     * @return Post[]
     */
    function getUnpublishedPosts();

    /**
     * @return Post[]
     */
    function getAllPosts();
}