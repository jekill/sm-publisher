<?php


namespace Jeka\Publisher\Command;

use Facebook\FacebookSession;
use Jeka\Publisher\Exception\PublisherException;
use Jeka\Publisher\Locker\FilePublishLocker;
use Jeka\Publisher\Model\Post;
use Jeka\Publisher\ServicePubliser\FacebookPublisher;
use Jeka\Publisher\ServicePubliser\PublisherInterface;
use Jeka\Publisher\ServicePubliser\VKPublisher;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author: Eugeny Fomin <info@jeka.ru>
 */
class PublisherCommand extends Command
{

    /**
     * @var FilePublishLocker
     */
    private $publishLocker;
    /** @var  PublisherInterface[] */
    private $publishers;
    private $config;
    /** @var  LoggerInterface */
    private $logger;

    public function __construct(FilePublishLocker $publishLocker, array $config)
    {
        parent::__construct();

        $this->publishLocker = $publishLocker;
        $this->config        = $config;
    }

    protected function configure()
    {
        $this->setName('publish');
        $this
            ->addArgument('handler', InputArgument::REQUIRED)
            ->addArgument('source', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handlerName = $input->getArgument('handler');
        $source      = $input->getArgument('source');

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->logger = new ConsoleLogger($output);

        $handlerClassName = 'Jeka\\Publisher\\SourceHandler\\' . $handlerName . 'SourceHandler';
        if (!class_exists($handlerClassName)) {
            throw new PublisherException(sprintf('Handler not found: %s', $handlerClassName));
        }

        $sourceContent = file_get_contents($source);

        $sourceHandler = new $handlerClassName;
        $sourceHandler->setSourceContent($sourceContent);
//        $sourceHandler->setPublishLocker($this->publishLocker);
        $posts = $sourceHandler->getAllPosts();
        /** @var Post $post */
        foreach ($posts as $post) {
            $this->publish($post);
        }
    }

    private function getPublishers()
    {
        return [
            $this->createVKPublisher(),
            $this->createFacebookPublisher()
        ];
    }

    private function publish(Post $post)
    {
        if (!$this->publishers) {
            $this->publishers = $this->getPublishers();
        }

        foreach ($this->publishers as $publisher) {
            $this->logger->info(sprintf('%s publish %s', constant(get_class($publisher) . '::ID_PREFIX'), $post->getId()));
            $publisher->publish($post);
        }
    }

    private function getConfig()
    {
        return $this->config;
    }


    /**
     * @return FacebookPublisher
     */
    private function createFacebookPublisher()
    {
        $locker = clone $this->publishLocker;
        $locker->setIdPerfix(FacebookPublisher::ID_PREFIX);
        $config = $this->getConfig()['fb'];

        FacebookSession::setDefaultApplication($config['app_id'], $config['app_secret']);

        $session = new FacebookSession(
            $config['page_access_token']
        );

        $session = $session->getLongLivedSession();

        $fbPublisher = new FacebookPublisher($locker, $session, $config['page_id']);

        return $fbPublisher;
    }

    /**
     * @return VKPublisher
     */
    private function createVKPublisher()
    {
        $config = $this->getConfig();

        $vkPublishLoker = clone $this->publishLocker;
        $vkPublishLoker->setIdPerfix(VKPublisher::ID_PREFIX);

        $vk = new VKPublisher($config['vk']['owner_id'], $config['vk']['group_id'], $vkPublishLoker);

        $api = \getjump\Vk\Core::getInstance()->apiVersion('5.25');
        $api->setToken($config['vk']['token']);

        $vk->setVkApi($api);

        return $vk;
    }
}
