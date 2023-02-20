<?php

namespace App\Http\Controllers;

use App\Actions\CalculatePacks;
use Illuminate\Http\Request;
use App\Models\PackSizes;
use Illuminate\Validation\ValidationException;

//this is not a good solution and my algorithm simply doesnt have good complexty to deal with large numbers
//to avoid memory leaks, the algorithm must have better complexity
$old = ini_set('memory_limit', '8192M');

//store error messages
$globalError = "";

class PackSizeCalculationController extends Controller
{
    public function ReturnBestPackSizes(Request $request, CalculatePacks $calulator)
    {
        $widgets = $request->widgets;
        $packSizes = $this->getPackSizes();

        //perform validation, we need the data in the right format
        $request->validate([
            'widgets' => 'required|numeric|min:2|max:1000000'
        ]);

        $packs_to_send = $calulator->getPacks($widgets, $packSizes);

        session()->put('widgets', $widgets);
        return redirect('/')->with('globalPackSizes', $packs_to_send);
    }

    /**
     * Returns available pack sizes.
     *
     * @return array
     */
    private function getPackSizes(): array
    {
        $sizes = PackSizes::select('PackSize')->pluck('PackSize');

        if (2 > $sizes->count()) {
            throw ValidationException::withMessages([
                'pack_sizes' => ['Please add at least two pack sizes.'],
            ]);
        }

        return $sizes->toArray();
    }
}
