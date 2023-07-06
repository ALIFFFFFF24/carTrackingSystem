<?php    
namespace App\Http\Controllers;
    
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
class DriverController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $drivers = DB::table('drivers')->get();
        return view('drivers.index',compact('drivers'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $users = User::all();
        return view('drivers.create', compact('users'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'user_id' => ['required', 'unique:drivers'],
            'no_telp' => 'required',
        ]);
        $driver = DB::table('users')->select('name')->where('id', $request->user_id)->get();
        foreach ($driver as $d) {
            $namaDriver = $d->name;
        };

        // Get the last order id
        $lastUserId = DB::table('drivers')
        ->select('drivers.id')
        ->orderBy('id', 'desc')
        ->first()
        ->id;

// Get last 3 digits of last order id
$lastIncreament = substr($lastUserId, -3);

// Make a new order id with appending last increment + 1
$newOrderId = 'DRV' . date('Ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        Driver::create(
            [
                'id' => $newOrderId,
                'user_id' => $request->user_id,
                'nama_sopir' => $namaDriver,
                'no_telp' => $request->no_telp,
                'created_at' => now()
            ]
        );

        return redirect()->route('drivers.index')
                        ->with('success','Driver created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver): View
    {
        return view('drivers.show',compact('driver'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id): View
    {
        
        $driver = DB::table('drivers')
        ->where('id', '=', $id)
        ->first();
        return view('drivers.edit',compact('driver'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driver $driver): RedirectResponse
    {
         request()->validate([
            'no_telp' => 'required',
        ]);
    
        $driver->update($request->all());
    
        return redirect()->route('drivers.index')
                        ->with('success','Driver updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();
    
        return redirect()->route('drivers.index')
                        ->with('success','Driver deleted successfully');
    }
}