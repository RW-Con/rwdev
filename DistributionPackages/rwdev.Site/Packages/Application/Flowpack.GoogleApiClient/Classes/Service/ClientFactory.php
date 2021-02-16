<?php
namespace Flowpack\GoogleApiClient\Service;

/*
 * This file is part of the Flowpack.GoogleApiClient package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Google\Auth\CredentialsLoader;
use Neos\Cache\Exception\InvalidBackendException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Utility\Environment;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Factory for creating Google Api clients preconfigured for usage with Neos CMS
 */
class ClientFactory
{
    /**
     * @Flow\InjectConfiguration(path="applicationName", package="Flowpack.GoogleApiClient")
     * @var string
     */
    protected $applicationName;

    /**
     * @Flow\InjectConfiguration(path="cache.applicationIdentifier", package="Neos.Flow")
     * @var string
     */
    protected $applicationIdentifier;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CacheItemPoolInterface
     */
    protected $clientCache;

    /**
     * @var CredentialsStorage
     * @Flow\Inject
     */
    protected $credentialsStorage;

    /**
     * @var ConfigurationManager
     * @Flow\Inject
     */
    protected $configurationManager;

    /**
     * @param LoggerInterface $cacheManager
     * @return void
     */
    public function injectLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return CacheItemPoolInterface
     */
    protected function getCachePool()
    {
        if ($this->clientCache === null) {
            $objectManager = Bootstrap::$staticObjectManager;
            /** @var \Neos\Flow\Utility\Environment $environment */
            $environment = $objectManager->get(Environment::class);
            $fileCacheBasePath = $environment->getPathToTemporaryDirectory();

            $environmentConfiguration = new \Neos\Cache\EnvironmentConfiguration($this->applicationIdentifier,
                $fileCacheBasePath);

            $cacheFactory = new \Neos\Cache\Psr\Cache\CacheFactory(
                $environmentConfiguration
            );

            try {
                $this->clientCache = $cacheFactory->create(
                    'Neos_GoogleApiClient',
                    \Neos\Cache\Backend\FileBackend::class
                );
            } catch (InvalidBackendException $e) {
                $this->logger->error('Could not instantiate cache for Google Api client', [$e->getMessage()]);
            }
        }
        return $this->clientCache;
    }

    /**
     * @throws \Google_Exception
     * @return \Google_Client
     */
    public function create()
    {
        $client = new \Google_Client();
        if (empty($this->credentialsStorage->getCredentials())) {
            if (empty(getenv(CredentialsLoader::ENV_VAR))) {
                $this->logger->warning(sprintf(
                    'Missing Google credentials. Please use "./flow googleapi:storecredentials auth.json"' .
                    'or set %s according to https://cloud.google.com/docs/authentication/production',
                    CredentialsLoader::ENV_VAR));
            }
        } else {
            $client->setAuthConfig($this->credentialsStorage->getCredentials());
        }

        $client->setLogger($this->logger);
        $client->setCache($this->getCachePool());
        $client->setApplicationName($this->applicationName);
        $client->setTokenCallback(function ($cacheKey, $accessToken) {
            $this->logger->info(sprintf('New access token received at cache key %s', $cacheKey));
        });
        $client->setHttpClient(new \GuzzleHttp\Client());

        return $client;
    }
}
