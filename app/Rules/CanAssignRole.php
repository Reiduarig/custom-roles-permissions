<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class CanAssignRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ya que un usuario puede tener múltiples roles, buscamos el rol con el nivel más alto
        $userHighestRole = $user->roles->sortByDesc('nivel')->first();

        $targetRole = Role::where('slug', $value)->first();

        if (! $targetRole || ! $userHighestRole) {
            $fail('User role or target role not found.');
        }

        if (! $userHighestRole->isHigherThan($targetRole)) {
            $fail("You do not have permission to assign the {$targetRole->name} role.");
        }
    }
}
