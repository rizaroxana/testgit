<?php

namespace App\Http\Controllers;

use App\Repositories\ProdusRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProdusController extends Controller
{
    private $produs;

    public function __construct(ProdusRepository $produsRepository) {

        $this->produs = $produsRepository;
        $this->baseUrl = request()->getSchemeAndHttpHost();
        $this->app     = app();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produse = DB::table('produs')->get();
        return view('index',compact('produse'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::table('produs')->insert([
                'nume' => $request->input('nume'),
                'descriere' => $request->input('descriere'),
                'status' => $request->input('status')]
        );

        return redirect()->route('produse');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function intrari($productId)
    {
        $intrari = DB::table('intrari')->where('produs_id', $productId)->get();
        return view('index_intrari',compact('intrari','productId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createIntrare()
    {
        return view('create_intrare');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeIntrare(Request $request)
    {
        DB::table('intrari')->insert([
                'produs_id' => $request->input('productId'),
                'buc' => $request->input('buc'),
                'created_at' => Carbon::now()->toDateTimeString()]
        );

        //event pt update nr total bucati pe produs
        return redirect()->route('produse');
    }
}
