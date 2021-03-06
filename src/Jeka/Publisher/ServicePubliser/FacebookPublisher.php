<?php


namespace Jeka\Publisher\ServicePubliser;

use Facebook\FacebookRequest;
use GuzzleHttp\Client;
use Jeka\Publisher\Locker\PublishLockerInterface;
use Jeka\Publisher\Model\Post;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class FacebookPublisher extends AbstractPublisher
{
    const ID_PREFIX = "fb:";
    private $facebookSession;
    /**
     * @var string
     */
    private $facebookPageId;

    public function __construct(PublishLockerInterface $locker, $facebookSession, $facebookPageId)
    {
        $this->guzzle          = new Client();
        $this->locker          = $locker;
        $this->facebookSession = $facebookSession;
        $this->facebookPageId  = $facebookPageId;
    }

    public function publish(Post $post)
    {
        if ($this->isPublished($post)) {
            return false;
        }

        $message = $this->stripMessageText($post->getMessage());
        $params  = [
            'message'   => $message,
            'published' => true
        ];

        if (count($post->getImages()) > 0) {
            $params['picture'] = $post->getImages()[0];
        }

        if ($post->getUrl()) {
            $params['link'] = $post->getUrl();
        }

        try {
            $this->doFacebookRequest($params);
            $this->markAsPublished($post);

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|null $parameters
     * @param string|null $version
     * @param string|null $etag
     *
     * @return \Facebook\FacebookRequest
     */
    private function createRequest($method, $path, $parameters = null, $version = null, $etag = null)
    {
        $request = new FacebookRequest($this->facebookSession, $method, $path, $parameters);

        return $request;
    }

    /**
     * @param $params
     */
    public function doFacebookRequest($params)
    {
        $postRequest = $this->createRequest('POST', '/' . $this->facebookPageId . '/feed', $params);
        $response    = $postRequest->execute()->getResponse();
        return $response;
    }
}