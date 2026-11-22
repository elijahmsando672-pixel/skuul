<?php

namespace App\Http\Controllers\Concerns;

use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesTenant
{
    protected function tenantId(): ?int
    {
        return TenantContext::schoolId();
    }

    protected function authorizeTenant(?Model $model): void
    {
        $schoolId = $this->tenantId();
        if ($schoolId && $model && isset($model->school_id) && (int) $model->school_id !== (int) $schoolId) {
            abort(404);
        }
    }
}
