<?php
namespace Flowpack\GoogleApiClient\Command;

/*
 * This file is part of the Flowpack.GoogleApiClient package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Mvc\Exception\InvalidArgumentValueException;
use Flowpack\GoogleApiClient\Service\CredentialsStorage;

class GoogleApiCommandController extends CommandController
{
    /**
     * @var CredentialsStorage
     * @Flow\Inject()
     */
    protected $credentialsStorage;

    /**
     * Store credentials.json downloaded from Google to be used with the Google Service API.
     *
     * @param  string $filePathToCredentialsJson
     * @param string $site
     * @throws InvalidArgumentValueException
     * @throws \Neos\Cache\Exception
     */
    public function storeCredentialsCommand(string $filePathToCredentialsJson, $site = '')
    {
        if (!file_exists($filePathToCredentialsJson)) {
            throw new InvalidArgumentValueException('Please provide a valid path to json file', 1551872086);
        }
        $authConfig = json_decode(file_get_contents($filePathToCredentialsJson), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->credentialsStorage->storeCredentials($authConfig);
            $this->outputLine('Stored credentials');
        } else {
            throw new InvalidArgumentValueException('JSON not valid', 1551872089);
        }
    }
}
