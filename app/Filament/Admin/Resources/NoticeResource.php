<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NoticeResource\Pages\CreateNotice;
use App\Filament\Admin\Resources\NoticeResource\Pages\EditNotice;
use App\Filament\Admin\Resources\NoticeResource\Pages\ListNotices;
use App\Models\Notice;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->optional()
                    ->searchable()
                    ->preload()
                    ->placeholder('All Departments'),

                Forms\Components\DatePicker::make('expires_at')
                    ->optional(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->default('All Departments'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'Expired' : $state),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),
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
            'index' => ListNotices::route('/'),
            'create' => CreateNotice::route('/create'),
            'edit' => EditNotice::route('/{record}/edit'),
        ];
    }
}
