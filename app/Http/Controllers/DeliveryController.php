<?php    
namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Tracking;
use App\Models\vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class DeliveryController extends Controller
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
        $users_id =  DB::table('drivers')
        ->join('users', 'drivers.user_id', '=', 'users.id')
        ->select('drivers.id')
        ->where('drivers.user_id', '=', Auth::user()->id)
        ->first();
        $data= json_decode( json_encode($users_id), true);
        $drivers = DB::table('deliveries')
        ->join('drivers', 'drivers.id', '=', 'deliveries.id_sopir')
        ->join('vehicles', 'vehicles.id', '=', 'deliveries.id_kendaraan')
        ->join('checkpoints', 'checkpoints.id', '=', 'deliveries.id_tujuan')
        ->select('deliveries.*', 'drivers.nama_sopir as sopir', 'vehicles.nama_kendaraan as kendaraan', 'checkpoints.tujuan as tujuan')
        ->where('deliveries.id_sopir', '=', $data)
        ->latest()
        ->get();
        $deliveries = DB::table('deliveries')
        ->join('drivers', 'drivers.id', '=', 'deliveries.id_sopir')
        ->join('vehicles', 'vehicles.id', '=', 'deliveries.id_kendaraan')
        ->join('checkpoints', 'checkpoints.id', '=', 'deliveries.id_tujuan')
        ->select('deliveries.*', 'drivers.nama_sopir as sopir', 'vehicles.nama_kendaraan as kendaraan', 'checkpoints.tujuan as tujuan')
        ->latest()
        ->get();
        return view('deliveries.index',compact('deliveries','drivers'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $drivers = DB::table('drivers')->get();
        $vehicles = DB::table('vehicles')->get();
        $checkpoints = DB::table('checkpoints')->get();
        return view('deliveries.create',compact('drivers','vehicles','checkpoints'));
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
            'id_kendaraan' => 'required',
            'id_sopir' => 'required',
            'kondisi' => 'required',
            'berat_barang' => 'required',
            'jenis_barang' => 'required',
            'tgl' => 'required',
            'jml_barang' => 'required',
            'id_tujuan' => 'required',
        ]);
    
        $status = 'Pending';
          // Get the last order id
$lastDeliveryId = DB::table('deliveries')
->select('deliveries.id')
->orderBy('id', 'desc')
->first()
->id ?? 0;

// Get last 3 digits of last order id
$lastIncreament = substr($lastDeliveryId, -3);

// Make a new order id with appending last increment + 1
$newOrderId = 'DLVR' . date('Ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        Delivery::create(
            [
                'id' => $newOrderId,
                'id_kendaraan' => $request->id_kendaraan,
                'id_sopir' => $request->id_sopir,
                'kondisi' => $request->kondisi,
                'berat_barang' => $request->berat_barang,
                'jenis_barang' => $request->jenis_barang,
                'tgl' => $request->tgl,
                'jml_barang' => $request->jml_barang,
                'id_tujuan' => $request->id_tujuan,
                'status' => $status,
                'created_at' => now()
            ]
        );
    
        return redirect()->route('deliveries.index')
                        ->with('success','Deliveries created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): View
    {
        $id_tujuan = DB::table('deliveries')
        ->select('deliveries.id_tujuan')
        ->where('deliveries.id','=',$id)
        ->first();
        $data= json_decode( json_encode($id_tujuan), true);
        $checkpoints = DB::table('checkpoints')
            ->select('checkpoints.*')
            ->where('checkpoints.id', '=', $data)
            ->first();
        $delivery = DB::table('deliveries')
        ->join('checkpoints', 'checkpoints.id', '=', 'deliveries.id_tujuan')
        ->select('deliveries.*','checkpoints.tujuan as tujuan')
        ->where('deliveries.id','=',$id)
        ->first();
        $trackings = DB::table('trackings')
        ->select('trackings.*')
        ->where('trackings.id_delivery','=',$id)
        ->first();

        return view('deliveries.show')->with(
            [
                'checkpoints' => $checkpoints,
                'delivery' => $delivery,
                'trackings' => $trackings,
            ]
        );
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id): View
    {
        
        $drivers = DB::table('drivers')->get();
        $vehicles = DB::table('vehicles')->get();
        $checkpoints = DB::table('checkpoints')->get();
        $delivery = DB::table('deliveries')
        ->where('id', '=', $id)
        ->first();
        return view('deliveries.edit',compact('delivery','drivers','vehicles','checkpoints'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
         request()->validate([
            'id_kendaraan' => 'required',
            'id_sopir' => 'required',
            'kondisi' => 'required',
            'berat_barang' => 'required',
            'jenis_barang' => 'required',
            'tgl' => 'required',
            'jml_barang' => 'required',
            'id_tujuan' => 'required',
            'status' => 'required',
        ]);
    
        $delivery->update($request->all());
    
        return redirect()->route('deliveries.index')
                        ->with('success','Deliveries updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delivery $delivery): RedirectResponse
    {
        $delivery->delete();
    
        return redirect()->route('deliveries.index')
                        ->with('success','Deliveries deleted successfully');
    }

    public function approval($id)
    {
        // Get the last order id
$lastTrackId = DB::table('trackings')
->select('trackings.id')
->orderBy('id', 'desc')
->first()
->id ?? 0;

// Get last 3 digits of last order id
$lastIncreament = substr($lastTrackId, -3);

// Make a new order id with appending last increment + 1
$newOrderId = 'TRCK' . date('Ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        $deliveries = Delivery::find($id);
        $data= json_decode( json_encode($id), true);
        $trackings = Tracking::create(
            [
                'id' => $newOrderId,
                'id_delivery' => $data,
                'id_tujuan' => null,
                'checkpoint1' => null,
                'tanggal1' => null,
                'checkpoint2' => null,
                'tanggal2' => null,
                'checkpoint3' => null,
                'tanggal3' => null,
                'checkpoint4' => null,
                'tanggal4' => null,
                'checkpoint5' => null,
                'tanggal5' => null,
                'created_at' => now()
            ]
        );
        if ($deliveries->status == 'Pending') {
            $deliveries->status = 'On Delivery';
            $deliveries->save();
            $trackings->save();
        } 
        return redirect()->back();
    }

    public function finish($id)
    {
        $deliveries = Delivery::find($id);
        if ($deliveries->status == 'On Delivery') {
            $deliveries->status = 'Delivered';
            $deliveries->save();
        } 
        return redirect()->route('deliveries.index')
                        ->with('success','Deliveries updated successfully');
    }

    
}