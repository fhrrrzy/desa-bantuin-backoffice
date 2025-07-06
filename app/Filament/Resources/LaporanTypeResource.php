<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LaporanType;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\LaporanTypeResource\Pages;
use Filament\Tables\Actions\ActionGroup;

class LaporanTypeResource extends Resource
{
    protected static ?string $model = LaporanType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Jenis Laporan';
    protected static ?string $modelLabel = 'Jenis Laporan';
    protected static ?string $pluralModelLabel = 'Jenis Laporan';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Jenis Laporan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: KTP, KK, Buku Nikah')
                    ->prefixIcon('heroicon-o-document-text'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Jenis Laporan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('requests_count')
                    ->label('Jumlah Permintaan')
                    ->counts('requests')
                    ->badge()
                    ->color('info'),

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
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ])
                ->label('Aksi')
                ->icon('heroicon-o-cog-6-tooth')
                ->tooltip('Aksi')
                ->button()
                ->color('gray')
                ->outlined(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Belum ada jenis laporan')
            ->emptyStateDescription('Jenis laporan akan ditambahkan oleh sistem.')
            ->emptyStateActions([
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
            'index' => Pages\ListLaporanTypes::route('/'),
            // No create or edit pages allowed - read-only
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->select('id', 'name');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
