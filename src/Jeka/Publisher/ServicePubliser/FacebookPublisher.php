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
//            $params['link']['picture'] = $post->getImages()[0];
//            var_dump($post->getImages()[0]);
//            $previewImage = '';
//            $response = $this->createRequest(
//                'POST',
//                "/{$this->facebookPageId}/albums",
//                [
//                    'name'    => $post->getId(),
//                    'message' => $this->stripMessageText($post->getMessage())
//                ]
//            )->execute()->getResponse();
//            $albumId  = $response->id;
//
//            foreach ($post->getImages() as $imageUrl) {
//                $uploadReq = $this->createRequest(
//                    "POST",
//                    "/$albumId/photos",
////                    "/{$this->facebookPageId}/photos",
//                    [
//                        'url' => $imageUrl,
//                    ]
//                );
//                $response  = $uploadReq->execute()->getResponse();
//                if (!$previewImage) {
//                    $previewImage = $response->id;
//                }
//            }
//            if ($previewImage) {
//                $params['object_attachment'] = $previewImage;
//            }
        }

        if ($post->getUrl()) {
            $params['link'] = $post->getUrl();
        }

        try {
//            var_dump($params);
            $postRequest = $this->createRequest('POST', '/' . $this->facebookPageId . '/feed', $params);
            $response    = $postRequest->execute()->getResponse();
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
}