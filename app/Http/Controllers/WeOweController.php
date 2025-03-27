<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\WeOweItem;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;

class WeOweController extends Controller
{
    /**
     * Display a listing of we-owe items.
     */
    public function index()
    {
        $weOweItems = WeOweItem::with(['vehicle', 'assignedTo', 'vendor'])
            ->latest()
            ->paginate(15);
            
        $vendors = Vendor::orderBy('name')->get();
        $users = User::role('staff')->orderBy('name')->get();
        
        return view('we-owe.index', compact('weOweItems', 'vendors', 'users'));
    }
}
