<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Nome')->required()->maxLength(100),
            TextInput::make('email')->label('E-mail de login')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('password')->label('Senha')->password()->revealable()
                ->rule(Password::min(8)->letters()->numbers())
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->helperText(fn (string $operation) => $operation === 'edit' ? 'Deixe em branco para manter a senha atual.' : 'Mínimo 8 caracteres, com letras e números.'),
        ]);
    }
}
