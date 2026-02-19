<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_UNREAD = 'UNREAD';
    public const STATUS_READ = 'READ';
    public const STATUS_REPLIED = 'REPLIED';

    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
        'is_read',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    protected static ?string $resolvedTableName = null;
    protected static ?bool $hasStatusColumn = null;
    protected static ?bool $hasIsReadColumn = null;

    public function getTable()
    {
        if (static::$resolvedTableName !== null) {
            return static::$resolvedTableName;
        }

        $defaultTable = parent::getTable();
        if (Schema::hasTable($defaultTable)) {
            static::$resolvedTableName = $defaultTable;
            return $defaultTable;
        }

        if (Schema::hasTable('messages')) {
            static::$resolvedTableName = 'messages';
            return 'messages';
        }

        static::$resolvedTableName = $defaultTable;
        return $defaultTable;
    }

    public static function tableExists(): bool
    {
        $model = new static();
        return Schema::hasTable($model->getTable());
    }

    public static function usesStatusColumn(): bool
    {
        if (static::$hasStatusColumn !== null) {
            return static::$hasStatusColumn;
        }

        $model = new static();
        $table = $model->getTable();
        static::$hasStatusColumn = Schema::hasTable($table) && Schema::hasColumn($table, 'status');

        return static::$hasStatusColumn;
    }

    public static function usesIsReadColumn(): bool
    {
        if (static::$hasIsReadColumn !== null) {
            return static::$hasIsReadColumn;
        }

        $model = new static();
        $table = $model->getTable();
        static::$hasIsReadColumn = Schema::hasTable($table) && Schema::hasColumn($table, 'is_read');

        return static::$hasIsReadColumn;
    }

    public static function normalizeStatus(string $status): string
    {
        $status = strtoupper($status);

        return match ($status) {
            self::STATUS_UNREAD, self::STATUS_READ, self::STATUS_REPLIED => $status,
            default => self::STATUS_UNREAD,
        };
    }

    public function getStatusAttribute($value): string
    {
        if (is_string($value) && $value !== '') {
            return static::normalizeStatus($value);
        }

        if (static::usesIsReadColumn() && array_key_exists('is_read', $this->attributes)) {
            return (bool) $this->attributes['is_read'] ? self::STATUS_READ : self::STATUS_UNREAD;
        }

        return self::STATUS_UNREAD;
    }

    public function setStatusAttribute($value): void
    {
        $status = static::normalizeStatus((string) $value);

        if (static::usesStatusColumn()) {
            $this->attributes['status'] = $status;
        }

        if (static::usesIsReadColumn()) {
            $this->attributes['is_read'] = $status !== self::STATUS_UNREAD;
        }
    }

    public function scopeUnread(Builder $query): Builder
    {
        if (static::usesStatusColumn()) {
            return $query->where('status', self::STATUS_UNREAD);
        }

        if (static::usesIsReadColumn()) {
            return $query->where('is_read', false);
        }

        return $query->whereRaw('1 = 0');
    }

    public function scopeUnreadFirst(Builder $query): Builder
    {
        if (static::usesStatusColumn()) {
            return $query->orderByRaw("FIELD(status, 'UNREAD', 'REPLIED', 'READ')");
        }

        if (static::usesIsReadColumn()) {
            return $query->orderBy('is_read', 'asc');
        }

        return $query;
    }

    public static function unreadCount(): int
    {
        if (! static::tableExists()) {
            return 0;
        }

        return static::query()->unread()->count();
    }

    public function isUnread(): bool
    {
        return $this->status === self::STATUS_UNREAD;
    }

    public function markAsRead(): bool
    {
        return $this->applyStatus(self::STATUS_READ);
    }

    public function markAsReplied(): bool
    {
        return $this->applyStatus(self::STATUS_REPLIED);
    }

    public function applyStatus(string $status): bool
    {
        $this->status = $status;
        return $this->save();
    }
}
