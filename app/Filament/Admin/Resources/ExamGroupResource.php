<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ExamGroupType;
use App\Filament\Admin\Resources\ExamGroupResource\Pages\CreateExamGroup;
use App\Filament\Admin\Resources\ExamGroupResource\Pages\EditExamGroup;
use App\Filament\Admin\Resources\ExamGroupResource\Pages\ListExamGroups;
use App\Models\AcademicYear;
use App\Models\ExamGroup;
use BackedEnum;
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

class ExamGroupResource extends Resource
{
    protected static ?string $model = ExamGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Examination';

    protected static ?string $navigationLabel = 'Exam Groups';

    protected static ?int $navigationSort = 2;

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
                        ->label('Name (e.g. FA1, SA2)')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(collect(ExamGroupType::cases())->mapWithKeys(
                            fn (ExamGroupType $t): array => [$t->value => $t->label()]
                        ))
                        ->nullable(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('college_class_id')
                        ->label('Class')
                        ->relationship('collegeClass', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('academic_year_id')
                        ->label('Academic Year')
                        ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all())
                        ->searchable()
                        ->nullable(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('conducted_date')
                        ->label('Conducted Date')
                        ->nullable(),

                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->default(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?ExamGroupType $state): string => $state?->shortLabel() ?? '—')
                    ->color(fn (?ExamGroupType $state): string => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('conducted_date')
                    ->label('Conducted')
                    ->date()
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_published')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Draft')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('exams_count')
                    ->label('Exams')
                    ->counts('exams')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['collegeClass', 'academicYear']))
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name'),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(collect(ExamGroupType::cases())->mapWithKeys(
                        fn (ExamGroupType $t): array => [$t->value => $t->shortLabel()]
                    )),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn (): array => AcademicYear::orderByDesc('start_year')->pluck('name', 'id')->all()),
            ])
            ->actions([
                \Filament\Actions\Action::make('publish')
                    ->label('Publish Results')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Publish Exam Results')
                    ->modalDescription('This will make results visible to students and parents. This cannot be easily undone.')
                    ->hidden(fn (ExamGroup $record): bool => $record->is_published)
                    ->action(function (ExamGroup $record): void {
                        $record->update(['is_published' => true]);

                        Notification::make()
                            ->title("Results for {$record->name} published successfully.")
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
            'index' => ListExamGroups::route('/'),
            'create' => CreateExamGroup::route('/create'),
            'edit' => EditExamGroup::route('/{record}/edit'),
        ];
    }
}
