# flowpack-googleapiclient

A package that provides Neos Flow & CMS packages with authentication and helpers for the Google API Client.

## Installation

Add the dependency to your site package like this:

    composer require --no-update flowpack/googleapiclient
    
And then run `composer update` in your project's root folder.

## Configuration

### Authentication

First retrieve your `auth.json` file from [Google](https://cloud.google.com/docs/authentication/production).

To allow the API client to authenticate, you either have to store the credentials via the following command:

    ./flow googleapi:storecredentials auth.json

Or set the environment variable `GOOGLE_APPLICATION_CREDENTIALS` to the path where you store your `auth.json`.

### Application name

Set your application name in your `Settings.yaml` like this:

    Flowpack:
      GoogleApiClient:
        applicationName: 'My app' 

## Usage

Add an `Objects.yaml` to your package's `Configuration` folder with the following content and adapt to your service name:

    Vendor\Package\Service\MyGoogleService:
      arguments:
        1:
          object:
            factoryObjectName: Flowpack\GoogleApiClient\Service\ClientFactory
            factoryMethodName: create
            
Then have your `MyGoogleService` class look like this:

    <?php
    namespace Vendor\Package\Service;
    
    use Google_Client;
    use Neos\Flow\Annotations as Flow;
    
    /**
     * My Google API service
     *
     * @Flow\Scope("singleton")
     */
    class MyGoogleService extends \Google_Service_Webmasters
    {
        /**
         * @inheritdoc
         */
        public function __construct(Google_Client $client)
        {
            parent::__construct($client);
    
            $client->addScope(\Google_Service_Webmasters::WEBMASTERS);
        }
    }

In this example it would use the API for the Google Webmasters endpoints.

You can adjust this to the API you need by changing the inheritance and setting the correct scope in the constructor.

Finally implement the methods for accessing the API and inject the service wherever you need it.
