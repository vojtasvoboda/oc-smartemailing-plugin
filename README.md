# SmartEmailing.cz plugin for OctoberCMS

SmartEmailing plugin for OctoberCMS adds connector to SmartEmailing API v3 and also "ready to use" subscribe component. No other plugin dependencies.

## Installation

After installing the plugin, you have to fill SmartEmailing credentials in CMS > Settings > SmartEmailing. You can find API Key at SmartEmailing > Account > API Keys.

## Using

You can use directly SmartEmailing client like that:

```
$api_username = \VojtaSvoboda\SmartEmailing\Models\Settings::get('api_username');
$api_key = \VojtaSvoboda\SmartEmailing\Models\Settings::get('api_key');
$smartEmailing = new \SmartEmailing\v3\Api($api_username, $api_key);
$import = $smartEmailing->import()->addContact(new Contact($email));
$import->send();
```

or you can use predefined Subscribe component.

**Feel free to send pull request!**

## Documentation

SmartEmailing API v3 documentation: https://app.smartemailing.cz/docs/api/v3/index.html

## Contributing

Please send Pull Request to the master branch.

## License

SmartEmailing plugin is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT) same as
OctoberCMS platform.
