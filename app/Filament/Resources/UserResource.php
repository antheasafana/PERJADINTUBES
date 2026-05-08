<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;

use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\Facades\Hash;

// FORM
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

// TABLE
use Filament\Tables\Columns\BadgeColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon =
        'heroicon-o-user-group';

    // GROUP MENU
    protected static ?string $navigationGroup =
        'Master Data';

    /**
     * FORM
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ======================
                // NAMA
                // ======================
                TextInput::make('name')

                    ->label('Nama')

                    ->required()

                    ->maxLength(100),

                // ======================
                // EMAIL
                // ======================
                TextInput::make('email')

                    ->email()

                    ->required()

                    ->maxLength(100),

                // ======================
                // PASSWORD
                // ======================
                TextInput::make('password')
                        ->password()
                        // ->required(fn (Forms\Form $form): bool => $form->getLivewire() instanceof Pages\CreateUser)
                        // Mengeset nilai default
                        ->default('password123') 
                        // Mencegah user mengubah input ini
                        ->disabled()
                        ->same('password_confirmation')
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->label('Password Confirmation')
                        // ->required(fn (Forms\Form $form): bool => $form->getLivewire() instanceof Pages\CreateUser) // ✅ Perbaikan
                        ->default('password123')
                        ->disabled()
                        ->dehydrated(false),
                Select::make('user_group')

                    ->label('Role')

                    ->required()

                    ->options([

                        'admin' => 'Admin',

                        'pegawai' => 'Pegawai',

                    ])

                    ->default('pegawai'),

            ]);
    }

    /**
     * TABLE
     */
    public static function table(
        Table $table
    ): Table
    {
        return $table
            ->columns([

                // NAMA
                Tables\Columns\TextColumn::make('name')

                    ->searchable(),

                // EMAIL
                Tables\Columns\TextColumn::make('email')

                    ->searchable(),

                // ROLE
                BadgeColumn::make('user_group')

                    ->label('Role')

                    ->colors([

                        'danger' => 'admin',

                        'success' => 'pegawai',

                    ]),

                // CREATED
                Tables\Columns\TextColumn::make('created_at')

                    ->dateTime('d M Y H:i'),
            ])

            ->filters([
                //
            ])

            ->actions([

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }

    /**
     * RELATIONS
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * PAGES
     */
    public static function getPages(): array
    {
        return [

            'index' =>
                Pages\ListUsers::route('/'),

            'create' =>
                Pages\CreateUser::route('/create'),

            'edit' =>
                Pages\EditUser::route('/{record}/edit'),

        ];
    }
}