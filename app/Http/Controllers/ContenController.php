<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ContenController extends Controller
{

    // public function index()
    // {
    //     $data = DB::table('dashboard')
    //         ->join('categories', 'dashboard.category_id', '=', 'categories.category_id')
    //         ->where('dashboard.dashboard_name', 'Telkomsel POIN Campaign Summary')
    //         ->orderBy('dashboard.created_at', 'desc')
    //         ->select('dashboard.dashboard_name', 'dashboard.view_name','categories.category_name')
    //         ->get();

    //     return response()->json($data);
    // }

  public function index(Request $request)
{
    // Ambil semua data dashboard dari tabel dashboard
    $query = DB::table('dashboard')
        ->join('categories', 'dashboard.category_id', '=', 'categories.category_id')
        ->select('dashboard.*', 'categories.*');

    // Eksekusi query dan ambil data
    $data = $query->get();

    // Cek apakah ada data yang ditemukan
    if ($data->isEmpty()) {
        return response()->json(['error' => 'No dashboards found'], 404);
    }

    // Jika ada data, kembalikan respons JSON dengan data dashboard
    return response()->json($data);
}


    public function detail(Request $request)
{
     $dashboard_name = $request->query('dashboard_name'); // Mengambil nilai 'dashboard_name' dari parameter query

    $dashboard_name = str_replace('-', ' ', $dashboard_name);

    $detailData = DB::table('dashboard')
        ->where('dashboard_name', $dashboard_name)
        ->first();

    if (!$detailData) {
        // Tambahkan penanganan kesalahan di sini, misalnya redirect ke halaman lain atau tampilkan pesan kesalahan.
        return response()->json(['error' => 'Dashboard not found'], 404);
    }

    // Get the ticket from the API
    $response = Http::get('http://10.2.114.197:8000/ticket');
    $ticket = $response->json();

    // Get the view_name from the detailData
    $viewName = $detailData->view_name;

    // Combine embed_url, 'trusted', ticket, and view_name to form the final URL
    $url = "https://tabfire.telkomsel.co.id/trusted/{$ticket}/views/{$viewName}";

    $data = [
        'url' => $url,
        'detailData' => $detailData,
    ];

    return response()->json($data);
}

    // public function index()
    // {
    //     $data = DB::table('dashboard')
    //     ->join('categories', 'dashboard.category_id', '=', 'categories.category_id')
    //     ->whereIn('dashboard.dashboard_name', ['Telkomsel POIN Campaign Summary', 'Prepaid Performance'])
    //     ->orderBy('dashboard.created_at', 'desc')
    //     ->select('dashboard.dashboard_name', 'dashboard.view_name')
    //     ->get();

    //     return response()->json($data);
    // }
}
