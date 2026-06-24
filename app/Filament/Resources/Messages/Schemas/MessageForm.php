<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('conversation_id')
                    ->relationship('conversation', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "ID: {$record->id} - ".($record->subject ?: 'No Subject')." ({$record->type})")
                    ->required(),
                Select::make('sender_id')
                    ->relationship('sender', 'name')
                    ->placeholder('System/External Contact'),
                TextInput::make('sender_name')
                    ->required(),
                TextInput::make('sender_email')
                    ->email()
                    ->required(),
                Textarea::make('body')
                    ->required(),
                Select::make('type')
                    ->options([
                        'internal' => 'Internal Chat',
                        'outbound_email' => 'Outbound Email',
                        'inbound_email' => 'Inbound Email',
                    ])
                    ->default('internal')
                    ->required(),
                Select::make('status')
                    ->options([
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                    ])
                    ->default('delivered')
                    ->required(),
                TextInput::make('message_id_header')
                    ->label('Message ID Header'),
            ]);
    }
}
