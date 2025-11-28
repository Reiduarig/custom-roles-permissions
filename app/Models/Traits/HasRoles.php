<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * The roles that belong to the model.
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * The permissions that belong to the model through roles.
     *
     * @return Collection<int, Permission>
     */
    public function permissions(): Collection
    {
        if ($this->relationLoaded('roles')) {
            /** @var Collection<int, Permission> */
            return $this->roles
                ->pluck('permissions')
                ->flatten()
                ->unique('id');
        }

        /** @var Collection<int, Permission> */
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    public function hasRole(string|Role $role): bool
    {
        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }

        return $this->roles->contains('slug', $role);
    }

    public function hasPermission(string|Permission $permission): bool
    {
        if ($permission instanceof Permission) {
            return $this->permissions()->contains('id', $permission->id);
        }

        return $this->permissions()->contains('name', $permission);
    }

    public function assignRole(string|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::query()->where('slug', $role)->firstOrFail();
        }

        // Asigna el rol sin eliminar los existentes
        $this->roles()->syncWithoutDetaching([$role->id]);

        return $this;
    }
}
