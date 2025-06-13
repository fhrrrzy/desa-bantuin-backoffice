<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Filament\Tables\Actions\ActionGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 2;

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->name} - ({$record->role})";
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->select('id', 'name', 'email', 'role');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'role'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-user')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-envelope')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('phone_number')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->prefixIcon('heroicon-o-phone')
                    ->columnSpan(1),

                Forms\Components\Select::make('role')
                    ->label('Peran')
                    ->prefixIcon('heroicon-o-hand-raised')
                    ->options([
                        'admin' => 'Admin',
                        'warga' => 'Warga',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->reactive()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required()
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->prefixIcon('heroicon-o-lock-closed')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required()
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->prefixIcon('heroicon-o-lock-closed')
                    ->columnSpan(1),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->deferLoading()
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Nomor Telepon')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',
                        'warga' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Admin',
                        'warga' => 'Warga',
                    ])
                    ->native(false)
                    ->searchable(),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\EditAction::make()
                            ->modalWidth('md')
                            ->slideOver(),

                        Tables\Actions\DeleteAction::make(),
                    ])->dropdown(false),
                    Tables\Actions\Action::make('changePassword')
                        ->label('Ubah Kata Sandi')
                        ->icon('heroicon-o-key')
                        ->modalWidth('md')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->label('Kata Sandi Baru')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(8),

                            Forms\Components\TextInput::make('password_confirmation')
                                ->label('Konfirmasi Kata Sandi')
                                ->password()
                                ->revealable()
                                ->required()
                                ->same('password'),
                        ])
                        ->modalHeading('Ubah Kata Sandi')
                        ->modalDescription('Masukkan kata sandi baru dan konfirmasikan untuk mengubah kata sandi pengguna.')
                        ->action(function ($record, $data) {
                            $admin = Auth::user();

                            $record->update([
                                'password' => bcrypt($data['password']),
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Kata Sandi Berhasil Diubah')
                                ->body("Kata sandi Anda telah diubah oleh admin: {$admin->name}. Jika anda tidak meminta ini, harap hubungi Admin event.")
                                ->sendToDatabase($record);

                            Notification::make()
                                ->success()
                                ->title('Kata sandi berhasil diubah')
                                ->body("Kata sandi untuk pengguna {$record->name} telah diubah oleh admin: {$admin->name}.")
                                ->send();
                        }),
                ])
                    ->label('Aksi')
                    ->button()
                    ->color('gray')
                    ->outlined()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
