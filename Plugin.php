<?php namespace VojtaSvoboda\SmartEmailing;

use System\Classes\PluginBase;
use VojtaSvoboda\SmartEmailing\Components\Subscribe;
use VojtaSvoboda\SmartEmailing\Models\Settings;

/**
 * SmartEmailing Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            Subscribe::class => 'smartemailing_subscribe',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'vojtasvoboda.smartemailing.configure' => [
                'tab' => 'SmartEmailing',
                'label' => 'Configure SmartEmailing API credentials.',
            ],
        ];
    }

    /**
     * Registers backend settings model.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'SmartEmailing',
                'icon' => 'icon-envelope',
                'description' => 'Configure SmartEmailing API access.',
                'class' => Settings::class,
                'order' => 600,
                'permissions' => [
                    'vojtasvoboda.smartemailing.configure',
                ],
            ]
        ];
    }
}
