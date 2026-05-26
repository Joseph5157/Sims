<?php

namespace App\Filament\Admin\Resources;

use App\Enums\GradingLevelType;
use App\Filament\Admin\Resources\GradingLevelResource\Pages\CreateGradingLevel;
use App\Filament\Admin\Resources\GradingLevelResource\Pages\EditGradingLevel;
use App\Filament\Admin\Resources\GradingLevelResource\Pages\ListGradingLevels;
use App\Models\AcademicYear;
use App\Models\GradingLevel;
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

class GradingLevelResource extends Resource
{
    protected static ?string $model = GradingLevel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Examination';

    protected static ?string $navigationLabel = 'Grading Levels';

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
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Label (e.g. A1, B2)')
                        ->required()
                        ->maxLength(10),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(collect(GradingLevelType::cases())->mapWithKeys(
                            fn (GradingLevelType $t): array => [$t->value => $t->label()]
                        ))
                        ->required()
                        ->default(GradingLevelType::Scholastic->value),
                ]),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('min_score')
                        ->label('Min %')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->maxValue(100),

                    Forms\Components\TextInput::make('max_score')
                        ->label('Max %')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->maxValue(100),

                    Forms\Components\TextInput::make('grade_point')
                        ->label('Grade Point')
                        ->numeric()
                        ->nullable()
                        ->minValue(0)
                        ->maxValue(10),
                ]),

                Forms\Components\Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all())
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Label')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('min_score')
                    ->label('Min %')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_score')
                    ->label('Max %')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade_point')
                    ->label('Grade Point')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?GradingLevelType $state): string => $state?->label() ?? '—')
                    ->color(fn (?GradingLevelType $state): string => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->placeholder('Global')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['academicYear']))
            ->defaultSort('min_score', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(GradingLevelType::cases())->mapWithKeys(
                        fn (GradingLevelType $t): array => [$t->value => $t->label()]
                    )),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all()),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGradingLevels::route('/'),
            'create' => CreateGradingLevel::route('/create'),
            'edit' => EditGradingLevel::route('/{record}/edit'),
        ];
    }
}
