<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    protected function getBusiness(Request $request): Business
    {
        $business = $request->user()->currentBusiness;

        if (!$business) {
            abort(403, 'Please create a business first');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $settings = $business->settings ?? [
            'notifications' => [
                'sms_enabled' => true,
                'email_enabled' => true,
                'reminder_hours' => 24,
                'send_birthday_greetings' => true,
            ],
            'booking' => [
                'max_advance_days' => 30,
                'min_cancel_hours' => 2,
                'allow_online_payment' => false,
            ],
            'integration' => [
                'telegram_bot_enabled' => false,
                'telegram_bot_token' => null,
                'sms_provider' => 'log',
            ],
        ];

        return view('settings.index', compact('business', 'settings'));
    }

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
            return back()->withErrors($validator)->withInput();
        }

        $business->update($request->all());

        return redirect()->route('settings.index')
            ->with('success', 'General settings updated successfully!');
    }

    public function updateNotifications(Request $request)
    {
        $business = $this->getBusiness($request);

        $validated = $request->validate([
            'sms_enabled' => 'nullable|boolean',
            'email_enabled' => 'nullable|boolean',
            'reminder_hours' => 'nullable|integer|min:1|max:72',
            'send_birthday_greetings' => 'nullable|boolean',
        ]);

        $settings = $business->settings ?? [];
        $settings['notifications'] = array_merge(
            $settings['notifications'] ?? [],
            $validated
        );

        $business->update(['settings' => $settings]);

        return redirect()->route('settings.index')
            ->with('success', 'Notification settings updated successfully!');
    }

    public function updateIntegration(Request $request)
    {
        $business = $this->getBusiness($request);

        $validated = $request->validate([
            'telegram_bot_enabled' => 'nullable|boolean',
            'telegram_bot_token' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|in:log,nexmo,infobip,smsc',
        ]);

        $settings = $business->settings ?? [];
        $settings['integration'] = array_merge(
            $settings['integration'] ?? [],
            $validated
        );

        $business->update(['settings' => $settings]);

        return redirect()->route('settings.index')
            ->with('success', 'Integration settings updated successfully!');
    }

    public function uploadLogo(Request $request)
    {
        $business = $this->getBusiness($request);

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($business->logo_url) {
            Storage::disk('public')->delete($business->logo_url);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $business->update(['logo_url' => $path]);

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'message' => 'Logo uploaded successfully!',
        ]);
    }

    public function deleteLogo(Request $request)
    {
        $business = $this->getBusiness($request);

        if ($business->logo_url) {
            Storage::disk('public')->delete($business->logo_url);
            $business->update(['logo_url' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logo deleted successfully!',
        ]);
    }

    public function widget(Request $request)
    {
        $business = $this->getBusiness($request);

        return view('settings.widget', compact('business'));
    }

    public function widgetPreview(Request $request)
    {
        $business = $this->getBusiness($request);

        return view('settings.widget-preview', compact('business'));
    }
}
