<?php
namespace App\Policies;

use App\Models\LokasiMonitoring;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LokasiPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the kebun.
     */
    public function update(User $user, LokasiMonitoring $lokasimonitoring)
    {
        return $user->id === $lokasimonitoring->user_id;
    }

    /**
     * Determine whether the user can delete the kebun.
     */
    public function delete(User $user, LokasiMonitoring $lokasimonitoring)
    {
        return $user->id === $lokasimonitoring->user_id;
    }
}