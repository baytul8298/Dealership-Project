<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @param string|null $search
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $search = null, array $filters = []): LengthAwarePaginator
    {
        $query = User::query()
            ->with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%");
            });
        }

        // Apply user type filter
        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
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
     * Get all users
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return User::orderBy('name')->get();
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        // Encode password as base64 and store in salt
        if (isset($data['password'])) {
            $data['salt'] = base64_encode($data['password']);
        }

        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // Will be hashed by Laravel
            'salt' => $data['salt'] ?? null,
            'user_type' => $data['user_type'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'address' => $data['address'] ?? null,
            'language' => $data['language'] ?? 'en',
            'status' => $data['status'] ?? true,
            'business_id' => $data['business_id'] ?? null,
        ]);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);

        // Encode password as base64 and store in salt if password is being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['salt'] = base64_encode($data['password']);
        } else {
            // Remove password from update if not provided
            unset($data['password']);
            unset($data['salt']);
        }

        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'username' => $data['username'] ?? $user->username,
            'email' => $data['email'] ?? $user->email,
            'user_type' => $data['user_type'] ?? $user->user_type,
            'contact_no' => $data['contact_no'] ?? $user->contact_no,
            'address' => $data['address'] ?? $user->address,
            'language' => $data['language'] ?? $user->language,
            'status' => $data['status'] ?? $user->status,
        ];

        // Add password and salt if they exist in data
        if (isset($data['password'])) {
            $updateData['password'] = $data['password'];
            $updateData['salt'] = $data['salt'];
        }

        $user->update($updateData);

        return $user->fresh(['roles']);
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Check if user exists by email
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $query = User::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if user exists by username
     *
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByUsername(string $username, ?int $excludeId = null): bool
    {
        $query = User::where('username', $username);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Assign role to user
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     */
    public function assignRole(int $userId, int $roleId): void
    {
        $user = $this->findById($userId);

        if ($user) {
            $user->syncRoles([$roleId]);
        }
    }
}
