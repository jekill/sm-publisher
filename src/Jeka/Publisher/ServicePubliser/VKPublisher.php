<?php


namespace Jeka\Publisher\ServicePubliser;

use getjump\Vk\Core;
use getjump\Vk\Model\Photos\UploadResponse;
use getjump\Vk\Wrapper\Photos;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Html2Text\Html2Text;
use Jeka\Publisher\Locker\PublishLockerInterface;
use Jeka\Publisher\Model\Post;
use Symfony\Component\Console\Logger\ConsoleLogger;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class VKPublisher extends AbstractPublisher
{
    /** @var Core */
    private $vkApi;
    private $groupId = '';
    private $ownerId = '';
    /** @var  Client */
    private $guzzle;

    const ID_PREFIX = "vk:";

    public function __construct($ownerId, $groupId, PublishLockerInterface $locker)
    {
        $this->ownerId = $ownerId;
        $this->groupId = $groupId;
        $this->guzzle  = new Client();
        $this->locker  = $locker;
    }


    public function publish(Post $post)
    {

        if ($this->isPublished($post)) {
            return false;
        }

        $attachments = $this->uploadImages($post);

        $message = $this->stripMessageText($post->getMessage());
        if ($post->getUrl()) {
//            $message .= $post->getUrl();
            $attachments[] = $post->getUrl();
        }
        $params = [
            'owner_id'   => '-' . $this->groupId,
            'from_group' => 1,
            'message'    => $message
        ];

        if (count($attachments) > 0) {
            $params['attachments'] = implode(',', $attachments);
        }

        $savePostResult = $this->vkApi->request(
            'wall.post',
            $params
        )->fetchData();

        if ($savePostResult->error) {
            return false;
        }

        $this->markAsPublished($post);
    }

    /**
     * @param Core $vkApi
     */
    public function setVkApi($vkApi)
    {
        $this->vkApi = $vkApi;
    }

    /**
     * @param Post $post
     *
     * @return array
     */
    protected function uploadImages(Post $post)
    {
        $attachments = [];
        if (count($post->getImages()) > 0) {
            $photos           = new Photos($this->vkApi);
            $serverUploadData = $this->vkApi->request('photos.getWallUploadServer')->fetchData()->getResponse();

            foreach ($post->getImages() as $imageUrl) {
                $request  = $this->guzzle->createRequest('POST', $serverUploadData->upload_url);
                $postBody = $request->getBody();
                $image    = file_get_contents($imageUrl);
                $postBody->addFile(new PostFile('photo', $image, 'photo.jpg'));
                /** @var ResponseInterface $response */
                $response = $this->guzzle->send($request);

                $responsePhoto = $response->json(['object' => 1]);
                if ($responsePhoto) {
                    $result = $this->vkApi->request(
                        'photos.saveWallPhoto',
                        [
                            'photo'  => $responsePhoto->photo,
                            'server' => $responsePhoto->server,
                            'hash'   => $responsePhoto->hash
                        ]
                    )->fetchData();
                    if (!$result->error) {
                        $savedPhotoResultData = $result->getResponse();
                        $attachments[]        = $savedPhotoResultData[0]->id;
                    }
                }
            }

            return $attachments;
        }

        return $attachments;
    }
}