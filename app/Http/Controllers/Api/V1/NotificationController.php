<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Notification;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\V1\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
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

    public function index(Request $request)
    {
        $business = $this->getBusiness($request);

        $query = $business->notifications()
            ->with(['user', 'client', 'appointment']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->has('unread') && $request->boolean('unread')) {
            $query->unread();
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $notifications = $query
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginatedResponse($notifications, NotificationResource::class);
    }

    public function markRead(Request $request)
    {
        $business = $this->getBusiness($request);

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $business->notifications()
            ->whereIn('id', $request->ids)
            ->update(['read_at' => now(), 'status' => 'read']);

        return $this->successResponse(
            null,
            'Notifications marked as read'
        );
    }

    public function markAllRead(Request $request)
    {
        $business = $this->getBusiness($request);

        $updated = $business->notifications()
            ->unread()
            ->update(['read_at' => now(), 'status' => 'read']);

        return $this->successResponse(
            ['updated_count' => $updated],
            'All notifications marked as read'
        );
    }

    public function destroy(Request $request, Notification $notification)
    {
        $business = $this->getBusiness($request);

        if ($notification->business_id !== $business->id) {
            return $this->errorResponse('Notification not found', 404);
        }

        $notification->delete();

        return $this->successResponse(
            null,
            'Notification deleted successfully'
        );
    }

    public function counts(Request $request)
    {
        $business = $this->getBusiness($request);

        $total = $business->notifications()->count();
        $unread = $business->notifications()->unread()->count();

        $byType = $business->notifications()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        $byStatus = $business->notifications()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $byChannel = $business->notifications()
            ->selectRaw('channel, COUNT(*) as count')
            ->groupBy('channel')
            ->get()
            ->pluck('count', 'channel');

        return $this->successResponse([
            'total' => $total,
            'unread' => $unread,
            'by_type' => $byType,
            'by_status' => $byStatus,
            'by_channel' => $byChannel,
        ]);
    }
}
