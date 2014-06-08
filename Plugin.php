<?php namespace SoBoRed\Rss;

use Backend;
use Controller;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'Blog RSS Feed',
            'description' => 'An RSS feed generator for the RainLab Blog plugin',
            'author'      => 'Josh Hall',
            'icon'        => 'icon-rss'
        ];
    }

    public function registerComponents()
    {
        return [
            'SoBoRed\Rss\Components\Link' => 'rssLink'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Blog RSS Settings',
                'description' => 'Manage the Blog RSS settings.',
                'category'    => 'Blog',
                'icon'        => 'icon-rss',
                'class'       => 'SoBoRed\Rss\Models\Settings',
                'order'       => 100
            ]
        ];
    }
}