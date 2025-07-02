<?php

namespace App\Filament\Widgets;

use App\Models\UserRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class RecentOnProcessRequestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Permintaan Terbaru (Sedang Diproses)';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserRequest::query()
                    ->where('status', 'onprocess')
                    ->with(['user', 'laporanType'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

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

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lampiran')
                    ->label('Lampiran')
                    ->formatStateUsing(fn($state) => $state ? count($state) . ' file' : 'Tidak ada')
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn(UserRequest $record): string => route('filament.admin.resources.user-requests.view', $record)),

                Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('return_message')
                            ->label('Pesan Balasan')
                            ->placeholder('Masukkan pesan balasan (opsional)')
                            ->nullable()
                            ->rows(3),
                    ])
                    ->action(function (UserRequest $record, array $data): void {
                        $record->update([
                            'status' => 'accepted',
                            'return_message' => $data['return_message'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Permintaan berhasil diselesaikan.')
                            ->success()
                            ->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('return_message')
                            ->label('Pesan Balasan')
                            ->placeholder('Masukkan alasan penolakan (opsional)')
                            ->nullable()
                            ->rows(3),
                    ])
                    ->action(function (UserRequest $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'return_message' => $data['return_message'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Permintaan berhasil ditolak.')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
