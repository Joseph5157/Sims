<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Stancl\Tenancy\Features\UserImpersonation;

class TenantImpersonateController extends Controller
{
    public function __invoke(string $token): Response
    {
        return UserImpersonation::makeResponse($token);
    }
}
