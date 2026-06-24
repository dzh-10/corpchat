<?php

namespace App\Filament\Resources\Conversations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConversationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                Select::make('type')
                    ->options(['internal_chat' => 'Internal chat', 'external_email' => 'External email'])
                    ->default('internal_chat')
                    ->required(),
                TextInput::make('external_contact_email')
                    ->email(),
                TextInput::make('external_contact_name'),
                TextInput::make('subject'),
            ]);
    }
}
