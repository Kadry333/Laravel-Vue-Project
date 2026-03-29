<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request) {
        $defaultYear = Carbon::now()->year;

        $genderYear = $request->query('gender_year', $defaultYear);
        $revenueYear = $request->query('revenue_year', $defaultYear);
        $countriesYear = $request->query('countries_year', $defaultYear);
        $clientsYear = $request->query('clients_year', $defaultYear);

        $availableYears = Reservation::select(DB::raw('YEAR(check_in_date) as year'))
            ->whereNotNull('check_in_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$defaultYear];
        }

        $genderData = Reservation::whereYear('check_in_date', $genderYear)
            ->join('users', 'reservations.client_id', '=', 'users.id')
            ->selectRaw('users.gender, COUNT(reservations.id) as count')
            ->groupBy('users.gender')
            ->pluck('count', 'gender')
            ->toArray();

        $genderStats = [
            'male' => $genderData['male'] ?? 0,
            'female' => $genderData['female'] ?? 0,
        ];

        $revenueData = Reservation::whereYear('check_in_date', $revenueYear)
            ->selectRaw('MONTH(check_in_date) as month, SUM(paid_price) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $revenueStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueStats[] = $revenueData[$i] ?? 0;
        }

        $allCountries = Reservation::whereYear('check_in_date', $countriesYear)
            ->with('client.country')
            ->get()
            ->groupBy(function($reservation) {
                return $reservation->client?->country?->official_name ?? 'Unknown';
            })
            ->map(function($group, $countryName) {
                return [
                    'country' => $countryName,
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->values();

        if ($allCountries->count() > 10) {
            $topCountries = $allCountries->take(9);
            $othersCount = $allCountries->slice(9)->sum('count');
            $topCountries->push(['country' => 'Others', 'count' => $othersCount]);
            $countriesStats = $topCountries->toArray();
        } else {
            $countriesStats = $allCountries->toArray();
        }

        $topClients = Reservation::whereYear('check_in_date', $clientsYear)
            ->join('users', 'reservations.client_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(reservations.id) as total')
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();

        return Inertia::render('AdminDashboard/AdminDashboard', [
            'genderStats' => $genderStats,
            'revenueStats' => $revenueStats,
            'countriesStats' => $countriesStats,
            'topClients' => $topClients,
            'currentYears' => [
                'gender' => (int)$genderYear,
                'revenue' => (int)$revenueYear,
                'countries' => (int)$countriesYear,
                'clients' => (int)$clientsYear,
            ],
            'availableYears' => $availableYears
        ]);
    }
}
