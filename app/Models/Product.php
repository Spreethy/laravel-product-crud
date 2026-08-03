<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            $product->syncLowStockAlert();
        });
    }

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'reorder_level',
        'is_active',
        'category_id',
        'supplier_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function openAlerts(): HasMany
    {
        return $this->stockAlerts()->open();
    }

    /**
     * Keep a single open low-stock alert per product in sync with the stock level.
     */
    private function syncLowStockAlert(): void
    {
        if ($this->trashed()) {
            return;
        }

        $open = $this->stockAlerts()->open()->first();

        if ($this->stock <= $this->reorder_level) {
            if (! $open) {
                $this->stockAlerts()->create([
                    'type' => StockAlert::TYPE_LOW_STOCK,
                    'status' => StockAlert::STATUS_OPEN,
                ]);
            }
        } elseif ($open) {
            $open->markResolved();
        }
    }
}
