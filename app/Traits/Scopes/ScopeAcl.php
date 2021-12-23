<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Youth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait ScopeAcl
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeAcl($query): mixed
    {
        /** @var Youth $authUser */
        $authUser = Auth::user();
        $tableName = $this->getTable();

        if ($authUser) {
            if (Schema::hasColumn($tableName, 'youth_id')) {
                $query = $query->where($tableName . '.youth_id', Auth::id());
            }
        }
        return $query;
    }

}
