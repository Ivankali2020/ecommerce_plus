<?php

namespace App\Services;

use Spatie\LaravelSettings\Support;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantAwareUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $url = asset('storage/'.$this->getPathRelativeToRoot());

        // $url = $this->versionUrl($this->getPathRelativeToRoot());
        // dd($url);

        return $url;
    }
}