<?php

namespace App\Filament\Resources;

use App\Enums\Type;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->with([
			'transaction_type',
			'user',
        ])
        ->orderByDesc('id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transaction_type_id')
                    ->label('Transaction Type')
                    ->relationship('transaction_type', 'name')
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => User::where(DB::raw('LOWER(name)'), 'like', "%" . $search . "%")->orWhere(DB::raw('LOWER(username)'), 'like', "%" . $search . "%")->limit(50)->pluck('name', 'id')->toArray())
                    ->helperText(str('Search with **username** or **name**')->inlineMarkdown()->toHtmlString())
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ammount')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('image')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')
                    ->label('User'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    // ->description(fn (Transaction $record): string => "Transaction Date : " . (string) Carbon::parse($record->created_at)->format('Y F d'))
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->copyMessage('Transaction Code copied')
                    ->copyMessageDuration(1500)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(DB::Raw('LOWER(code)'), 'LIKE', "%".strtolower($search)."%");
                    }),
                Tables\Columns\TextColumn::make('transaction_type.name')
                    ->description(fn (Transaction $record): string => "Transaction " . (string) $record->transaction_type->type->value)
                    ->color(fn (Transaction $record): string => match ((string) $record->transaction_type->type->value) {
                        'in' => 'success',
                        'out' => 'danger',
                    })
                    ->icon(fn (Transaction $record): string => match ((string) $record->transaction_type->type->value) {
                        'in' => 'heroicon-m-arrow-trending-up',
                        'out' => 'heroicon-m-arrow-trending-down',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    // ->formatStateUsing(fn (Transaction $state) => $state->user->username)
                    ->sortable(),
                Tables\Columns\TextColumn::make('ammount')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 2, ', ', '.'))
                    // ->money('IDR')
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(DB::Raw('LOWER(note)'), 'LIKE', "%".strtolower($search)."%");
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Transaction Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter::make('service_id_true')->query(fn (Builder $query): Builder => $query->where('service_id', '!=', null))->label('User with Services'),
				SelectFilter::make('transaction_type_id')
                ->relationship('transaction_type', 'name')
                // ->query(fn (Builder $query): Builder => $query->whereRelation('transaction_type', 'type', 'in'))
				->label('Type')
				// ->options([
				// 	1 => 'PENDING',
				// 	2 => 'PROGRESS',
				// 	3 => 'DONE',
				// 	4 => 'FAILED',
				// ])
                ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
