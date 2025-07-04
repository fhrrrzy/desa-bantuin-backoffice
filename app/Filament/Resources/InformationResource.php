<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformationResource\Pages;
use App\Filament\Resources\InformationResource\RelationManagers;
use App\Models\Information;
use App\Models\LaporanType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class InformationResource extends Resource
{
    protected static ?string $model = Information::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Informasi';

    protected static ?string $modelLabel = 'Informasi';

    protected static ?string $pluralModelLabel = 'Informasi';

    protected static ?string $navigationGroup = 'Layanan Desa';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laporan_type_id')
                    ->label('Jenis Laporan')
                    ->options(LaporanType::all()->pluck('name', 'id'))
                    ->required()
                    ->prefixIcon('heroicon-o-document-text')
                    ->searchable(),

                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->prefixIcon('heroicon-o-document-text')
                    ->maxLength(255),

                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('attachment')
                    ->label('Lampiran')
                    ->multiple()
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                    ->maxFiles(5)
                    ->maxSize(10240) // 10MB
                    ->directory('information-attachments')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('laporanType.name')
                    ->label('Jenis Laporan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->html()
                    ->limit(100),

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
                    ->label('Filter berdasarkan Jenis Laporan')
                    ->native(false)
                    ->searchable()
                    ->options(LaporanType::all()->pluck('name', 'id')),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->tooltip('Aksi')
                    ->button()
                    ->color('gray')
                    ->outlined(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInformation::route('/'),
            // 'create' => Pages\CreateInformation::route('/create'),
            // 'edit' => Pages\EditInformation::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->title} - {$record->laporanType->name}";
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('laporanType')->select('id', 'title', 'laporan_type_id');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'description',
            'laporanType.name',
        ];
    }
}
