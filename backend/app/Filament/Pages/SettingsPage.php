<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use BackedEnum;
use UnitEnum;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|UnitEnum|null $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'Business Settings';

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                TextInput::make('site_name')->label('Site Name'),
                                TextInput::make('site_email')->label('Support Email'),
                                TextInput::make('site_phone')->label('Support Phone'),
                            ]),
                        Tabs\Tab::make('SMS Gateway')
                            ->schema([
                                Select::make('sms_gateway')
                                    ->options([
                                        'mimsms' => 'MiM SMS',
                                        'reve' => 'Reve SMS',
                                        'bulk' => 'Bulk SMS BD',
                                    ]),
                                TextInput::make('sms_api_key')->password(),
                                TextInput::make('sms_sender_id'),
                            ]),
                        Tabs\Tab::make('Courier')
                            ->schema([
                                Section::make('Steadfast Courier')
                                    ->schema([
                                        TextInput::make('steadfast_api_key')->password(),
                                        TextInput::make('steadfast_secret_key')->password(),
                                    ]),
                                Section::make('Pathao Courier')
                                    ->schema([
                                        TextInput::make('pathao_client_id'),
                                        TextInput::make('pathao_client_secret')->password(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Payments')
                            ->schema([
                                Section::make('bKash')
                                    ->schema([
                                        TextInput::make('bkash_app_key'),
                                        TextInput::make('bkash_app_secret')->password(),
                                        TextInput::make('bkash_username'),
                                        TextInput::make('bkash_password')->password(),
                                    ]),
                                Section::make('Nagad')
                                    ->schema([
                                        TextInput::make('nagad_merchant_id'),
                                        TextInput::make('nagad_public_key'),
                                        TextInput::make('nagad_private_key')->password(),
                                    ]),
                                Section::make('SSLCommerz')
                                    ->schema([
                                        TextInput::make('ssl_store_id'),
                                        TextInput::make('ssl_store_password')->password(),
                                        Toggle::make('ssl_sandbox')->label('Sandbox Mode'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Site Tracking')
                            ->schema([
                                TextInput::make('fb_pixel_id')->label('FB Pixel ID'),
                                TextInput::make('fb_capi_token')->label('FB CAPI Access Token')->password(),
                                TextInput::make('gtm_id')->label('Google Tag Manager ID'),
                                TextInput::make('ga_tracking_id')->label('Google Analytics ID'),
                            ]),
                        Tabs\Tab::make('Fraud Check')
                            ->schema([
                                TextInput::make('fraud_check_api_key')->label('Fraud Check API Key')->password(),
                                TextInput::make('fraud_check_endpoint')->label('Fraud Check Endpoint'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
