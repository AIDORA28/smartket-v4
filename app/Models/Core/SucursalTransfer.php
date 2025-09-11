<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SucursalTransfer extends Model
{
    protected $table = 'sucursal_transfers';

    protected $fillable = [
        'from_sucursal_id',
        'to_sucursal_id',
        'transfer_number',
        'transfer_type',
        'status',
        'total_items',
        'total_value',
        'reason',
        'notes',
        'requested_by',
        'approved_by',
        'processed_by',
        'requested_at',
        'approved_at',
        'processed_at',
        'transfer_data'
    ];

    protected $casts = [
        'total_items' => 'integer',
        'total_value' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'transfer_data' => 'array',
    ];

    // Relationships
    public function fromSucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'from_sucursal_id');
    }

    public function toSucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'to_sucursal_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SucursalTransferItem::class, 'transfer_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForSucursal($query, int $sucursalId)
    {
        return $query->where('from_sucursal_id', $sucursalId)
                    ->orWhere('to_sucursal_id', $sucursalId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('transfer_type', $type);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeApproved(): bool
    {
        return $this->isPending() && !$this->approved_at;
    }

    public function canBeProcessed(): bool
    {
        return $this->isApproved() && !$this->processed_at;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']) && !$this->processed_at;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'approved' => 'Aprobado',
            'processed' => 'Procesado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'processed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->transfer_type) {
            'stock_transfer' => 'Transferencia de Stock',
            'emergency_transfer' => 'Transferencia de Emergencia',
            'rebalance_transfer' => 'Rebalanceo de Stock',
            'return_transfer' => 'Transferencia de Devolución',
            default => 'Transferencia General'
        };
    }

    public function getTotalFormattedValue(): string
    {
        return 'S/ ' . number_format($this->total_value, 2);
    }

    // Business logic methods
    public function approve(User $approvedBy): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function process(User $processedBy): bool
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        $this->update([
            'status' => 'processed',
            'processed_by' => $processedBy->id,
            'processed_at' => now(),
        ]);

        // TODO: Implement actual stock transfer logic
        // $this->processStockTransfer();

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . ($reason ? "\nCancelado: " . $reason : ''),
        ]);

        return true;
    }
}
