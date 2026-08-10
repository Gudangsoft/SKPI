<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa()->with('programStudi.fakultas')->first();

        return view('student.dashboard', [
            'mahasiswa' => $mahasiswa,
        ]);
    }
}
