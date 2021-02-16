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

use Neos\Cache\Exception as CacheException;
use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class CredentialsStorage
{
    private const CREDENTIALS = 'SERVICE_CREDENTIALS';

    /**
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $cache;

    /**
     * @param  array $authConfig
     * @throws CacheException
     */
    public function storeCredentials(array $authConfig): void
    {
        $this->cache->set(self::CREDENTIALS, $authConfig);
    }

    /**
     * @return array
     */
    public function getCredentials(): array
    {
        $credentials = $this->cache->get(self::CREDENTIALS);
        if ($credentials === false || !is_array($credentials)) {
            return [];
        }
        return $credentials;
    }

    /**
     * Remove existing tokens
     *
     * @return void
     */
    public function removeCredentials()
    {
        $this->cache->remove(self::CREDENTIALS);
    }
}
