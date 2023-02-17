<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PackSizes;

//this is not a good solution and my algorithm simply doesnt have good complexty to deal with large numbers
//to avoid memory leaks, the algorithm must have better complexity
$old = ini_set('memory_limit', '8192M');

$globalPackSizes = array();
$globalPacks = array();
$globalReturnMessage = array();
$globalError = "";

class Pack 
{
	public $overallAmount;
    public $amounts;
    public $widgets;
}

class PackSizeCalculationController extends Controller
{

    public function ReturnBestPackSizes(Request $request) 
    {

        $widgets = $request->widgets;
        $this->globalPackSizes = PackSizes::select('PackSize')->pluck('PackSize')->toArray();

        //perform validation, we need the data in the right format

        $request->validate([
            'widgets' => 'required|numeric|min:2|max:50000'
        ]);

        if (!$this->Validaton($widgets, $this->globalPackSizes))
        {
            return redirect('/')->withErrors($this->globalError);
        }

        $globalBestPacks = [];

        $this->globalReturnMessage[] = "========First Pack Combination========";
        $this->OptimizePacks($this->globalPackSizes, $widgets, $globalBestPacks);

        $amounts = array_reverse($this->globalPackSizes);
        $this->globalPacks = array();
        $this->Change($newList = array(), $amounts, 0, 0, $widgets);
        $this->FindLowestAmounts();

        session()->put('widgets', $widgets);
        return redirect('/')->with('globalPackSizes', $this->globalReturnMessage);
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
    function OptimizePacks($packSizes,$number, $bestPacks)
    {

	    //keep this for later use
	    $target = $number;
	
	    //sort array in decending order
	    $packSizes = array_reverse($packSizes);
	
	    //create default array
	    $requiredPacks = array_fill_keys($packSizes, 0);
	
	    foreach($packSizes as $size)
	    {
		    //divide and round down
		    $packs = floor($number / $size);
		
		    if ($packs > 0) {
                $requiredPacks[$size] = $packs;
                $number -= $packs*$size;
            }
	    }
	
	    //remove the largest element from array for recursion
	    $packSizesTemp = $packSizes;
	    $index = 0;
	
	    //find the index of largest element
	    foreach($requiredPacks as $value)
	    {
		    if ($value > 0)
		    {
			    break;
		    }
		
		    $index++;
	    }

	    //we must counter for numbers lower than the lowest pack size, otherwise, index will be higher than collection
	    if ($index > count($requiredPacks) - 1)
	    {
		    $index--;
	    }
	
	    //if we have reached the lowest pack size index, return
	    $packIndex = array_search(min($packSizes), $packSizes);
	    if ($index != $packIndex)
	    {
		    unset($packSizesTemp[$index]);
		    $packSizesTemp = array_values($packSizesTemp);
		    $packSizes = $packSizesTemp;
	    }
	
	    if ($number > 0)
	    {
		    //find lowest pack and double it to go over widgets
		    $requiredPacks[min($packSizes)]++;
		
		    //now find the sum of all packs of widgets
		    $total = 0;
		    foreach($requiredPacks as $key => $value)
		    {
		 	    if ($value > 0)
		 	    {
		 		    $total += ($key * $value);
		 	    }
		    }
		 
		    //do we already have a single pack which would fufill required widgets with leftovers
		    if (array_key_exists((int)$total, $requiredPacks))
		    {
		 	     //reduce everything below or equal to total to 0
		 	    foreach($requiredPacks as $key => $value)
		 	    {
		 	 	    if ($key <= $total)
		 	 	    {
		 	 		    $requiredPacks[$key] = 0;
                    }
		 	    }
		 	 
		 	    //and add this new pack, i.e. we would rather have less packs of more quantity, than more packs of same quantity
		 	    $requiredPacks[$total]++;
		    }
		 
            //print the results
		 	foreach($requiredPacks as $key => $value)
		 	{
                $this->globalReturnMessage[] = $key . ": " . $value;
		 	}
		 
		    $bestPacks[] = $requiredPacks;
		 
		    //do a bit of a recursion where the highest number is taken out, just to check for other combinations that are lower
            //i.e 800 + 250 to reach 1015 is better than 1000 + 250
         
            if ($index != $packIndex)
            {
                $this->globalReturnMessage[] = "========Other Pack Combination========";
         	
        	    //sort array in ascending order to be reversed again on recursion
			    $packSizes = array_reverse($packSizes);
			    $this->OptimizePacks($packSizes, $target, $bestPacks);
            }
         
            //final result
            else 
            {
         	    $minNumberOfWidgets = INF;
         	
         	    //now find the best pack from the best solutions found based on the wastage of widgets
         	    foreach($bestPacks as $bestPacksVar)
         	    {
         		    $totalNumOfWidgets = 0;
         		
         		    foreach($bestPacksVar as $pk => $pv)
         		    {
         			    $totalNumOfWidgets += ($pk * $pv);
         		    }
         		
         		    if ($totalNumOfWidgets < $minNumberOfWidgets)
         		    {
         			    $minNumberOfWidgets = $totalNumOfWidgets;
                        $bestPack = $bestPacksVar;
         		    }
         	    }
         	
         	    //one final check to see if there is a better solution
                //compare final result with possible shorter packs with other numbers in the orginal collection
            
                $this->globalReturnMessage[] = "========Best Overall Combination========";
                unset($this->packs);
            
			    $amounts = array_reverse($this->globalPackSizes);
                $this->Change($newArray = array(), $amounts, 0, 0, $minNumberOfWidgets);
            
                if (!$this->FindLowestAmounts())
                {
            	    $this->globalReturnMessage[] = "========Best Overall Combination========";
            	    //print the results
            	    $this->globalReturnMessage[] = print_r($bestPack, true);
                }
            }
	    }
    }

    function Change($widgets, $amounts, $highest, $sum, $goal)
    {
	    //see if we are done
	    if ($sum == $goal)
	    {
            $this->Display($widgets, $amounts);
	 	    return;
	    }
	 
	    //see if we have too much
	    if ($sum > $goal)
	    {
	 	    return;
	    }
	 
	    //loop through amounts
	    foreach($amounts as $value)
	    {
	 	    //only add higher or equal amounts
	 	    if ($value >= $highest)
	 	    {
	 		    $copy = $widgets;
	 		    array_push($copy, $value);
	 		    $this->Change($copy, $amounts, $value, $sum + $value, $goal);
	 	    }
	    }
    }

    function Display($widgets, $amounts)
    {
	    //count number of occurances
        //add to lowest set
        //if lower than lowest set
        //this is the new lowest
    
        $pack = new Pack();
        $pack->amounts = $amounts;
        $pack->widgets = $widgets;
    
        $overallCount = 0;
    
        foreach($amounts as $amount)
        {
    	    $countArr = array_count_values($widgets);

		    //check if the countArr contains index, otherwise, it will throw out of bounds error
		    if(isset($countArr[$amount]))
		    {
			    $count = $countArr[$amount];
		    }

		    else
		    {
			    $count = 0;
		    }
		
		    $overallCount += $count;
        }
    
        $pack->overallAmount = $overallCount;
        $this->globalPacks[] = $pack;
        //array_push($this->globalPacks, $pack);
    }

    function FindLowestAmounts()
    {
	    if (isset($this->globalPacks) && sizeof($this->globalPacks) > 0)
	    {
		    $smallestAmount = array_column($this->globalPacks, 'overallAmount');
		    $smallestAmount = min($smallestAmount);
		    $smallestPacks[] = array_column($this->globalPacks, null, 'overallAmount')[$smallestAmount];
		
		    foreach($smallestPacks as $pack)
		    {
			    foreach($pack->amounts as $amount)
			    {
				    $countArr = array_count_values($pack->widgets);

				    //check if the countArr contains index, otherwise, it will throw out of bounds error
				    if(isset($countArr[$amount]))
				    {
					    $count = $countArr[$amount];
				    }

				    else
				    {
					    $count = 0;
				    }
				
                    $this->globalReturnMessage[] = $amount . ": " . $count;
			    }
		    }
		
		    return true;
	    }

	    else
	    {
		    //$this->globalReturnMessage[] = "Cant calcualte to exact amount";
		    return false;
	    }
    }
}
