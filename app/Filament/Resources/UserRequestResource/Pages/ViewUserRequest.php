<?php

namespace App\Filament\Resources\UserRequestResource\Pages;

use App\Filament\Resources\UserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewUserRequest extends ViewRecord
{
    protected static string $resource = UserRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Deskripsi Permintaan')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->html()
                                    ->columnSpanFull(),

                                TextEntry::make('lampiran')
                                    ->label('File Lampiran')
                                    ->view('filament.infolists.components.attachments-view')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),


                        Grid::make(1)
                            ->schema([
                                Section::make('Informasi Pengguna')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Pengguna')
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('user.email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope'),
                                        TextEntry::make('user.phone_number')
                                            ->label('Nomor Telepon')
                                            ->icon('heroicon-o-phone'),
                                    ]),

                                Section::make('Detail Permintaan')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        TextEntry::make('laporanType.name')
                                            ->label('Jenis Laporan')
                                            ->icon('heroicon-o-document'),
                                        TextEntry::make('type')
                                            ->label('Tipe')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'permintaan' => 'info',
                                                'pelaporan' => 'success',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn($state) => ucwords($state)),
                                        TextEntry::make('status')
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
                                        TextEntry::make('created_at')
                                            ->label('Dibuat Pada')
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                    ]),



                                Section::make('Pesan Balasan')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->schema([
                                        TextEntry::make('return_message')
                                            ->label('Pesan Balasan')
                                            ->html()
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn($record) => !empty($record->return_message)),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Only show Terima and Tolak actions if status is 'onprocess'
        if ($this->record->status === 'onprocess') {
            $actions[] = Actions\Action::make('selesai')
                ->label('Selesai')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->outlined()
                ->form([
                    Forms\Components\Textarea::make('return_message')
                        ->label('Pesan Balasan')
                        ->placeholder('Masukkan pesan balasan (opsional)')
                        ->nullable()
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'accepted',
                        'return_message' => $data['return_message'] ?? null,
                    ]);

                    $this->notify('success', 'Permintaan berhasil diselesaikan.');
                });

            $actions[] = Actions\Action::make('tolak')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->outlined()
                ->form([
                    Forms\Components\Textarea::make('return_message')
                        ->label('Pesan Balasan')
                        ->placeholder('Masukkan alasan penolakan (opsional)')
                        ->nullable()
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'return_message' => $data['return_message'] ?? null,
                    ]);

                    $this->notify('success', 'Permintaan berhasil ditolak.');
                });
        }

        return $actions;
    }
}
