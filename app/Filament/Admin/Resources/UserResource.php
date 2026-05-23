<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
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
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    protected static ?string $navigationLabel = 'Users & Roles';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Account Details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->unique('users', 'email', ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Leave blank to keep the current password.'
                                : '')
                            ->minLength(8)
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Role Assignment')
                    ->icon('heroicon-o-shield-check')
                    ->description('Controls which panel this user can log in to.')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(fn (): array => Role::orderBy('name')->pluck('name', 'name')->all())
                            ->required()
                            ->searchable()
                            ->helperText('admin / faculty → Admin panel | faculty → Faculty panel | student → Student panel | parent → Parent panel'),
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

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'   => 'danger',
                        'faculty' => 'warning',
                        'student' => 'info',
                        'parent'  => 'success',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('profile_type')
                    ->label('Profile')
                    ->getStateUsing(function (User $record): string {
                        if ($record->studentProfile) {
                            return 'Student';
                        }
                        if ($record->facultyProfile) {
                            return 'Faculty';
                        }
                        if ($record->guardianProfile) {
                            return 'Parent';
                        }

                        return 'Admin Only';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Student'    => 'info',
                        'Faculty'    => 'warning',
                        'Parent'     => 'success',
                        'Admin Only' => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(fn (): array => Role::orderBy('name')->pluck('name', 'name')->all())
                    ->query(fn ($query, array $data) => filled($data['value'])
                        ? $query->role($data['value'])
                        : $query),
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
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
