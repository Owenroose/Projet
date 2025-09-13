<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'phone',
        'address',
        'city', // Ajout
        'quantity',
        'total_price',
        'status',
        'is_priority',
        'notes',
        'status_updated_at',
        'order_group',
        'payment_method', // Ajout
        'fedapay_token',  // Ajout
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'is_priority' => 'boolean',
        'status_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending_payment', // Mise à jour pour les paiements en ligne
        'is_priority' => false,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    /**
     * Accesseur pour formater le prix
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accesseur pour le statut traduit
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En cours de traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée'
        ];

        return $labels[$this->status] ?? 'Inconnu';
    }

    /**
     * Accesseur pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'orange',
            'processing' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les commandes prioritaires
     */
    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }

    /**
     * Scope pour les commandes récentes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope pour rechercher dans les commandes
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhereHas('product', function($productQuery) use ($term) {
                  $productQuery->where('name', 'like', "%{$term}%");
              });
        });
    }

    /**
     * Méthode pour mettre à jour le statut avec timestamp
     */
    public function updateStatus($newStatus)
    {
        $this->update([
            'status' => $newStatus,
            'status_updated_at' => now()
        ]);
    }

    /**
     * Méthode pour ajouter une note
     */
    public function addNote($content, $author)
    {
        $notes = $this->notes ? json_decode($this->notes, true) : [];

        $notes[] = [
            'content' => $content,
            'author' => $author,
            'created_at' => now()->toDateTimeString()
        ];

        $this->update(['notes' => json_encode($notes)]);
    }

    /**
     * Accesseur pour les notes formatées
     */
    public function getNotesArrayAttribute()
    {
        return $this->notes ? json_decode($this->notes, true) : [];
    }

    /**
     * Méthode pour calculer le temps écoulé depuis la commande
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ]);
    }

    /**
     * Scope pour les commandes d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope pour les commandes de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour les commandes de ce mois
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope pour les commandes avec revenus (livrées, expédiées, en cours)
     */
    public function scopeWithRevenue($query)
    {
        return $query->whereIn('status', ['delivered', 'shipped', 'processing']);
    }

    /**
     * Mutateur pour formater le numéro de téléphone
     */
    public function setPhoneAttribute($value)
    {
        // Nettoyer et formater le numéro de téléphone
        $this->attributes['phone'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Accesseur pour formater le numéro de téléphone pour WhatsApp
     */
    public function getWhatsappPhoneAttribute()
    {
        return str_replace(['+', ' ', '-', '(', ')'], '', $this->phone);
    }

    /**
     * Accesseur pour l'URL Google Maps
     */
    public function getGoogleMapsUrlAttribute()
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->address);
    }

    /**
     * Méthode pour marquer comme prioritaire
     */
    public function markAsPriority($isPriority = true)
    {
        return $this->update(['is_priority' => $isPriority]);
    }

    /**
     * Méthode pour obtenir le prochain statut logique
     */
    public function getNextStatusAttribute()
    {
        $statusFlow = [
            'pending' => 'processing',
            'processing' => 'shipped',
            'shipped' => 'delivered',
            'delivered' => null,
            'cancelled' => null
        ];

        return $statusFlow[$this->status] ?? null;
    }

    /**
     * Méthode pour obtenir le statut précédent
     */
    public function getPreviousStatusAttribute()
    {
        $statusFlow = [
            'processing' => 'pending',
            'shipped' => 'processing',
            'delivered' => 'shipped',
            'pending' => null,
            'cancelled' => null
        ];

        return $statusFlow[$this->status] ?? null;
    }

    /**
     * Vérifier si la commande peut être modifiée
     */
    public function canBeModified()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Vérifier si la commande peut être annulée
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Vérifier si la commande est terminée
     */
    public function isCompleted()
    {
        return in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Calculer le délai de livraison estimé
     */
    public function getEstimatedDeliveryAttribute()
    {
        $businessDays = 3; // Délai standard en jours ouvrables

        switch ($this->status) {
            case 'pending':
                return $this->created_at->addBusinessDays($businessDays + 2);
            case 'processing':
                return $this->created_at->addBusinessDays($businessDays);
            case 'shipped':
                return $this->created_at->addBusinessDays(1);
            default:
                return null;
        }
    }

    /**
     * Obtenir la couleur CSS pour le statut
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'badge bg-warning',
            'processing' => 'badge bg-info',
            'shipped' => 'badge bg-primary',
            'delivered' => 'badge bg-success',
            'cancelled' => 'badge bg-danger'
        ];

        return $classes[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Obtenir l'icône pour le statut
     */
    public function getStatusIconAttribute()
    {
        $icons = [
            'pending' => 'bx bx-time-five',
            'processing' => 'bx bx-loader-alt bx-spin',
            'shipped' => 'bx bx-package',
            'delivered' => 'bx bx-check-circle',
            'cancelled' => 'bx bx-x-circle'
        ];

        return $icons[$this->status] ?? 'bx bx-help-circle';
    }

    /**
     * Générer un numéro de suivi factice
     */
    public function getTrackingNumberAttribute()
    {
        if (in_array($this->status, ['shipped', 'delivered'])) {
            return 'NT' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
        }
        return null;
    }

    /**
     * Calculer la commission ou les frais
     */
    public function getCommissionAttribute()
    {
        $commissionRate = 0.05; // 5% de commission
        return $this->total_price * $commissionRate;
    }

    /**
     * Obtenir les statistiques de délai
     */
    public function getProcessingTimeAttribute()
    {
        if ($this->status_updated_at) {
            return $this->status_updated_at->diffInHours($this->created_at);
        }
        return $this->created_at->diffInHours(now());
    }

    /**
     * Scope pour les commandes en retard
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'delivered')
                    ->where('status', '!=', 'cancelled')
                    ->where('created_at', '<', now()->subDays(5));
    }

    /**
     * Méthode statique pour les statistiques rapides
     */
    public static function getQuickStats()
    {
        return [
            'total' => self::count(),
            'pending' => self::where('status', 'pending')->count(),
            'processing' => self::where('status', 'processing')->count(),
            'shipped' => self::where('status', 'shipped')->count(),
            'delivered' => self::where('status', 'delivered')->count(),
            'cancelled' => self::where('status', 'cancelled')->count(),
            'priority' => self::where('is_priority', true)->count(),
            'today' => self::whereDate('created_at', today())->count(),
            'this_month' => self::whereMonth('created_at', now()->month)->count(),
            'revenue' => self::whereIn('status', ['delivered', 'shipped', 'processing'])->sum('total_price'),
            'overdue' => self::overdue()->count()
        ];
    }

    /**
     * Boot du modèle pour les événements
     */
    protected static function boot()
    {
        parent::boot();

        // Événement lors de la création d'une commande
        static::creating(function ($order) {
            // Assigner un ID de référence unique si nécessaire
            if (!$order->reference) {
                $order->reference = 'CMD-' . strtoupper(uniqid());
            }
        });

        // Événement lors de la mise à jour du statut
        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                $order->status_updated_at = now();
            }
        });
    }

    /**
     * Relations polymorphes pour les notifications (optionnel)
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Relation avec les logs d'activité (optionnel)
     */
    public function activities()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject');
    }

    /**
     * Convertir en array pour export
     */
    public function toExportArray()
    {
        return [
            'ID' => $this->id,
            'Référence' => $this->reference ?? 'CMD-' . $this->id,
            'Client' => $this->name,
            'Email' => $this->email,
            'Téléphone' => $this->phone,
            'Produit' => $this->product->name,
            'Marque' => $this->product->brand ?? 'N/A',
            'Quantité' => $this->quantity,
            'Prix Unitaire' => number_format($this->product->price, 0, ',', ' ') . ' FCFA',
            'Total' => $this->formatted_price,
            'Statut' => $this->status_label,
            'Prioritaire' => $this->is_priority ? 'Oui' : 'Non',
            'Adresse' => $this->address,
            'Date de commande' => $this->created_at->format('d/m/Y H:i'),
            'Dernière modification' => $this->updated_at->format('d/m/Y H:i'),
            'Temps de traitement (h)' => $this->processing_time,
        ];
    }
}
