<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserRequest;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserRequestResource\Pages;
use App\Filament\Resources\UserRequestResource\RelationManagers;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

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
        // Both admin and warga can create requests
        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('status', 'onprocess')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'laporanType'])->select('id', 'user_id', 'type', 'status', 'description');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'user.name',
            'laporanType.name', 
            'type',
            'status',
            'description',
        ];
    }

    public static function form(Form $form): Form
    {
        $isAdmin = Auth::user()->role === 'admin';
        
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Permintaan KTP Baru'),
                Forms\Components\Select::make('user_id')
                    ->label('Pilih Pengguna')
                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', 'warga'))
                    ->required()
                    ->prefixIcon('heroicon-o-user')
                    ->searchable()
                    ->preload(),
                
                Forms\Components\Select::make('laporan_type_id')
                    ->label('Jenis Laporan')
                    ->relationship('laporanType', 'name')
                    ->required()
                    ->prefixIcon('heroicon-o-document-text')
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'permintaan' => 'Permintaan',
                        'pelaporan' => 'Pelaporan',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-o-clipboard-document-list')
                    ->native(false),
                    
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('laporanType.name')
                    ->label('Jenis Laporan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->color(fn(string $state): string => match ($state) {
                        'permintaan' => 'info',
                        'pelaporan' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucwords($state)),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->html()
                    ->limit(100),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'onprocess' => 'heroicon-o-clock',
                        'accepted' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    ActivityLogTimelineTableAction::make('Aktivitas')
                ])->label('Aksi')
                    ->button()
                    ->color('gray')
                    ->outlined()
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
            // 'create' => Pages\CreateUserRequest::route('/create'),
            'view' => Pages\ViewUserRequest::route('/{record}'),
            // 'edit' => Pages\EditUserRequest::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'onprocess';
        $data['return_message'] = null;
        
        return $data;
    }
}
