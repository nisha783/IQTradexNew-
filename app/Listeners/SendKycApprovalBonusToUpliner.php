<?php

namespace App\Listeners;

use App\Events\KycUserVerified;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendKycApprovalBonusToUpliner implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(KycUserVerified $event): void
    {
        info("SendKycApprovalBonusToUpliner: " . $event->user->name);
        // checking if this user has valid upliner
        $user = $event->user;

        // sending bonus to this user
        $bonus_amount = $user->settings('kyc_approval_bonus');
        if ($user->userPlans->count() == 0) {
            $transaction = Transaction::firstOrCreate([
                'user_id' => $user->id,
                'amount' => $bonus_amount,
                'status' => 'approved',
                'type' => 'kyc bonus',
                'sum' => true,
                'payment_status' => false,
                'reference' => 'Kyc Approval Bonus',
            ]);
        }

        if ($user->upliner) {
            $upliner = $user->upliner;
            $bonus_amount = $user->settings('kyc_approval_bonus_for_upliner');
            info('Bonus amount: ' . $bonus_amount);
            // checking if this user has already active plan
            if ($upliner->userPlans->count() == 0) {
                info("Upliner {$upliner->name} has no active plan");
                // adding commission
                // checking if the upline KYC is approved
                if ($upliner->kyc && $upliner->kyc->status == 'approved') {
                    info('Upliner KYC is approved');

                    // checking if this user already get kyc bonus more then 8 time
                    info($upliner->transactions()->where('type', 'kyc bonus')->where('additional_type', 'kyc bonus from downline')->count());
                    if ($upliner->transactions()->where('type', 'kyc bonus')->where('additional_type', 'kyc bonus from downline')->count() < 8) {
                        $transaction = Transaction::firstOrCreate([
                            'user_id' => $upliner->id,
                            'amount' => $bonus_amount,
                            'status' => 'approved',
                            'type' => 'kyc bonus',
                            'additional_type' => 'kyc bonus from downline',
                            'payment_status' => false,
                            'sum' => true,
                            'reference' => 'Bonus from ' . $user->username . ' on KYC approval',
                        ]);
                        info('Transaction added: ' . $transaction->id);
                    } else {
                        info('User already get kyc bonus more then 8 time');
                    }
                } else {
                    info('Upliner KYC is not approved');
                }
            } else {
                info('User not qualified for kyc bonus: because has active plan');
            }
        }
    }
}
