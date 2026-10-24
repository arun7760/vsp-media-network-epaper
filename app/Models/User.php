<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($this->role, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isReviewer(): bool
    {
        return in_array($this->role, ['reviewer', 'approver'], true);
    }

    public function canUpload(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin', 'editor'], true);
    }

    public function canEdit(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin', 'editor'], true);
    }

    public function canApprove(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin', 'approver', 'reviewer'], true);
    }

    public function canPublish(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin', 'publisher'], true);
    }

    public function canDelete(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function canManageUsers(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function canViewAuditLogs(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function canManageSettings(): bool
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }
}
