<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Enums\Type;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Scopes\IsActiveScope;
use App\Models\Transaction;
use App\Models\User;
use Faker\Core\Uuid;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserResource extends Resource
{
	protected static ?string $model = User::class;

	protected static ?string $navigationIcon = 'heroicon-o-users';

	protected static ?string $navigationGroup = 'Auth';

	protected static ?int $navigationSort = 0;

	public static function getEloquentQuery(): Builder
	{
		return parent::getEloquentQuery()
		->withoutGlobalScopes([IsActiveScope::class])
		->with([
			'transactions',
		])
		->orderByDesc('id');
	}

	public static function form(Form $form): Form
	{
		$date = str_replace('-', '', date('Y-m-d')) . fake()->unique()->numberBetween(1000000, 9999999);
		return $form
			->schema([
				Forms\Components\TextInput::make('username')
					->required()
					->unique(ignorable: fn ($record) => $record)
					->maxLength(255),
				Forms\Components\TextInput::make('email')
					->unique(ignorable: fn ($record) => $record)
					->email()
					->required()
					->maxLength(255),
				Forms\Components\TextInput::make('name')
					->required()
					->maxLength(255),
				Forms\Components\DatePicker::make('birth_date')
					->required(),
				Forms\Components\TextInput::make('password')
					->password()
					->required()
					->rule(Password::default())
					->dehydrateStateUsing(fn ($state) => Hash::make($state))
					->same('passwordConfirmation')
					->minLength(8)
					->visible(fn (string $context): bool => $context === 'create'),
				Forms\Components\TextInput::make('passwordConfirmation')
					->label('Password Confirmation')
					->password()
					->required()
					->dehydrated(false)
					->visible(fn (string $context): bool => $context === 'create'),
				Hidden::make('pin')
					->default(''),
				Hidden::make('ammount_balance')
					->default(0),
				Hidden::make('is_active')
					->default(true),
				Hidden::make('email_verified_at')
					->default(now()),
				Forms\Components\TextInput::make('phone_number')
					->unique(ignorable: fn ($record) => $record)
					->tel()
					->required()
					->maxLength(16),
				Select::make('gender')
					->required()
					->enum(Gender::class)
					->options([
						'M' => 'Male',
						'F' => 'Female',
					])
					->native(false),
				Forms\Components\Textarea::make('address')
					->required()
					->columnSpanFull(),
				FileUpload::make('image')
					->image()
					->disk('filament')
					->directory('')
					->maxSize(1024)
					->getUploadedFileNameForStorageUsing(
						fn (TemporaryUploadedFile $file): string
						=> (string) str("image-" . $date . '-' . time() . Uuid::uuid4(). '.' . $file->getClientOriginalExtension())->prepend('user-'),
					)
					->columnSpanFull(),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('username')
					->copyable()
					->copyMessage('Username copied')
					->copyMessageDuration(1500)
					->searchable(),
				Tables\Columns\TextColumn::make('name')
					->searchable(),
				Tables\Columns\TextColumn::make('email')
					->fontFamily(FontFamily::Mono)
					->copyable()
					->copyMessage('Email address copied')
					->copyMessageDuration(1500)
					->searchable(),
				Tables\Columns\TextColumn::make('ammount_balance')
					->numeric(
						decimalPlaces: 2,
						decimalSeparator: ',',
						thousandsSeparator: '.',
					)
					->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 2, ', ', '.'))
					// ->money('IDR')
					->alignRight()
					->sortable()
					->summarize(Sum::make()->label('Total Ammount Balance')->numeric(
						decimalPlaces: 2,
						decimalSeparator: ',',
						thousandsSeparator: '.',
					)),
				Tables\Columns\TextColumn::make('birth_date')
					->date()
					->sortable(),
				Tables\Columns\TextColumn::make('phone_number')
					->searchable(),
				Tables\Columns\TextColumn::make('gender')
					->badge()
					->color(fn (Gender $state) => match ($state) {
						Gender::M => 'primary',
						Gender::F => 'yellow',
					})
					->searchable(),
				// Tables\Columns\IconColumn::make('is_active')
				//     ->boolean()
				//     ->sortable(),
				ToggleColumn::make('is_active')
					->onColor('success')
					->offColor('danger')
					->sortable(),
				Tables\Columns\TextColumn::make('email_verified_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('created_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				Tables\Columns\TextColumn::make('updated_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
			])
			->filters([
				SelectFilter::make('is_active')
					->label('Status')
					->options([
						true => 'Active',
						false => 'Non-Active',
					])
					->native(false),
				SelectFilter::make('gender')
					->label('Gender')
					->options(Gender::class)
					->native(false),
			])
			->actions([
				Tables\Actions\ViewAction::make()->color('info'),
				Tables\Actions\EditAction::make()->color('success'),

				Action::make('delete')
					->requiresConfirmation()
					->icon('heroicon-o-trash')
					->color('danger')
					->action(fn (User $record) => $record->delete()),
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

	public static function infolist(Infolist $infolist): Infolist
	{
		return $infolist
			->schema([
				Fieldset::make('User Details')
				->schema([
					TextEntry::make('username')->label('Username'),
					TextEntry::make('name')->label('Name'),
					TextEntry::make('email')->label('Email'),
				])->columns(3),
				Section::make('User Transaction History')
				->description('History of detailed user log transactions')
				->schema([
					Tabs::make('Label')
					->tabs([
						Tabs\Tab::make('Incoming Transactions')
							->icon('heroicon-m-arrow-trending-up')
							->iconPosition(IconPosition::After)
							// ->getEloquentQuery(fn (Builder $builder) => $builder->whereRelation('transaction_type', 'type', 'in'))
							->schema([
								RepeatableEntry::make('in_transactions')
								->label('Transaction Logs')
								->schema([
									TextEntry::make('code')->label('Code : ')
										->fontFamily(FontFamily::Mono)
										->inlineLabel(),
									TextEntry::make('ammount')->label('Ammount : ')
										->numeric(
											decimalPlaces: 0,
											decimalSeparator: ',',
											thousandsSeparator: '.',
										)
										->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 2, ', ', '.'))
										->inlineLabel()
										->color('success'),
									TextEntry::make('transaction_type.name')->label('Label : ')->inlineLabel(),
									TextEntry::make('transaction_type.type')->label('Type : ')->inlineLabel()
									->badge()
									->icon(fn (Type $state) => match ($state) {
										Type::in => 'heroicon-m-arrow-trending-up',
										Type::out => 'heroicon-m-arrow-trending-down',
									})
									->iconPosition(IconPosition::After)
									->color(fn (Type $state) => match ($state) {
										Type::in => 'success',
										Type::out => 'danger',
									}),
									TextEntry::make('note')->label('Note : ')->columnSpanFull(),
									Fieldset::make('Date')
									->schema([
										TextEntry::make('created_at')->dateTime()->inlineLabel(),
										TextEntry::make('updated_at')->dateTime()->inlineLabel(),
									]),
								])
								// ->contained(false)
								->columns(2),
								Fieldset::make('Date')
								->schema([
									TextEntry::make('created_at')->dateTime(),
									TextEntry::make('updated_at')->dateTime(),
								]),
							]),
						Tabs\Tab::make('Outcoming Transactions')
							->icon('heroicon-m-arrow-trending-down')
							->iconPosition(IconPosition::After)
							->schema([
								RepeatableEntry::make('out_transactions')
								->label('Transaction Logs')
								->schema([
									TextEntry::make('code')->label('Code : ')
										->fontFamily(FontFamily::Mono)
										->inlineLabel(),
									TextEntry::make('ammount')->label('Ammount : ')
										->numeric(
											decimalPlaces: 0,
											decimalSeparator: ',',
											thousandsSeparator: '.',
										)
										->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 2, ', ', '.'))
										->inlineLabel()
										->color('danger'),
									TextEntry::make('transaction_type.name')->label('Label : ')->inlineLabel(),
									TextEntry::make('transaction_type.type')->label('Type : ')->inlineLabel()
									->badge()
									->icon(fn (Type $state) => match ($state) {
										Type::in => 'heroicon-m-arrow-trending-up',
										Type::out => 'heroicon-m-arrow-trending-down',
									})
									->iconPosition(IconPosition::After)
									->color(fn (Type $state) => match ($state) {
										Type::in => 'success',
										Type::out => 'danger',
									}),
									TextEntry::make('note')->label('Note : ')->columnSpanFull(),
									Fieldset::make('Date')
									->schema([
										TextEntry::make('created_at')->dateTime()->inlineLabel(),
										TextEntry::make('updated_at')->dateTime()->inlineLabel(),
									]),
								])
								// ->contained(false)
								->columns(2),
								Fieldset::make('Date')
								->schema([
									TextEntry::make('created_at')->dateTime(),
									TextEntry::make('updated_at')->dateTime(),
								]),
							]),
					])
					->activeTab(1)
					->contained(false)
					->columnSpanFull(),
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
			'index' => Pages\ListUsers::route('/'),
			'create' => Pages\CreateUser::route('/create'),
			'edit' => Pages\EditUser::route('/{record}/edit'),
		];
	}
}
