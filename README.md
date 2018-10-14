# Linode Provider for OAuth 2.0 Client

[![PHP](https://img.shields.io/badge/PHP-5.6%2B-blue.svg)](https://secure.php.net/migration56)

This package provides Linode OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```bash
composer require webinarium/oauth2-linode
```

## Usage

Usage is the same as The League's OAuth client, using `\Linode\OAuth2\Client\Provider\Linode` as the provider.

### Authorization Code Flow

```php
$provider = new Linode\OAuth2\Client\Provider\Linode([
    'clientId'     => '{linode-client-id}',
    'clientSecret' => '{linode-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        /** @var \Linode\OAuth2\Client\Provider\LinodeResourceOwner $owner */
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getFirstName());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

## Development

``` bash
./bin/php-cs-fixer fix
./bin/phpunit --coverage-text
```

## Contributing

Please see [CONTRIBUTING](https://github.com/webinarium/oauth2-linode/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Artem Rodygin](https://github.com/webinarium)

## License

The MIT License (MIT). Please see [License File](https://github.com/webinarium/oauth2-linode/blob/master/LICENSE) for more information.
