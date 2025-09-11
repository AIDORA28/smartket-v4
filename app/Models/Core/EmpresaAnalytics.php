<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaAnalytics extends Model
{
    protected $table = 'empresa_analytics';

    protected $fillable = [
        'empresa_id',
        'metric_type',
        'metric_name',
        'metric_value',
        'metric_data',
        'period_type',
        'period_start',
        'period_end',
        'category'
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'metric_data' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
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
    public function isFinancialMetric(): bool
    {
        return in_array($this->metric_type, [
            'revenue', 'profit', 'expenses', 'gross_margin'
        ]);
    }

    public function isOperationalMetric(): bool
    {
        return in_array($this->metric_type, [
            'sales_count', 'orders_count', 'customers_count', 'products_sold'
        ]);
    }

    public function isInventoryMetric(): bool
    {
        return in_array($this->metric_type, [
            'stock_level', 'inventory_value', 'turnover_rate', 'low_stock_items'
        ]);
    }

    public function getFormattedValue(): string
    {
        if ($this->isFinancialMetric()) {
            return 'S/ ' . number_format($this->metric_value, 2);
        }

        if (in_array($this->metric_type, ['percentage', 'margin_percentage'])) {
            return number_format($this->metric_value, 2) . '%';
        }

        return number_format($this->metric_value);
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
    public static function getRevenueForPeriod(int $empresaId, string $period, $start = null, $end = null)
    {
        return static::where('empresa_id', $empresaId)
            ->byMetricType('revenue')
            ->byPeriod($period)
            ->when($start && $end, fn($q) => $q->forPeriod($start, $end))
            ->sum('metric_value');
    }

    public static function getSalesCountForPeriod(int $empresaId, string $period, $start = null, $end = null)
    {
        return static::where('empresa_id', $empresaId)
            ->byMetricType('sales_count')
            ->byPeriod($period)
            ->when($start && $end, fn($q) => $q->forPeriod($start, $end))
            ->sum('metric_value');
    }
}
