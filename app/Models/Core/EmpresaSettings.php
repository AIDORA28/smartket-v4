<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaSettings extends Model
{
    protected $table = 'empresa_settings';

    protected $fillable = [
        'empresa_id',
        'timezone',
        'currency',
        'language',
        'date_format',
        'time_format',
        'email_settings',
        'notification_settings',
        'business_hours',
        'invoice_settings',
        'tax_settings',
        'address_settings'
    ];

    protected $casts = [
        'email_settings' => 'array',
        'notification_settings' => 'array',
        'business_hours' => 'array',
        'invoice_settings' => 'array',
        'tax_settings' => 'array',
        'address_settings' => 'array',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // Helper methods
    public function getTimezone(): string
    {
        return $this->timezone ?? 'America/Lima';
    }

    public function getCurrency(): string
    {
        return $this->currency ?? 'PEN';
    }

    public function getLanguage(): string
    {
        return $this->language ?? 'es';
    }

    public function getDateFormat(): string
    {
        return $this->date_format ?? 'd/m/Y';
    }

    public function getTimeFormat(): string
    {
        return $this->time_format ?? 'H:i';
    }

    public function getBusinessHours(): array
    {
        return $this->business_hours ?? [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'closed' => false],
            'saturday' => ['open' => '08:00', 'close' => '14:00', 'closed' => false],
            'sunday' => ['open' => '00:00', 'close' => '00:00', 'closed' => true],
        ];
    }

    public function getNotificationSettings(): array
    {
        return $this->notification_settings ?? [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'low_stock_alerts' => true,
            'sales_notifications' => true,
            'payment_reminders' => true,
        ];
    }

    public function getInvoiceSettings(): array
    {
        return $this->invoice_settings ?? [
            'auto_numbering' => true,
            'invoice_prefix' => 'INV',
            'default_terms' => 'Net 30',
            'show_discount' => true,
            'show_tax' => true,
            'footer_text' => '',
        ];
    }
}
