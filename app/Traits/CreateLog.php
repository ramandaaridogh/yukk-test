<?php

namespace App\Traits;

use Exception;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log as LogHelper;
use Throwable;

trait CreateLog
{

    /**
     * writeLog
     *
     * @param  mixed $message
     * @return string (traceback id)
     */
    public function writeLog(Throwable $e): string
    {
        $traceBackId = str_replace('', '', Uuid::uuid4());

        $message = $e->getFile() . ":" . $e->getLine() . "\n";
        $message .= $e->getMessage() . "\n[stacktrace]\n";
        $message .= $e->getTraceAsString();
        // $message .= 'ini trace';

        $error = "{$traceBackId} : {$message}";

        LogHelper::error($error);

        return $traceBackId;
    }
}
