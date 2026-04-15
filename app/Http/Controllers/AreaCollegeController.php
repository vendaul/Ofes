<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\College;
use Illuminate\Http\Request;

class AreaCollegeController extends Controller
{
    public function index(Request $request)
    {
        $areas    = Area::orderBy('area_code')->get();
        $colleges = College::whereNull('deleted_at')->orderBy('area_code')->orderBy('name')->get();
        $selectedArea = $request->session()->get('selected_area');
        $selectedCollege = $request->session()->get('selected_college');

        return view('admin.area_college', compact('areas', 'colleges', 'selectedArea', 'selectedCollege'));
    }

    // ── AREA ──────────────────────────────────────────────

    public function storeArea(Request $request)
    {
        $request->validate([
            'area_code'    => 'required|string|max:100|unique:areas,area_code',
            'area_name'    => 'required|string|max:50',
            'area_address' => 'nullable|string|max:150',
        ]);

        Area::create($request->only('area_code', 'area_name', 'area_address'));

        return back()->with('success', 'Area added successfully.');
    }

    public function updateArea(Request $request, Area $area)
    {
        $request->validate([
            'area_code'    => 'required|string|max:100|unique:areas,area_code,' . $area->id,
            'area_name'    => 'required|string|max:50',
            'area_address' => 'nullable|string|max:150',
        ]);

        $area->update($request->only('area_code', 'area_name', 'area_address'));

        return back()->with('success', 'Area updated successfully.');
    }

    public function destroyArea(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }

    // ── COLLEGE ───────────────────────────────────────────

    public function storeCollege(Request $request)
    {
        $request->validate([
            'area_code'   => 'required|string|max:10',
            'name'        => 'required|string|max:255',
            'prefix'      => 'nullable|string|max:5',
            'head_officer'=> 'nullable|string|max:255',
        ]);

        College::create($request->only('area_code', 'name', 'prefix', 'head_officer'));

        return back()->with('success', 'College added successfully.');
    }

    public function updateCollege(Request $request, College $college)
    {
        $request->validate([
            'area_code'   => 'required|string|max:10',
            'name'        => 'required|string|max:255',
            'prefix'      => 'nullable|string|max:5',
            'head_officer'=> 'nullable|string|max:255',
        ]);

        $college->update($request->only('area_code', 'name', 'prefix', 'head_officer'));

        return back()->with('success', 'College updated successfully.');
    }

    public function destroyCollege(College $college)
    {
        $college->delete();
        return back()->with('success', 'College deleted.');
    }

    public function setActiveCollege(Request $request, College $college)
    {
        $request->session()->put('selected_area', $college->area_code);
        $request->session()->put('selected_college', (string) $college->id);

        return back()->with('success', 'Active college filter updated.');
    }
}
