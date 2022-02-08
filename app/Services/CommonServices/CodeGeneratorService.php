<?php

namespace App\Services\CommonServices;

use App\Models\Youth;
use App\Models\YouthCodePessimisticLocking;
use Illuminate\Support\Facades\DB;
use Throwable;

class CodeGeneratorService
{

    /**
     * @throws Throwable
     */
    public static function getYouthCode(): string
    {
        DB::beginTransaction();
        try {
            /** @var YouthCodePessimisticLocking $lastEntity */
            $lastEntity = YouthCodePessimisticLocking::lockForUpdate()->first();
            $lastIncrementalVal = !empty($lastEntity) && $lastEntity->last_incremental_value ? $lastEntity->last_incremental_value : 0;
            $lastIncrementalVal = $lastIncrementalVal + 1;
            $padSize = Youth::YOUTH_CODE_SIZE - strlen($lastIncrementalVal);

            /**
             * Prefix+000000N. Ex: Y+ incremental value
             */
            $youthCode = str_pad(Youth::YOUTH_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $lastIncrementalVal;

            /**
             * Code Update
             */
            if ($lastEntity) {
                $lastEntity->last_incremental_value = $lastIncrementalVal;
                $lastEntity->save();
            } else {
                YouthCodePessimisticLocking::create([
                    "last_incremental_value" => $lastIncrementalVal
                ]);
            }
            DB::commit();
            return $youthCode;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

    }
}
