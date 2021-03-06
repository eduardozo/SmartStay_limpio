<?php

namespace App\Http\Controllers;


use App\Events\NewOrderRequest;
use App\Guest;
use App\SpaAppointment;
use App\SpaTreatmentType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SpaAppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'show', 'edit', 'update', 'changeStatus']]);
        $this->middleware('auth', ['except' => ['orderList', 'store', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spaAppointments = SpaAppointment::paginate(12);

        return view('services.spa.index', compact('spaAppointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $guests = Guest::all();
        foreach ($guests as $guest) {
            $guest->guestRoomNumber = (isset($guest->rooms[0]->number))
                ? $guest->rooms[0]->number . ' - ' . $guest->firstname . ' ' . $guest->lastname : 'Not Found';
        }
        $guests = $guests->pluck('guestRoomNumber', 'id');

        $spaTypes = SpaTreatmentType::all();
        foreach ($spaTypes as $spaType) {
            $spaType->spaname = $spaType->name;
        }
        $spaTypes = $spaTypes->pluck('spaname', 'id');


        return view('services.spa.create', compact('guests', 'spaTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $input = Input::all();

        if ($request->ajax()) {
            if (Session::exists('guest_id')) {
                $input['guest_id'] = Session::get('guest_id');
            } else {
                return response()->json(['status' => false]);
            }
        }

        $rules     = [
            'guest_id'          => 'required|numeric',
            'day_hour'          => 'required|date',
            'treatment_type_id' => 'required|numeric',
        ];
        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
                DB::beginTransaction();
                $input['order_date'] = Carbon::today();
                $input['status']     = '0';
                $input['price'] = SpaTreatmentType::getPriceById($input['treatment_type_id']);
                $guest          = Guest::find($input['guest_id']);
                $spa            = $guest->spas()->create($input);
                DB::commit();

                event(new NewOrderRequest($spa->service_id, $input['guest_id'], $spa->id));

                if ($request->ajax()) {
                    //$return = ['status' => true];
                    return;
                } else {
                    $return = redirect()->route('spa.index')->with('status', 'Order added successfully.');
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            // No pasó el validador
            if ($request->ajax()) {
                //$return = ['status' => false];
                //return;
            } else {
                $return = redirect()->route('spa.create')->withErrors($validator->getMessageBag());
            }
        }

        return $return;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SpaAppointment $spa
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SpaAppointment $spa)
    {
        $guest = Guest::find($spa->guest_id);

        return view('services.spa.show', compact('spa', 'guest'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SpaAppointment $spa
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SpaAppointment $spa)
    {
        $guests = Guest::all();
        foreach ($guests as $guest) {
            $guest->guestRoomNumber = (isset($guest->rooms[0]->number))
                ? $guest->rooms[0]->number . ' - ' . $guest->firstname . ' ' . $guest->lastname : 'Not Found';
        }
        $guests = $guests->pluck('guestRoomNumber', 'id');

        $spaTypes = SpaTreatmentType::all();
        foreach ($spaTypes as $spaType) {
            $spaType->spaname = $spaType->name;
        }
        $spaTypes = $spaTypes->pluck('spaname', 'id');

        return view('services.spa.edit', compact('guests', 'spa', 'spaTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $input     = Input::all();
        $rules     = [
            'day_hour'          => 'required|date',
        ];
        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
                DB::beginTransaction();
                $spa = SpaAppointment::find($id);
                $spa->update($input);
                DB::commit();

                $return = redirect()->route('spa.index')->with('status', 'Order updated successfully.');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            $return = redirect()->route('spa.edit', $id)->withErrors($validator->getMessageBag());
        }

        return $return;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        SpaAppointment::find($id)->delete();
        $totalOrders = SpaAppointment::all()->count();
        if ($request->ajax()) {
            $return = response()->json([
                'total'   => $totalOrders,
                'message' => 'Order number: ' . $id . ' was deleted',
            ]);
        } else {
            $return = redirect()->back()->with('status', 'Guest deleted successfully');
        }

        return $return;
    }

    public function changeStatus($id)
    {
        $spa         = SpaAppointment::findOrFail($id);
        ($spa->status == 2) ? Guest::reduceBalance($spa) : null;
        $spa->status = ($spa->status === '1') ? '2' : '1';
        ($spa->status == 2) ? Guest::updateBalance($spa) : null;
        $spa->save();

        return response()->json($spa->status);
    }

    public function orderList()
    {
        $spa = SpaAppointment::where('guest_id', Session::get('guest_id'))->get();

        return $spa;
    }
}
