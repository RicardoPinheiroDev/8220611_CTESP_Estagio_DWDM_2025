<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Enums\UserType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (isset($data['promote_user_id']) && $data['promote_user_id']) {
            $user = \App\Models\User::find($data['promote_user_id']);
            if ($user) {
                // Create team member from existing user
                $team = \App\Models\Team::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'type' => $data['type'] ?? UserType::EMPLOYEE,
                    'status' => $data['status'] ?? true,
                    'department_id' => $data['department_id'],
                ]);

                // Also create corresponding user if not exists
                \App\Models\User::firstOrCreate(
                    ['email' => $user->email],
                    [
                        'name' => $user->name,
                        'password' => $user->password,
                        'type' => $data['type'] ?? UserType::EMPLOYEE,
                        'status' => $data['status'] ?? true,
                    ]
                );

                return $team;
            }
        }

        $hashedPassword = Hash::make($data['password']);
        
        // Create new team member
        $team = \App\Models\Team::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'type' => $data['type'] ?? UserType::EMPLOYEE,
            'status' => $data['status'] ?? true,
            'department_id' => $data['department_id'],
        ]);

        // Also create corresponding user (always create, update if exists)
        \App\Models\User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => $hashedPassword,
                'type' => $data['type'] ?? UserType::EMPLOYEE,
                'status' => $data['status'] ?? true,
            ]
        );

        return $team;
    }
}
