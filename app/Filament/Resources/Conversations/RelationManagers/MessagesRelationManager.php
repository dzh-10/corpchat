<?php

namespace App\Filament\Resources\Conversations\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sender_id')
                    ->relationship('sender', 'name')
                    ->placeholder('System/External'),
                TextInput::make('sender_email')
                    ->required(),
                TextInput::make('sender_name')
                    ->required(),
                Textarea::make('body')
                    ->required(),
                Select::make('type')
                    ->options([
                        'internal' => 'Internal',
                        'outbound_email' => 'Outbound Email',
                        'inbound_email' => 'Inbound Email',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('sender_name')
                    ->searchable(),
                TextColumn::make('sender_email')
                    ->searchable(),
                TextColumn::make('body')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
