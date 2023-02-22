<?php

namespace App\Actions;

class CalculatePacks
{
    /**
     * Calculates Packs Required.
     *
     * @param int $order_size
     * @param array $pack_sizes
     *
     * @return array
     */
    public function getPacks(int $order_size, array $pack_sizes): array
    {
        //sort array in decending order
        rsort($pack_sizes);

        //create default array
        $packs_to_send = array_fill_keys($pack_sizes, 0);

        $totalInPacks = 0;

        // Iterate through the pack sizes and try to use as many packs as possible
        foreach ($pack_sizes as $pack_size) {
            if ($order_size >= $pack_size) {
                $num_packs = floor($order_size / $pack_size);
                $packs_to_send[$pack_size] = $num_packs;
                $order_size -= $num_packs * $pack_size;
                $totalInPacks += $num_packs * $pack_size;
            }
        }

        // If there is still some order left to fulfill, it means we couldn't use any of the larger packs
        // and we need to use smaller packs
        if ($order_size > 0) {
            //find lowest pack and double it to go over widgets
            $packs_to_send[min($pack_sizes)]++;

            //update total pack size also
            $totalInPacks += $pack_size;

            $packs_to_send = $this->find_minimum_count($packs_to_send, $totalInPacks);
        }

        return $packs_to_send;
    }

    private function find_minimum_count($pack_sizes, $target): array
    {
        $numbers = array_keys($pack_sizes);
        sort($numbers);

        // Initialize a list to keep track of the minimum count of numbers to reach each sum up to the target
        $dp = array_fill(0, $target + 1, INF);
        $dp[0] = 0; // It takes 0 numbers to reach a sum of 0

        // Initialize a list to keep track of the numbers used to reach each sum up to the target
        $used_nums = array_fill(0, $target + 1, []);

        for ($i = 1; $i <= $target; $i++) {
            foreach ($numbers as $num) {
                if ($num <= $i && $dp[$i - $num] + 1 < $dp[$i]) {
                    // If the current number can be used to reach the current sum and results in a smaller count, update the minimum count and the used numbers list
                    $dp[$i] = $dp[$i - $num] + 1;
                    $used_nums[$i] = array_merge($used_nums[$i - $num], [$num]);
                }
            }
        }

        if ($dp[$target] == INF) {
            return [-1, []];
        } else {
            //unset all values for each key
            foreach ($pack_sizes as $key => $value) {
                $pack_sizes[$key] = 0;
            }

            foreach ($used_nums[$target] as $pack_size) {
                if (array_key_exists((int)$pack_size, $pack_sizes)) {
                    $pack_sizes[$pack_size] += 1;
                }
            }

            return $pack_sizes;
        }
    }
}
