<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GuardianResource\Pages\CreateGuardian;
use App\Filament\Admin\Resources\GuardianResource\Pages\EditGuardian;
use App\Filament\Admin\Resources\GuardianResource\Pages\ListGuardians;
use App\Models\Guardian;
use App\Models\User;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            ->modifyQueryUsing(fn ($query) => $query->with(['student.user']))
            ->filters([
                //
            ])
            ->actions([
                Action::make('create_login')
                    ->label('Create Login')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->visible(fn (Guardian $record): bool => $record->user_id === null)
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Login Email')
                            ->email()
                            ->required()
                            ->default(fn (Guardian $record): ?string => $record->email)
                            ->unique('users', 'email')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->default(fn (): string => Str::random(10))
                            ->helperText('Share this password with the parent.'),
                    ])
                    ->action(function (Guardian $record, array $data): void {
                        $user = User::create([
                            'name'              => $record->fullName(),
                            'email'             => $data['email'],
                            'password'          => Hash::make($data['password']),
                            'email_verified_at' => now(),
                        ]);

                        $user->assignRole('parent');
                        $record->update(['user_id' => $user->id]);

                        Notification::make()
                            ->title('Login created')
                            ->body("Parent account created: {$data['email']}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Create Parent Login')
                    ->modalSubmitActionLabel('Create Login'),

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
