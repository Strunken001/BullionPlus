<?php

namespace App\Http\Helpers;

use App\Http\Helpers\Reloadly;
use Exception;
use App\Models\Admin\MobileTopUpProvider;

use App\Models\Admin\ReloadlyApi;

class MobileTopUpHelper{


    /**
     * Set provider slug
     */
    public string|null $provider_slug = null;

    /**
     * Register provider class
     */
    public array $provider_classes = [
        Reloadly::SLUG      => Reloadly::class,
    ];

    const RESPONSE_WEBHOOK  = "WEBHOOK";
    const RESPONSE_ORDER    = "ORDER";

    /**
     * store provider
     */
    public ?ReloadlyApi $provider = null; 

    public function __construct()
    {
        // store provider
        $this->resolveProvider();
    }

    /**
     * set active provider
     * @param \App\Models\Admin\ReloadlyApi|null $provider
     * @return self
     */
    public function resolveProvider(?ReloadlyApi $provider = null):self
    {
        if(!$provider) $this->provider = ReloadlyApi::active()->mobileTopUp()->first();
        if($provider instanceof ReloadlyApi) $provider = $provider;

        if(is_string($provider)) $provider = ReloadlyApi::where('provider', $provider)->first();
        if(!$this->provider) throw new Exception("Providers not found!");

        return $this;
    }

    /**
     * get provide instance
     * @param \App\Models\Admin\ReloadlyApi|null $provider
     * @return \App\Http\Helpers\MobileTopUpProviders\Reloadly
     */
    public function getInstance(?ReloadlyApi $provider = null)
    {
        $provider       = $this->provider;
        if(!$provider) $provider = $provider;
        if(!$provider) $provider = $this->resolveProvider($this->provider_slug);

        if(!$provider) throw new Exception("Provider Not Found!");
        if(!array_key_exists($provider->provider, $this->provider_classes)) throw new Exception("Does Not Register Provider Class, You Should Bind Provider Slug With Provider Class");

        $provider_class = $this->provider_classes[$provider->provider];

        return new $provider_class($provider);
    }

    /**
     * set provider slug
     * @param string $slug
     */
    public function setProviderSlug(string $slug)
    {
        $this->provider_slug = $slug;
        return $this;
    }
}