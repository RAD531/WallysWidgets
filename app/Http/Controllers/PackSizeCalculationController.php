<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PackSizes;

//this is not a good solution and my algorithm simply doesnt have good complexty to deal with large numbers
//to avoid memory leaks, the algorithm must have better complexity
$old = ini_set('memory_limit', '8192M');

//store error messages
$globalError = "";

class PackSizeCalculationController extends Controller
{

    public function ReturnBestPackSizes(Request $request) 
    {

        $widgets = $request->widgets;
        $pack_sizes = PackSizes::select('PackSize')->pluck('PackSize')->toArray();

        //perform validation, we need the data in the right format

        $request->validate([
            'widgets' => 'required|numeric|min:2|max:1000000'
        ]);

        if (!$this->Validaton($widgets, $pack_sizes))
        {
            return redirect('/')->withErrors($this->globalError);
        }

		$packs_to_send = $this->get_packs($widgets, $pack_sizes);

        session()->put('widgets', $widgets);
        return redirect('/')->with('globalPackSizes', $packs_to_send);
    }

    function Validaton($widgets, $globalPackSizes)
    {
        if (!isset($globalPackSizes) || sizeof($globalPackSizes) < 2)
        {
            $this->globalError = "Please add at least two packs";
            return false;
        }

        return true;
    }

    //===============
    //PRIVATE METHODS
    //===============

	function get_packs($order_size, $pack_sizes) 
	{

    	//sort array in decending order
		rsort($pack_sizes);

		//create default array
		$packs_to_send = array_fill_keys($pack_sizes, 0);

    	$totalInPacks = 0;

    	// Iterate through the pack sizes and try to use as many packs as possible
    	foreach ($pack_sizes as $pack_size) 
    	{
        	if ($order_size >= $pack_size) 
        	{
            	$num_packs = floor($order_size / $pack_size);
            	$packs_to_send[$pack_size] = $num_packs;
            	$order_size -= $num_packs * $pack_size;
            	$totalInPacks += $num_packs * $pack_size;
        	}
    	}

    	// If there is still some order left to fulfill, it means we couldn't use any of the larger packs
    	// and we need to use smaller packs
    	if ($order_size > 0) 
    	{

			//find lowest pack and double it to go over widgets
			$packs_to_send[min($pack_sizes)]++;

        	//update total pack size also
        	$totalInPacks += $pack_size;

        	$packs_to_send = $this->find_minimum_count($packs_to_send, $totalInPacks);
    	}

    	return $packs_to_send;
	}

	function find_minimum_count($pack_sizes, $target) 
	{

    	$numbers = array_keys($pack_sizes);
    	sort($numbers);

    	// Initialize a list to keep track of the minimum count of numbers to reach each sum up to the target
    	$dp = array_fill(0, $target + 1, INF);
    	$dp[0] = 0; // It takes 0 numbers to reach a sum of 0

    	// Initialize a list to keep track of the numbers used to reach each sum up to the target
    	$used_nums = array_fill(0, $target + 1, []);

    	for ($i = 1; $i <= $target; $i++) 
    	{
        	foreach ($numbers as $num) 
        	{
            	if ($num <= $i && $dp[$i - $num] + 1 < $dp[$i]) 
            	{
                	// If the current number can be used to reach the current sum and results in a smaller count, update the minimum count and the used numbers list
                	$dp[$i] = $dp[$i - $num] + 1;
                	$used_nums[$i] = array_merge($used_nums[$i - $num], [$num]);
            	}
        	}
    	}

    	if ($dp[$target] == INF) 
    	{
        	return [-1, []];
    	} 
    
    	else 
    	{

        	//unset all values for each key
        	foreach($pack_sizes as $key => $value)
        	{
            	$pack_sizes[$key] = 0;
        	}

        	foreach($used_nums[$target] as $pack_size)
        	{
            	if (array_key_exists((int)$pack_size, $pack_sizes))
            	{
                	$pack_sizes[$pack_size] += 1;
            	}
        	}

        	return $pack_sizes;
    	}
	}   
}