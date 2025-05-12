<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Chef;
use App\Models\Order;
use App\Models\Branch;
use App\Enums\OrderStatus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ChefController extends Controller
{
    public function showLoginForm()
    {
        return view('chef.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('chef')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/chef/orders');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('chef')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/chef/login');
    }

    public function showOrders()
    {
        $chef = Auth::guard('chef')->user();
        if (!$chef) {
            return redirect('/chef/login');
        }
        $orders = Order::with('branch')
            ->where('branch_id', $chef->branch_id)
            ->where('status', 7)
            ->orwhere('status', 4)
            ->get();
        return view('chef.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . OrderStatus::PROCESSING,
        ]);

        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }



    public function index()
    {
        $chefs = Chef::with('branch')->get();
        return view('chef.index', compact('chefs'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('chef.create', compact('branches'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:chefs,email',
            'password' => 'required|string|min:8',
            'branch_id' => 'required|exists:branches,id',
        ]);

        Chef::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('chef_management.index')->with('success', 'Chef added successfully.');
    }
    public function destroy(Chef $chef)
    {
        $chef->delete();
        return redirect()->route('chef_management.index')->with('success', 'Chef deleted successfully.');
    }
}
