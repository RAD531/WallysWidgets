<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackSizes;

class PackController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('packs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'packSize' => 'required|numeric|min:2'
        ]);

        PackSizes::create([
            'packSize' => $request->input('packSize')
        ]);
        
        return redirect()
            ->route('home')
            ->with('success', 'Pack has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PackSizes $pack)
    {
        return view('packs.show', [
            'packSize' => $pack,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PackSizes $pack)
    {
        return view('packs.edit', [
            'packSize' => $pack,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PackSizes $pack)
    {
        $request->validate([
            'packSize' => 'required|numeric|min:2'
        ]);

        $packToUpdate = PackSizes::find($pack->id);
        $packToUpdate->packSize = $request->packSize;
        $packToUpdate->save();


        if($pack->update()) {
            return redirect('/')->with('success', 'Pack Size has been updated!');
          } else {
            return redirect('/')->withErrors('Update failed');
          }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PackSizes $pack)
    {
        if($pack->delete()) {
            return redirect('/')->with('success', 'Pack Size has been deleted!');
          } else {
            return redirect('/')->withErrors('Delete failed');
          }
    }
}
