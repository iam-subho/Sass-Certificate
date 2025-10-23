<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Throwable;

trait HandlesTransactions
{
    /**
     * Execute a callback within a database transaction
     *
     * @param callable $callback
     * @param string $errorPrefix
     * @return mixed
     */
    protected function executeInTransaction(callable $callback, string $errorPrefix = 'Operation failed')
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();

            // Log the error
            logger()->error($errorPrefix . ': ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', $errorPrefix . ': ' . $e->getMessage());
        }
    }

    /**
     * Execute a callback within a transaction and redirect on success
     *
     * @param callable $callback
     * @param string $redirectRoute
     * @param string $successMessage
     * @param string $errorPrefix
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function executeInTransactionWithRedirect(
        callable $callback,
        string $redirectRoute,
        string $successMessage,
        string $errorPrefix = 'Operation failed'
    ) {
        DB::beginTransaction();

        try {
            $callback();
            DB::commit();

            return redirect()->route($redirectRoute)->with('success', $successMessage);
        } catch (Throwable $e) {
            DB::rollBack();

            // Log the error
            logger()->error($errorPrefix . ': ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', $errorPrefix . ': ' . $e->getMessage());
        }
    }
}
