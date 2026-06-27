<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        return response()->json(Label::orderBy('sort_order')->get());
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'key'     => 'required|string|unique:labels,key|max:50',
            'name_ar' => 'required|string|max:100',
            'name_en' => 'required|string|max:100',
            'name_fr' => 'required|string|max:100',
            'color'   => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        return response()->json(Label::create($v), 201);
    }
}
