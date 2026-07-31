<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
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

        // Основной список
        $clients = $business->clients()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount(['appointments'])
            ->orderBy('appointments_count', 'desc')
            ->paginate(20);

        // Статистика
        $stats = [
            'total' => $business->clients()->count(),
            'new_this_month' => $business->clients()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'returning' => $business->clients()
                ->where('total_visits', '>', 1)
                ->count(),
            'average_check' => $business->clients()
                    ->where('total_spent', '>', 0)
                    ->avg('total_spent') ?? 0,
        ];

        // Топ по записям
        $topByVisits = $business->clients()
            ->withCount(['appointments'])
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        // Топ по тратам
        $topBySpent = $business->clients()
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        return view('clients.index', compact('clients', 'stats', 'topByVisits', 'topBySpent'));
    }

    public function create(Request $request)
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        try {
            $business = $this->getBusiness($request);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $client = $business->clients()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            \Log::info('Client created:', $client->toArray());

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'phone' => $client->phone,
                    'email' => $client->email,
                ],
                'message' => 'Клиент создан'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Client creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания клиента: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        $client->load(['appointments' => function ($query) {
            $query->orderBy('start_time', 'desc')
                ->limit(10)
                ->with(['service', 'employee']);
        }]);

        $stats = [
            'total_visits' => $client->total_visits,
            'total_spent' => $client->total_spent,
            'average_check' => $client->total_visits > 0
                ? $client->total_spent / $client->total_visits
                : 0,
            'last_visit' => $client->last_visit_at,
            'upcoming_appointments' => $client->appointments()
                ->where('start_time', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
        ];

        return view('clients.show', compact('client', 'stats'));
    }

    public function edit(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'birthday' => 'nullable|date',
            'instagram' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully!');
    }

    public function destroy(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        // Check if client has future appointments
        $hasFutureAppointments = $client->appointments()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasFutureAppointments) {
            return redirect()->route('clients.index')
                ->with('error', 'Cannot delete client with future appointments!');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully!');
    }

    public function history(Request $request, Client $client)
    {
        $business = $this->getBusiness($request);

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        $appointments = $client->appointments()
            ->with(['service', 'employee'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('clients.history', compact('client', 'appointments'));
    }

    public function search(Request $request)
    {
        $business = $this->getBusiness($request);

        $query = $request->get('q', '');

        $clients = $business->clients()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);

        return response()->json($clients);
    }

    public function export(Request $request)
    {
        $business = $this->getBusiness($request);

        $clients = $business->clients()
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($clients) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Phone', 'Email', 'Visits', 'Total Spent', 'Last Visit']);

            foreach ($clients as $client) {
                fputcsv($handle, [
                    $client->full_name,
                    $client->phone,
                    $client->email,
                    $client->total_visits,
                    $client->total_spent,
                    $client->last_visit_at?->format('d.m.Y'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $business = $this->getBusiness($request);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $header = array_shift($data);
        $count = 0;

        foreach ($data as $row) {
            if (count($row) >= 3) {
                $clientData = [
                    'first_name' => $row[0] ?? '',
                    'last_name' => $row[1] ?? '',
                    'phone' => $row[2] ?? '',
                    'email' => $row[3] ?? null,
                ];

                // Check if client exists
                $existing = $business->clients()
                    ->where('phone', $clientData['phone'])
                    ->first();

                if (!$existing) {
                    $business->clients()->create($clientData);
                    $count++;
                }
            }
        }

        return redirect()->route('clients.index')
            ->with('success', "Imported {$count} clients successfully!");
    }
}
