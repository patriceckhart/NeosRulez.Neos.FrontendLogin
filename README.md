# Frontend Login for Neos CMS

A Neos CMS package that handles the frontend login and user management. The forms are created with custom finishers and validators in the Neos Form Builder..

## Installation

The NeosRulez.Neos.FrontendLogin package is listed on packagist (https://packagist.org/packages/neosrulez/neos-frontendlogin) - therefore you don't have to include the package in your "repositories" entry any more.

Just run:

```
composer require neosrulez/neos-frontendlogin
```

## Configuration

```yaml
NeosRulez:
  Neos:
    FrontendLogin:
      senderMail: 'noreply@foo.com'
      createUser:
        templatePathAndFilename: 'resource://NeosRulez.Neos.FrontendLogin/Private/Templates/Mail/Create.html'
      resetPassword:
        templatePathAndFilename: 'resource://NeosRulez.Neos.FrontendLogin/Private/Templates/Mail/Reset.html'
```

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
