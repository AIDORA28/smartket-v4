<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SucursalPerformance extends Model
{
    protected $table = 'sucursal_performance';

    protected $fillable = [
        'sucursal_id',
        'metric_type',
        'metric_name',
        'metric_value',
        'metric_data',
        'period_type',
        'period_start',
        'period_end',
        'category',
        'comparison_value',
        'target_value'
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'comparison_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'metric_data' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    // Relationships
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Scopes
    public function scopeByMetricType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period_type', $period);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    // Helper methods
    public function getFormattedValue(): string
    {
        if ($this->isFinancialMetric()) {
            return 'S/ ' . number_format($this->metric_value, 2);
        }

        if (in_array($this->metric_type, ['percentage', 'efficiency_rate'])) {
            return number_format($this->metric_value, 2) . '%';
        }

        return number_format($this->metric_value);
    }

    public function isFinancialMetric(): bool
    {
        return in_array($this->metric_type, [
            'revenue', 'profit', 'expenses', 'average_sale'
        ]);
    }

    public function isPerformanceMetric(): bool
    {
        return in_array($this->metric_type, [
            'sales_count', 'customers_served', 'efficiency_rate', 'conversion_rate'
        ]);
    }

    public function isAboveTarget(): bool
    {
        return $this->target_value && $this->metric_value >= $this->target_value;
    }

    public function isImprovedFromComparison(): bool
    {
        return $this->comparison_value && $this->metric_value > $this->comparison_value;
    }

    public function getTargetPercentage(): ?float
    {
        if (!$this->target_value || $this->target_value == 0) {
            return null;
        }

        return ($this->metric_value / $this->target_value) * 100;
    }

    public function getComparisonPercentage(): ?float
    {
        if (!$this->comparison_value || $this->comparison_value == 0) {
            return null;
        }

        return (($this->metric_value - $this->comparison_value) / $this->comparison_value) * 100;
    }

    public function getPeriodLabel(): string
    {
        return match($this->period_type) {
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'quarterly' => 'Trimestral',
            'yearly' => 'Anual',
            default => 'Personalizado'
        };
    }

    // Static methods for common metrics
    public static function getRevenueForBranch(int $sucursalId, string $period, $start = null, $end = null)
    {
        return static::where('sucursal_id', $sucursalId)
            ->byMetricType('revenue')
            ->byPeriod($period)
            ->when($start && $end, fn($q) => $q->forPeriod($start, $end))
            ->sum('metric_value');
    }

    public static function getSalesCountForBranch(int $sucursalId, string $period, $start = null, $end = null)
    {
        return static::where('sucursal_id', $sucursalId)
            ->byMetricType('sales_count')
            ->byPeriod($period)
            ->when($start && $end, fn($q) => $q->forPeriod($start, $end))
            ->sum('metric_value');
    }
}
