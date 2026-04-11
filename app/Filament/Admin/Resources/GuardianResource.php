<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GuardianResource\Pages\CreateGuardian;
use App\Filament\Admin\Resources\GuardianResource\Pages\EditGuardian;
use App\Filament\Admin\Resources\GuardianResource\Pages\ListGuardians;
use App\Models\Guardian;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Students';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'roll_number')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? $record->roll_number)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Student'),

                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('last_name')
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\Select::make('relation')
                    ->required()
                    ->options([
                        'Father' => 'Father',
                        'Mother' => 'Mother',
                        'Brother' => 'Brother',
                        'Sister' => 'Sister',
                        'Guardian' => 'Guardian',
                        'Other' => 'Other',
                    ]),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\Textarea::make('address')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_primary_contact')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Full Name')
                    ->formatStateUsing(fn ($state, $record) => $record->fullName())
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('relation')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_primary_contact')
                    ->label('Primary')
                    ->badge()
                    ->color(fn ($state): string => (bool) $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state): string => (bool) $state ? 'Primary' : 'No'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuardians::route('/'),
            'create' => CreateGuardian::route('/create'),
            'edit' => EditGuardian::route('/{record}/edit'),
        ];
    }
}
