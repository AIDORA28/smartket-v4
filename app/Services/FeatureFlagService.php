<?php

namespace App\Services;

use App\Models\FeatureFlag;

class FeatureFlagService
{
    public function isEnabled(string $feature, ?int $empresaId = null): bool
    {
        $empresaId = $empresaId ?: app(TenantService::class)->getEmpresaId();
        
        if (!$empresaId) {
            return false;
        }

        return FeatureFlag::where('empresa_id', $empresaId)
            ->where('feature_key', $feature)
            ->where('enabled', true)
            ->exists();
    }

    public function hasFeature(string $feature, ?int $empresaId = null): bool
    {
        return $this->isEnabled($feature, $empresaId);
    }

    public function enable(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => true]
        );
    }

    public function disable(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => false]
        );
    }

    public function getEnabledFeatures(int $empresaId): array
    {
        return FeatureFlag::where('empresa_id', $empresaId)
            ->where('enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }

    public function setupDefaultFeatures(int $empresaId, array $features): void
    {
        foreach ($features as $feature => $enabled) {
            FeatureFlag::updateOrCreate(
                ['empresa_id' => $empresaId, 'feature_key' => $feature],
                ['enabled' => $enabled]
            );
        }
    }
}
