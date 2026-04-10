<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CollegeClassResource\Pages\CreateCollegeClass;
use App\Filament\Admin\Resources\CollegeClassResource\Pages\EditCollegeClass;
use App\Filament\Admin\Resources\CollegeClassResource\Pages\ListCollegeClasses;
use App\Models\CollegeClass;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CollegeClassResource extends Resource
{
    protected static ?string $model = \App\Models\CollegeClass::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('section')
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\TextInput::make('semester')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12),

                Forms\Components\TextInput::make('academic_year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->helperText('Start year (e.g. 2026).'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('section')
                    ->searchable(),

                Tables\Columns\TextColumn::make('semester')
                    ->searchable(),

                Tables\Columns\TextColumn::make('academic_year')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => ListCollegeClasses::route('/'),
            'create' => CreateCollegeClass::route('/create'),
            'edit' => EditCollegeClass::route('/{record}/edit'),
        ];
    }
}
