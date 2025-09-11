<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationBranding extends Model
{
    protected $table = 'organization_branding';

    protected $fillable = [
        'empresa_id',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
        'font_family',
        'theme_mode',
        'theme_settings',
        'custom_css',
        'brand_description',
        'social_links'
    ];

    protected $casts = [
        'theme_settings' => 'array',
        'social_links' => 'array',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // Helper methods
    public function getPrimaryColor(): string
    {
        return $this->primary_color ?? '#dc2626'; // SmartKet red
    }

    public function getSecondaryColor(): string
    {
        return $this->secondary_color ?? '#991b1b';
    }

    public function getAccentColor(): string
    {
        return $this->accent_color ?? '#fbbf24';
    }

    public function getTextColor(): string
    {
        return $this->text_color ?? '#111827';
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color ?? '#ffffff';
    }

    public function getFontFamily(): string
    {
        return $this->font_family ?? 'Inter, system-ui, sans-serif';
    }

    public function getThemeMode(): string
    {
        return $this->theme_mode ?? 'light';
    }

    public function getThemeSettings(): array
    {
        return $this->theme_settings ?? [
            'rounded_corners' => true,
            'shadow_style' => 'medium',
            'animation_speed' => 'normal',
            'compact_mode' => false,
            'sidebar_style' => 'default',
        ];
    }

    public function getSocialLinks(): array
    {
        return $this->social_links ?? [
            'website' => '',
            'facebook' => '',
            'instagram' => '',
            'twitter' => '',
            'linkedin' => '',
            'whatsapp' => '',
        ];
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo_url);
    }

    public function hasFavicon(): bool
    {
        return !empty($this->favicon_url);
    }

    public function hasCustomTheme(): bool
    {
        return $this->primary_color !== '#dc2626' || 
               $this->secondary_color !== '#991b1b' ||
               !empty($this->custom_css);
    }

    public function getCssVariables(): array
    {
        return [
            '--primary-color' => $this->getPrimaryColor(),
            '--secondary-color' => $this->getSecondaryColor(),
            '--accent-color' => $this->getAccentColor(),
            '--text-color' => $this->getTextColor(),
            '--background-color' => $this->getBackgroundColor(),
            '--font-family' => $this->getFontFamily(),
        ];
    }
}
