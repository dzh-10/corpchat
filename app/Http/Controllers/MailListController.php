<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MailList;
use Illuminate\Http\Request;

class MailListController extends Controller
{
    public function index(Request $request)
    {
        $lists = MailList::where('user_id', $request->user()->id)
            ->get()
            ->map(fn ($l) => [
                'id'     => $l->id,
                'name'   => $l->name,
                'emails' => $l->emails,
                'count'  => $l->count,
            ]);

        return response()->json(['data' => $lists]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'     => 'required|string|max:100',
            'emails'   => 'required|array|min:1',
            'emails.*' => 'email|max:255',
        ]);

        $list = MailList::create([
            'user_id' => $request->user()->id,
            'name'    => $v['name'],
            'emails'  => $v['emails'],
        ]);

        return response()->json(['success' => true, 'data' => $list], 201);
    }

    public function destroy(Request $request, MailList $mailList)
    {
        if ($mailList->user_id !== $request->user()->id) {
            abort(403);
        }

        $mailList->delete();

        return response()->json(['success' => true]);
    }
}
