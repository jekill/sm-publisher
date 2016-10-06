<?php


namespace Jeka\Publisher\ServicePubliser;

use Html2Text\Html2Text;
use Jeka\Publisher\Locker\PublishLockerInterface;
use Jeka\Publisher\Model\Post;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
abstract class AbstractPublisher implements PublishLockerInterface
{
    /** @var  PublishLockerInterface */
    protected $locker;


    protected function stripMessageText($message)
    {
        $html2text = new Html2Text(trim($message),['width'=>0]);
        $message   = $html2text->getText();

        return $message;
    }


    public function isPublished(Post $post)
    {
        return $this->locker->isPublished($post);
    }

    public function markAsPublished(Post $post)
    {
        $this->locker->markAsPublished($post);
    }
}