<?php    
namespace App\Http\Controllers;

use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use DB;
class CheckpointController extends Controller
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
        $checkpoints = DB::table('checkpoints')->get();
        return view('checkpoints.index',compact('checkpoints'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('checkpoints.create');
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
            'tujuan' => ['required', 'unique:checkpoints'],
        ]);
    // Get the last order id
$lastVehicleId = DB::table('checkpoints')
->select('checkpoints.id')
->orderBy('id', 'desc')
->first()
->id ?? 0;

// Get last 3 digits of last order id
$lastIncreament = substr($lastVehicleId, -3);

// Make a new order id with appending last increment + 1
$newOrderId = 'CHK' . date('Ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        Checkpoint::create([
        'id' => $newOrderId,
        'tujuan' => $request->tujuan,
        'checkpoint1' => $request->checkpoint1,
        'checkpoint2' => $request->checkpoint2,
        'checkpoint3' => $request->checkpoint3,
        'checkpoint4' => $request->checkpoint4,
        'checkpoint5' => $request->checkpoint5,
        'created_at' => now(),
        ]);
    
        return redirect()->route('checkpoints.index')
                        ->with('success','Checkpoints created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Checkpoint $checkpoint): View
    {
        return view('checkpoints.show',compact('checkpoint'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id): View
    {   $checkpoint = DB::table('checkpoints')
        ->where('id', '=', $id)
        ->first();
        return view('checkpoints.edit',compact('checkpoint'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Checkpoint $checkpoint): RedirectResponse
    {
         request()->validate([
            'tujuan' => 'required',
        ]);
    
        $checkpoint->update($request->all());
    
        return redirect()->route('checkpoints.index')
                        ->with('success','Checkpoints updated successfully');
    }
    
    
    public function destroy(Checkpoint $checkpoint): RedirectResponse
    {
        $checkpoint->delete();
    
        return redirect()->route('checkpoints.index')
                        ->with('success','Checkpoints deleted successfully');
    }
}