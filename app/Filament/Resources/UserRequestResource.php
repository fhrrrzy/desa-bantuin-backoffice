<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRequestResource\Pages;
use App\Filament\Resources\UserRequestResource\RelationManagers;
use App\Models\User;
use App\Models\UserRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserRequestResource extends Resource
{
    protected static ?string $model = UserRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Permintaan & Permohonan';
    protected static ?string $modelLabel = 'Permintaan';
    protected static ?string $pluralModelLabel = 'Permintaan & Permohonan';
    protected static ?string $navigationGroup = 'Layanan Desa';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        // Only warga can create requests, admin cannot
        return Auth::user()->role === 'warga';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('status', 'onprocess')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->prefixIcon('heroicon-o-user'),
                Forms\Components\Select::make('laporan_type_id')
                    ->relationship('laporanType', 'name')
                    ->required()
                    ->prefixIcon('heroicon-o-document-text'),
                Forms\Components\Select::make('type')
                    ->options([
                        'permintaan' => 'Permintaan',
                        'pelaporan' => 'Pelaporan',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-clipboard-document-list'),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'blockquote',
                        'codeBlock',
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('public'),
                Forms\Components\Select::make('status')
                    ->options([
                        'onprocess' => 'Sedang Diproses',
                        'accepted' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-clock'),
                Forms\Components\Textarea::make('return_message')
                    ->label('Pesan Balasan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('lampiran')
                    ->label('Lampiran')
                    ->multiple()
                    ->directory('lampiran')
                    ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxFiles(5)
                    ->maxSize(5120) // 5MB per file
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('laporanType.name')
                    ->label('Jenis Laporan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'permintaan' => 'info',
                        'pelaporan' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucwords($state)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'onprocess' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'onprocess' => 'Sedang Diproses',
                        'accepted' => 'Selesai',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('lampiran')
                    ->label('Lampiran')
                    ->formatStateUsing(fn($state) => $state ? count($state) . ' file' : 'Tidak ada')
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('laporan_type_id')
                    ->label('Jenis Laporan')
                    ->relationship('laporanType', 'name')
                    ->native(false)
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'permintaan' => 'Permintaan',
                        'pelaporan' => 'Pelaporan',
                    ])
                    ->native(false)
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Belum ada permintaan')
            ->emptyStateDescription('Belum ada permintaan atau pelaporan yang dibuat. Mulai dengan membuat permintaan baru.')
            ->emptyStateActions(
                Auth::user()->role === 'warga' ? [
                    Tables\Actions\Action::make('create')
                        ->label('Buat Permintaan')
                        ->url(route('filament.admin.resources.user-requests.create'))
                        ->icon('heroicon-m-plus')
                        ->button(),
                ] : []
            );
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
            'index' => Pages\ListUserRequests::route('/'),
            'create' => Pages\CreateUserRequest::route('/create'),
            'view' => Pages\ViewUserRequest::route('/{record}'),
            'edit' => Pages\EditUserRequest::route('/{record}/edit'),
        ];
    }
}
