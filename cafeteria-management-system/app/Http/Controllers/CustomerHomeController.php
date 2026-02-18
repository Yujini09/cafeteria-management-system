<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:customer']);
    }

    public function index(): View
    {
        $menus = \App\Models\Menu::with('items')->get();
        return view('customer.home', compact('menus'));
    }
}
