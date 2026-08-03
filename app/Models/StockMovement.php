<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_IN = 'in';

    public const TYPE_OUT = 'out';

    public const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reason',
        'user_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a stock movement and update the product's stock within a transaction.
     */
    public static function record(Product $product, string $type, int $quantity, ?string $reason = null, ?User $user = null): self
    {
        return DB::transaction(function () use ($product, $type, $quantity, $reason, $user) {
            $product->refresh();

            $previous = (int) $product->stock;

            $new = match ($type) {
                self::TYPE_IN => $previous + $quantity,
                self::TYPE_OUT => max(0, $previous - $quantity),
                self::TYPE_ADJUSTMENT => max(0, $quantity),
                default => throw new \InvalidArgumentException("Unsupported movement type: {$type}"),
            };

            $product->stock = $new;
            $product->save();

            return self::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'previous_stock' => $previous,
                'new_stock' => $new,
                'reason' => $reason,
                'user_id' => $user?->id,
            ]);
        });
    }

    /**
     * Delete the movement and restore the product's stock to its previous value.
     */
    public function revert(): void
    {
        DB::transaction(function () {
            $product = $this->product;
            $product->refresh();
            $product->stock = $this->previous_stock;
            $product->save();

            $this->delete();
        });
    }
}
