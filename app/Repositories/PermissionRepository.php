<?php

namespace App\Repositories;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Get all permissions with pagination
     *
     * @param int $perPage
     * @param string|null $search
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $search = null, array $filters = []): LengthAwarePaginator
    {
        $query = Permission::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply guard name filter
        if (!empty($filters['guard_name'])) {
            $query->where('guard_name', $filters['guard_name']);
        }

        // Apply created from filter
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        // Apply created to filter
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all permissions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Find permission by ID
     *
     * @param int $id
     * @return Permission|null
     */
    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    /**
     * Create a new permission
     *
     * @param array $data
     * @return Permission
     */
    public function create(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    /**
     * Update permission
     *
     * @param int $id
     * @param array $data
     * @return Permission
     */
    public function update(int $id, array $data): Permission
    {
        $permission = $this->findById($id);

        $permission->update([
            'name' => $data['name'] ?? $permission->name,
            'display_name' => $data['display_name'] ?? $permission->display_name,
            'description' => $data['description'] ?? $permission->description,
        ]);

        return $permission->fresh();
    }

    /**
     * Delete permission
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $permission = $this->findById($id);

        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }

    /**
     * Check if permission exists by name
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $query = Permission::where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
