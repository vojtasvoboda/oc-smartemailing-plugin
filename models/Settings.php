<?php namespace VojtaSvoboda\SmartEmailing\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation as ValidationTrait;

class Settings extends Model
{
    use ValidationTrait;

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'vojtasvoboda_smartemailing_settings';

    public $settingsFields = 'fields.yaml';

    public $rules = [
        'api_username' => 'required',
        'api_key' => 'required',
    ];
}
