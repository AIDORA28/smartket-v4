<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SucursalSettings extends Model
{
    protected $table = 'sucursal_settings';

    protected $fillable = [
        'sucursal_id',
        'inventory_mode',
        'pos_mode',
        'auto_restock',
        'low_stock_threshold',
        'printer_settings',
        'cash_settings',
        'receipt_settings',
        'display_settings',
        'security_settings',
        'notification_settings',
        'working_hours',
    ];

    protected $casts = [
        'auto_restock' => 'boolean',
        'low_stock_threshold' => 'integer',
        'printer_settings' => 'array',
        'cash_settings' => 'array',
        'receipt_settings' => 'array',
        'display_settings' => 'array',
        'security_settings' => 'array',
        'notification_settings' => 'array',
        'working_hours' => 'array',
    ];

    // Relationships
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Helper methods
    public function getInventoryMode(): string
    {
        return $this->inventory_mode ?? 'standard';
    }

    public function getPosMode(): string
    {
        return $this->pos_mode ?? 'full';
    }

    public function hasAutoRestock(): bool
    {
        return $this->auto_restock ?? false;
    }

    public function getLowStockThreshold(): int
    {
        return $this->low_stock_threshold ?? 10;
    }

    public function getPrinterSettings(): array
    {
        return $this->printer_settings ?? [
            'default_printer' => '',
            'receipt_printer' => '',
            'barcode_printer' => '',
            'auto_print_receipts' => true,
            'print_customer_copy' => true,
            'paper_size' => '80mm',
        ];
    }

    public function getCashSettings(): array
    {
        return $this->cash_settings ?? [
            'require_cash_count' => true,
            'allow_negative_balance' => false,
            'auto_reconcile' => false,
            'daily_limit' => 5000.00,
            'warning_threshold' => 4000.00,
        ];
    }

    public function getReceiptSettings(): array
    {
        return $this->receipt_settings ?? [
            'show_logo' => true,
            'show_address' => true,
            'show_phone' => true,
            'show_tax_details' => true,
            'footer_message' => 'Gracias por su compra',
            'qr_code' => false,
        ];
    }

    public function getDisplaySettings(): array
    {
        return $this->display_settings ?? [
            'customer_display' => false,
            'show_prices' => true,
            'show_stock' => false,
            'theme' => 'default',
            'language' => 'es',
        ];
    }

    public function getSecuritySettings(): array
    {
        return $this->security_settings ?? [
            'require_supervisor_override' => false,
            'max_discount_percentage' => 20,
            'require_reason_for_voids' => true,
            'lock_after_hours' => true,
            'audit_all_transactions' => true,
        ];
    }

    public function getWorkingHours(): array
    {
        return $this->working_hours ?? [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'saturday' => ['open' => '08:00', 'close' => '14:00', 'closed' => false],
            'sunday' => ['open' => '00:00', 'close' => '00:00', 'closed' => true],
        ];
    }
}
