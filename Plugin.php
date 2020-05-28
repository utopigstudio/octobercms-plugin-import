<?php namespace Utopigs\Import;

use System\Classes\PluginBase;
use Backend;
use BackendMenu;
use BackendAuth;

class Plugin extends PluginBase
{
    public function pluginDetails()
	{
		return [
			'name'			=> 'utopigs.import::lang.plugin.name',
			'description'	=> 'utopigs.import::lang.plugin.description',
			'author'		=> 'Utopig Studio',
			'icon'			=> 'icon-circle'
		];
	}
}