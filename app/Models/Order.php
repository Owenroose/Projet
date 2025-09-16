<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Get the product associated with the order.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order history for this order group.
     */
    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_group', 'order_group');
    }

    /**
     * Get the delivery assignment for this order group.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_group', 'order_group');
    }

    /**
     * Get the total amount formatted.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get the subtotal formatted.
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get the unit price formatted.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get the shipping fee formatted.
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
            'processing' => 'En cours de traitement',
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
        return $this->status === 'delivered';
    }
}
