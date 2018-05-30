<?php

namespace App\Http\Controllers;

use App\Guest;
use App\Restaurant;
use App\RoomType;
use App\Services;

class AdminDashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurants = Restaurant::all();
        $serviceName = Services::getServiceName($restaurants[0]->id);
        foreach ($restaurants as $key => $restaurant) {
            $restaurant->serviceName = $serviceName;
        }

        return view('admin.dashboard', compact('restaurants'));
    }

    public function pendingOrders()
    {

    }

    public function dispatchedOrders()
    {

    }

    public function checkout()
    {
        $guests = Guest::getGuestsByCheckoutDate();
        foreach ($guests as $guest) {
            $guest->number        = $guest->rooms[0]->number;
            $guest->checkin_date  = $guest->rooms[0]->pivot->checkin_date;
            $guest->checkout_date = $guest->rooms[0]->pivot->checkout_date;
            $guest->roomType      = RoomType::find($guest->rooms[0]->type_id)->name;
        }

        return view('admin.checkout', compact('guests'));
    }

    public function checkin()
    {
        $guests = Guest::getGuestsByCheckinDate();
        foreach ($guests as $guest) {
            $guest->number        = $guest->rooms[0]->number;
            $guest->checkin_date  = $guest->rooms[0]->pivot->checkin_date;
            $guest->checkout_date = $guest->rooms[0]->pivot->checkout_date;
            $guest->roomType      = RoomType::find($guest->rooms[0]->type_id)->name;
        }

        return view('admin.checkin', compact('guests'));
    }

}
