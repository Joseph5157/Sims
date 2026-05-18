<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AcademicYearResource\Pages\CreateAcademicYear;
use App\Filament\Admin\Resources\AcademicYearResource\Pages\EditAcademicYear;
use App\Filament\Admin\Resources\AcademicYearResource\Pages\ListAcademicYears;
use App\Models\AcademicYear;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Academic Years';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->placeholder('e.g. 2025-2026')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('start_year')
                    ->label('Start Year')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(2100),

                Forms\Components\TextInput::make('end_year')
                    ->label('End Year')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(2100),

                Forms\Components\Toggle::make('is_current')
                    ->label('Active')
                    ->helperText('Only one academic year can be active at a time.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_year')
                    ->label('Start Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_year')
                    ->label('End Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_current')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->defaultSort('start_year', 'desc')
            ->filters([])
            ->actions([
                Action::make('setActive')
                    ->label('Set Active')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Set as Active Year')
                    ->modalDescription('This will deactivate all other academic years.')
                    ->hidden(fn (AcademicYear $record): bool => $record->is_current)
                    ->action(function (AcademicYear $record): void {
                        AcademicYear::query()->update(['is_current' => false]);
                        $record->update(['is_current' => true]);

                        Notification::make()
                            ->title("{$record->name} is now the active academic year.")
                            ->success()
                            ->send();
                    }),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
