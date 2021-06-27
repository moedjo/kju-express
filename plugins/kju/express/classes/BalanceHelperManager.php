<?php

namespace Kju\Express\Classes;

use Backend\Facades\BackendAuth;
use Kju\Express\Models\Balance;
use Kju\Express\Models\Settings;
use Kju\Express\Models\Transaction;
use October\Rain\Exception\ApplicationException;

class BalanceHelperManager
{
    public function getMyBalance()
    {
        $user = BackendAuth::getUser();
        $branch = $user->branch;
        if (isset($branch)) {
            return $branch->balance;
        }
        return $user->balance;
    }


    public function creditMyBalance($amount = 0, $description = "")
    {
        $balance = $this->getMyBalance();
        return $this->creditBalance($balance->id,$amount,$description);
    }

    public function debitMyBalance($amount = 0, $description = "")
    {
        $balance = $this->getMyBalance();
        return $this->debitBalance($balance->id,$amount,$description);
    }

    public function creditBalance($balance_id,  $amount = 0, $description = "")
    {

        $user = BackendAuth::getUser();

        $enabled_balance = $user->hasPermission('enabled_balances');
        // $enabled_balance = Settings::get('enabled_balance', false);
        if (!$enabled_balance) {
            return;
        }

        $amount = abs($amount);
        if ($amount > 0) {
            $transaction = new Transaction();
            $transaction->description = $description;
            $transaction->amount = 1 * $amount;
            $transaction->balance_id = $balance_id;
            $transaction->save();
        }

    }

    public function debitBalance($balance_id,  $amount = 0, $description = ""){

        $user = BackendAuth::getUser();
        $enabled_balance = $user->hasPermission('enabled_balances');
        // $enabled_balance = Settings::get('enabled_balance', false);
        if (!$enabled_balance) {
            return;
        }

        $amount = abs($amount);
        $balance = Balance::findOrFail($balance_id);
        if ($amount > $balance->balance) {
            throw new ApplicationException(
                e(trans('kju.express::lang.global.insufficient_balance'))
            );
        }
        $transaction = new Transaction();
        $transaction->description = $description;
        $transaction->amount = -1 * $amount;
        $transaction->balance_id = $balance_id;
        $transaction->save();

    }

   
}
