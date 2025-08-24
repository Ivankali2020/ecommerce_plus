<?php

namespace App\Listeners;

use App\Models\ReferralHistory;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AwardReferralPoints implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        try {
            DB::beginTransaction();

            $user = $event->user;

            // Check if user was referred
            if (!$user->referred_by) {
                DB::commit();
                return;
            }

            // Get the referrer
            $referrer = User::find($user->referred_by);
            if (!$referrer) {
                DB::commit();
                return;
            }

            // Get referral points from seller settings
            $referralPoints = $referrer->seller->setting->data['referral_points'] ?? 0;
            if ($referralPoints <= 0) {
                DB::commit();
                return;
            }

            // Create referral history
            $referralHistory = ReferralHistory::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
                'seller_id' => $user->seller_id,
                'tenant_id' => $user->tenant_id,
                'points_awarded' => $referralPoints,
                'verified_at' => now(),
            ]);

            // Award points to referrer using points service
            $pointservice = app()->make(PointService::class);
            $pointservice->awardReferralPoints($referrer, $user);

            //clear cache for referral stats
            Cache::forget("user_referral_stats_{$referrer->id}");

            DB::commit();

            Log::info('Referral points awarded', [
                'referral_history_id' => $referralHistory->id,
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
                'points' => $referralPoints,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award referral points', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Referral points award job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
