<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $order_group
 * @property int $product_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $subtotal
 * @property numeric $shipping_fee
 * @property numeric $total_amount
 * @property string $fedapay_transaction_id
 * @property string|null $fedapay_token
 * @property string $status
 * @property string $payment_status
 * @property string $customer_name
 * @property string|null $customer_email
 * @property string $customer_phone
 * @property string $customer_address
 * @property string $customer_city
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order processing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order shipped()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order delivered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_group',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'shipping_fee',
        'total_amount',
        'fedapay_transaction_id',
        'fedapay_token',
        'status',
        'payment_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Relation avec le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope pour les commandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les commandes en traitement
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope pour les commandes expédiées
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope pour les commandes livrées
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope pour les commandes annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Obtient le prix total formaté
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtient le prix unitaire formaté
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtient le sous-total formaté
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtient les frais de livraison formatés
     */
    public function getFormattedShippingFeeAttribute()
    {
        return number_format($this->shipping_fee, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtient le statut de la commande en français
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Obtient le statut du paiement en français
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'declined' => 'Refusé',
            'cancelled' => 'Annulé',
        ];

        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Vérifie si la commande est payée
     */
    public function isPaid()
    {
        return $this->payment_status === 'approved' && !is_null($this->paid_at);
    }

    /**
     * Vérifie si la commande peut être annulée
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Vérifie si la commande est terminée
     */
    public function isCompleted()
    {
        return in_array($this->status, ['delivered', 'cancelled']);
    }
}
