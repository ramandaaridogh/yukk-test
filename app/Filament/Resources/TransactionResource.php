<?php

namespace App\Filament\Resources;

use App\Enums\Type;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Ramsey\Uuid\Uuid;

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
		$date = str_replace('-', '', date('Y-m-d')) . fake()->numberBetween(1000000, 9999999);
		return $form
			->schema([
				Select::make('transaction_type_id')
					->label('Transaction Type')
					->relationship('transaction_type', 'name')
					->native(false)
					->live(debounce: 500)
					->afterStateUpdated(function (Set $set, ?int $state, Select $component) use ($date) {
						$set('code', TransactionType::find($state)->code . $date);
						// $set('note', TransactionType::find($state)->type);
						// $set('type', TransactionType::find($state)->has_image);
					})
					->required(),
				Forms\Components\Select::make('user_id')
					->label('User')
					->relationship('user', 'name')
					->searchable()
					->getSearchResultsUsing(fn (string $search): array => User::where(DB::raw('LOWER(name)'), 'like', "%" . $search . "%")->orWhere(DB::raw('LOWER(username)'), 'like', "%" . $search . "%")->limit(50)->pluck('name', 'id')->toArray())
					->helperText(str('Search with **username** or **name**')->inlineMarkdown()->toHtmlString())
					->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
					->native(false)
					->required(),
				Forms\Components\TextInput::make('code')
					->required()
					->live(onBlur: true)
					->maxLength(255)
					->readOnly(),
				// Hidden::make('type')
				//     ->live(onBlur: true),
				Forms\Components\TextInput::make('ammount')
					->required()
					->numeric(),
				Forms\Components\Textarea::make('note')
					->live(onBlur: true)
					->required()
					->columnSpanFull(),
				FileUpload::make('image')
					->image()
					->disk('filament')
					->directory('')
					->maxSize(1024)
					->getUploadedFileNameForStorageUsing(
						fn (TemporaryUploadedFile $file): string
						=> (string) str("image-" . $date . '-' . time() . Uuid::uuid4(). '.' . $file->getClientOriginalExtension())->prepend('transaction-'),
					)
					->columnSpanFull()
					->live(debounce: 500)
					->visible(fn (Get $get) => match (TransactionType::find($get('transaction_type_id'))?->type->value) {
						'in' => true,
						'out' => false,
						default => false
					})
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
					->sortable(),
				Tables\Columns\TextColumn::make('note')
					->label('Note')
					->searchable(query: function (Builder $query, string $search): Builder {
						return $query->where(DB::Raw('LOWER(note)'), 'LIKE', "%".strtolower($search)."%");
					})
                    ->size(TextColumn\TextColumnSize::ExtraSmall)
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
					// ->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('ammount')
                ->numeric(
                    decimalPlaces: 0,
                    decimalSeparator: ',',
                    thousandsSeparator: '.',
                )
                ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 2, ', ', '.'))
                ->alignRight()
                ->color(fn (Transaction $record): string => match ((string) $record->transaction_type->type->value) {
                    'in' => 'success',
                    'out' => 'danger',
                })
                ->sortable(),
                // ->summarize(Sum::make()->label('Total Ammount Balance')->numeric(
                // 	decimalPlaces: 2,
                // 	decimalSeparator: ',',
                // 	thousandsSeparator: '.',
                // )),
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
				SelectFilter::make('transaction_type_id')
				->relationship('transaction_type', 'name')
				->label('Type')
				->native(false),
				SelectFilter::make('user')
				->label('Users')
				->relationship('user', 'name')
				->searchable()
				->getSearchResultsUsing(fn (string $search): array => User::where(DB::raw('LOWER(name)'), 'like', "%" . $search . "%")->orWhere(DB::raw('LOWER(username)'), 'like', "%" . $search . "%")->limit(50)->pluck('name', 'id')->toArray())
				->native(false),
			])
			->actions([
				// Tables\Actions\EditAction::make(),
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
