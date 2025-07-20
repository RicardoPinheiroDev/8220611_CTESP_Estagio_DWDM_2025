<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Enums\UserType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (isset($data['promote_user_id']) && $data['promote_user_id']) {
            $user = \App\Models\User::find($data['promote_user_id']);
            if ($user) {
                $user->update([
                    'type' => UserType::EMPLOYEE,
                    'status' => true,
                ]);
                return $user;
            }
        }

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => UserType::EMPLOYEE,
            'status' => $data['status'] ?? true,
        ]);

        return $user;
    }
}
