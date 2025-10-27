<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;

class AcceptInvitationController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('invitation.accept', [
            'invitation' => Invitation::where('token', $request->token)->firstOrFail(),
        ]);
    }
}
