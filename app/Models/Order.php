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
        'notes'
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
     * Scope pour les commandes d'un même groupe
     */
    public function scopeByOrderGroup($query, $orderGroup)
    {
        return $query->where('order_group', $orderGroup);
    }

    /**
     * Scope pour les commandes payées
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'approved');
    }

    /**
     * Scope pour les commandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Getter pour le statut formaté
     */
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Getter pour le statut de paiement formaté
     */
    public function getFormattedPaymentStatusAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'approved' => 'Payée',
            'declined' => 'Refusée',
            'cancelled' => 'Annulée'
        ];

        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Vérifie si la commande est payée
     */
    public function isPaid()
    {
        return $this->payment_status === 'approved';
    }

    /**
     * Vérifie si la commande peut être annulée
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
