<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'delivery_type',
        'delivery_address',
        'status',
        'notes',
        'total',
        'payment_method',
        'cancel_reason',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Get linked member via user
     */
    public function member()
    {
        return $this->user?->member();
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'processing', 'ready']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ===== HELPERS =====

    /**
     * Generate unique booking code: BK-YYYYMMDD-XXX
     */
    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "BK-{$date}-";

        $lastBooking = static::where('booking_code', 'like', "{$prefix}%")
            ->orderBy('booking_code', 'desc')
            ->first();

        if ($lastBooking) {
            // Ambil 3 digit sebelum suffix random
            $lastNumber = (int) substr($lastBooking->booking_code, strlen($prefix), 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // BUG-01: Tambah random suffix untuk mencegah collision
        $randomSuffix = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '-' . $randomSuffix;
    }

    /**
     * Check if booking can be accepted
     */
    public function canBeAccepted(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        // BUG-02: Include 'processing' agar reject bisa kembalikan stok
        return in_array($this->status, ['pending', 'confirmed', 'processing']);
    }

    /**
     * Check if booking can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'ready';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'ready' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }
}
