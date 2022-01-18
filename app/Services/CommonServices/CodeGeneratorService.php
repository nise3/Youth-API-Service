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
        $youthCode = "";
        DB::beginTransaction();
        try {
            /** @var YouthCodePessimisticLocking $existingCode */
            $existingCode = YouthCodePessimisticLocking::lockForUpdate()->first();
            $code = !empty($existingCode) && !empty($existingCode->code) ? $existingCode->code : 0;
            $code = $code + 1;
            $padSize = Youth::YOUTH_CODE_SIZE - strlen($code);

            /**
             * Prefix+000000N. Ex: Y+ incremental value
             */
            $youthCode = str_pad(Youth::YOUTH_CODE_PREFIX, $padSize, '0', STR_PAD_RIGHT) . $code;

            /**
             * Code Update
             */
            if ($existingCode) {
                $existingCode->code = $code;
                $existingCode->save();
            } else {
                YouthCodePessimisticLocking::create([
                    "code" => $code
                ]);
            }
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return $youthCode;
    }
}
