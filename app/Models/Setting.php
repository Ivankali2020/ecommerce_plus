<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'seller_id',
        'tenant_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get privacy policy content
     */
    public function getPrivacyPolicyAttribute(): ?string
    {
        return $this->data['privacy_policy'] ?? null;
    }

    /**
     * Get terms and conditions content
     */
    public function getTermsConditionsAttribute(): ?string
    {
        return $this->data['terms_conditions'] ?? null;
    }

    /**
     * Get about us content
     */
    public function getAboutUsAttribute(): ?string
    {
        return $this->data['about_us'] ?? null;
    }

    /**
     * Get returns and exchanges policy content
     */
    public function getReturnsExchangesAttribute(): ?string
    {
        return $this->data['returns_exchanges'] ?? null;
    }

    /**
     * Get FAQ items
     */
    public function getFaqAttribute(): array
    {
        return $this->data['faq'] ?? [];
    }

    /**
     * Get social media links
     */
    public function getSocialLinksAttribute(): array
    {
        return [
            'facebook' => $this->data['facebook'] ?? null,
            'twitter' => $this->data['twitter'] ?? null,
            'youtube' => $this->data['youtube'] ?? null,
            'instagram' => $this->data['instagram'] ?? null,
        ];
    }
}
