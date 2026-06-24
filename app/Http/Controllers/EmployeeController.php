<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends Controller
{
    public function search(Request $request): AnonymousResourceCollection
    {
        $q = $request->get('q', '');

        $employees = User::where('id', '!=', $request->user()->id)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get();

        return UserResource::collection($employees);
    }
}
