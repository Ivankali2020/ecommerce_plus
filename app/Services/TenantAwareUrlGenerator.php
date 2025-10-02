<?php

namespace App\Services;

use Spatie\LaravelSettings\Support;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantAwareUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $path = $this->getPathRelativeToRoot();
        
        // If path is null (during upload processing), fall back to parent behavior
        if ($path === null) {
            return parent::getUrl();
        }
        
        return asset('storage/'.$path);
    }
}