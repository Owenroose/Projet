<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $company
 * @property string $subject
 * @property string $message
 * @property bool $read
 * @property string $status
 * @property string $priority
 * @property int|null $assigned_to
 * @property string|null $response
 * @property \Illuminate\Support\Carbon|null $response_sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assignedTo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact priority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact status($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereResponseSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'read',
        'status',
        'assigned_to',
        'priority',
        'response',
        'response_sent_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'response_sent_at' => 'datetime'
    ];

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function markAsRead()
    {
        $this->update(['read' => true]);
    }

    public function markAsUnread()
    {
        $this->update(['read' => false]);
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NEW => 'Nouveau',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_RESOLVED => 'Résolu',
            self::STATUS_CLOSED => 'Fermé'
        ];
    }

    public static function getPriorityOptions()
    {
        return [
            self::PRIORITY_LOW => 'Basse',
            self::PRIORITY_MEDIUM => 'Moyenne',
            self::PRIORITY_HIGH => 'Haute',
            self::PRIORITY_URGENT => 'Urgente'
        ];
    }

    public function hasResponse()
    {
        return !is_null($this->response_sent_at);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
