<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    use ApiResponseTrait;

    protected function getBusiness(Request $request): Business
    {
        $businessId = $request->user()->current_business_id;
        $business = Business::findOrFail($businessId);

        if (!$request->user()->hasBusinessAccess($businessId)) {
            abort(403, 'You do not have access to this business');
        }

        return $business;
    }

    /**
     * Get all settings
     */
    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $settings = [
            'general' => [
                'name' => $business->name,
                'phone' => $business->phone,
                'email' => $business->email,
                'address' => $business->address,
                'city' => $business->city,
                'region' => $business->region,
                'description' => $business->description,
                'logo_url' => $business->logo_url ? Storage::url($business->logo_url) : null,
                'timezone' => $business->timezone,
                'status' => $business->status,
                'trial_ends_at' => $business->trial_ends_at?->toISOString(),
                'is_on_trial' => $business->isOnTrial(),
                'is_active' => $business->isActive(),
            ],
            'notifications' => $business->settings['notifications'] ?? [
                    'sms_enabled' => true,
                    'email_enabled' => true,
                    'reminder_hours' => 24,
                    'send_birthday_greetings' => true,
                    'send_promotions' => false,
                    'reminder_channels' => ['sms', 'email'],
                ],
            'booking' => $business->settings['booking'] ?? [
                    'max_advance_days' => 30,
                    'min_cancel_hours' => 2,
                    'allow_online_payment' => false,
                    'allow_deposit' => false,
                    'deposit_percent' => 0,
                    'auto_confirm' => false,
                ],
            'integration' => $business->settings['integration'] ?? [
                    'telegram_bot_enabled' => false,
                    'telegram_bot_token' => null,
                    'sms_provider' => 'log',
                    'sms_api_key' => null,
                    'email_provider' => 'log',
                    'google_calendar_sync' => false,
                    'yclients_sync' => false,
                ],
            'widget' => [
                'enabled' => $business->settings['widget']['enabled'] ?? true,
                'primary_color' => $business->settings['widget']['primary_color'] ?? '#4F46E5',
                'button_text' => $business->settings['widget']['button_text'] ?? 'Записаться онлайн',
                'position' => $business->settings['widget']['position'] ?? 'bottom-right',
                'display_on' => $business->settings['widget']['display_on'] ?? ['desktop', 'mobile'],
                'custom_css' => $business->settings['widget']['custom_css'] ?? null,
            ],
        ];

        return $this->successResponse($settings);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'timezone' => 'nullable|string|timezone',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $business->update($request->all());

        return $this->successResponse(
            $business->only(['name', 'phone', 'email', 'address', 'city', 'region', 'description', 'timezone']),
            'General settings updated successfully'
        );
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'sms_enabled' => 'nullable|boolean',
            'email_enabled' => 'nullable|boolean',
            'reminder_hours' => 'nullable|integer|min:1|max:72',
            'send_birthday_greetings' => 'nullable|boolean',
            'send_promotions' => 'nullable|boolean',
            'reminder_channels' => 'nullable|array',
            'reminder_channels.*' => 'in:sms,email,telegram',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $settings = $business->settings ?? [];
        $settings['notifications'] = array_merge(
            $settings['notifications'] ?? [],
            $request->only([
                'sms_enabled',
                'email_enabled',
                'reminder_hours',
                'send_birthday_greetings',
                'send_promotions',
                'reminder_channels',
            ])
        );

        $business->update(['settings' => $settings]);

        return $this->successResponse(
            $settings['notifications'],
            'Notification settings updated successfully'
        );
    }

    /**
     * Update booking settings
     */
    public function updateBooking(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'max_advance_days' => 'nullable|integer|min:1|max:365',
            'min_cancel_hours' => 'nullable|integer|min:0|max:72',
            'allow_online_payment' => 'nullable|boolean',
            'allow_deposit' => 'nullable|boolean',
            'deposit_percent' => 'nullable|numeric|min:0|max:100',
            'auto_confirm' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $settings = $business->settings ?? [];
        $settings['booking'] = array_merge(
            $settings['booking'] ?? [],
            $request->only([
                'max_advance_days',
                'min_cancel_hours',
                'allow_online_payment',
                'allow_deposit',
                'deposit_percent',
                'auto_confirm',
            ])
        );

        $business->update(['settings' => $settings]);

        return $this->successResponse(
            $settings['booking'],
            'Booking settings updated successfully'
        );
    }

    /**
     * Update integration settings
     */
    public function updateIntegration(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'telegram_bot_enabled' => 'nullable|boolean',
            'telegram_bot_token' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|in:log,nexmo,infobip,smsc,telegram',
            'sms_api_key' => 'nullable|string|max:255',
            'email_provider' => 'nullable|string|in:log,smtp,sendgrid,mailgun',
            'google_calendar_sync' => 'nullable|boolean',
            'yclients_sync' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Don't expose sensitive data
        $data = $request->only([
            'telegram_bot_enabled',
            'telegram_bot_token',
            'sms_provider',
            'sms_api_key',
            'email_provider',
            'google_calendar_sync',
            'yclients_sync',
        ]);

        $settings = $business->settings ?? [];
        $settings['integration'] = array_merge(
            $settings['integration'] ?? [],
            $data
        );

        $business->update(['settings' => $settings]);

        // Remove sensitive data from response
        $response = $settings['integration'];
        unset($response['sms_api_key']);
        unset($response['telegram_bot_token']);

        return $this->successResponse(
            $response,
            'Integration settings updated successfully'
        );
    }

    /**
     * Upload business logo
     */
    public function uploadLogo(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Delete old logo
        if ($business->logo_url) {
            Storage::disk('public')->delete($business->logo_url);
        }

        $path = $request->file('logo')->store('logos/' . $business->id, 'public');
        $business->update(['logo_url' => $path]);

        return $this->successResponse([
            'logo_url' => Storage::url($path),
            'logo_path' => $path,
        ], 'Logo uploaded successfully');
    }

    /**
     * Delete business logo
     */
    public function deleteLogo(Request $request)
    {
        $business = $this->getBusiness($request);

        if ($business->logo_url) {
            Storage::disk('public')->delete($business->logo_url);
            $business->update(['logo_url' => null]);
        }

        return $this->successResponse(null, 'Logo deleted successfully');
    }

    /**
     * Get widget settings and embed code
     */
    public function widget(Request $request)
    {
        $business = $this->getBusiness($request);

        $widgetSettings = $business->settings['widget'] ?? [
            'enabled' => true,
            'primary_color' => '#4F46E5',
            'button_text' => 'Записаться онлайн',
            'position' => 'bottom-right',
            'display_on' => ['desktop', 'mobile'],
            'custom_css' => null,
        ];

        // Generate embed code
        $embedCode = $this->generateWidgetEmbedCode($business, $widgetSettings);

        return $this->successResponse([
            'settings' => $widgetSettings,
            'embed_code' => $embedCode,
            'widget_url' => route('public.booking', $business->slug),
        ]);
    }

    /**
     * Update widget settings
     */
    public function updateWidget(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'enabled' => 'nullable|boolean',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'button_text' => 'nullable|string|max:50',
            'position' => 'nullable|string|in:bottom-right,bottom-left,top-right,top-left',
            'display_on' => 'nullable|array',
            'display_on.*' => 'in:desktop,mobile',
            'custom_css' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $settings = $business->settings ?? [];
        $settings['widget'] = array_merge(
            $settings['widget'] ?? [],
            $request->only([
                'enabled',
                'primary_color',
                'button_text',
                'position',
                'display_on',
                'custom_css',
            ])
        );

        $business->update(['settings' => $settings]);

        return $this->successResponse(
            $settings['widget'],
            'Widget settings updated successfully'
        );
    }

    /**
     * Get widget embed code only
     */
    public function widgetEmbed(Request $request)
    {
        $business = $this->getBusiness($request);

        $widgetSettings = $business->settings['widget'] ?? [];
        $embedCode = $this->generateWidgetEmbedCode($business, $widgetSettings);

        return $this->successResponse([
            'embed_code' => $embedCode,
            'widget_url' => route('public.booking', $business->slug),
        ]);
    }

    /**
     * Generate widget embed code
     */
    protected function generateWidgetEmbedCode($business, array $settings): string
    {
        $color = $settings['primary_color'] ?? '#4F46E5';
        $buttonText = $settings['button_text'] ?? 'Записаться онлайн';
        $position = $settings['position'] ?? 'bottom-right';

        $positionStyles = [
            'bottom-right' => 'bottom: 20px; right: 20px;',
            'bottom-left' => 'bottom: 20px; left: 20px;',
            'top-right' => 'top: 20px; right: 20px;',
            'top-left' => 'top: 20px; left: 20px;',
        ];

        $style = $positionStyles[$position] ?? 'bottom: 20px; right: 20px;';

        return <<<HTML
<!-- ZapisKavkaz Booking Widget -->
<div id="zapis-kavkaz-widget" style="position: fixed; {$style}; z-index: 9999;">
    <a href="{$this->getPublicBookingUrl($business->slug)}"
       target="_blank"
       style="
           display: inline-block;
           padding: 12px 24px;
           background-color: {$color};
           color: #ffffff;
           border-radius: 8px;
           text-decoration: none;
           font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
           font-size: 16px;
           font-weight: 600;
           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           transition: transform 0.2s, box-shadow 0.2s;
       "
       onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.15)';"
       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
        {$buttonText}
    </a>
</div>
{$this->getWidgetCustomCss($settings['custom_css'] ?? null)}
<!-- End ZapisKavkaz Widget -->
HTML;
    }

    /**
     * Get widget custom CSS
     */
    protected function getWidgetCustomCss(?string $customCss): string
    {
        if (!$customCss) {
            return '';
        }

        return <<<HTML
<style>
/* ZapisKavkaz Widget Custom CSS */
{$customCss}
</style>
HTML;
    }

    /**
     * Get public booking URL
     */
    protected function getPublicBookingUrl(string $slug): string
    {
        return config('app.url') . '/booking/' . $slug;
    }
}
