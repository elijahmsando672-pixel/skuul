<?php
namespace App\Policies;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;

class TenantPolicy
{
    protected function sameSchool(User $user, Model $model): bool
    {
        $schoolId = TenantContext::schoolId();
        return $schoolId && isset($model->school_id) && (int) $model->school_id === (int) $schoolId;
    }
    public function view(User $user, Model $model): bool { return $this->sameSchool($user, $model); }
    public function update(User $user, Model $model): bool { return $this->sameSchool($user, $model) && $user->hasRole('admin'); }
    public function delete(User $user, Model $model): bool { return $this->sameSchool($user, $model) && $user->hasRole('admin'); }
}
