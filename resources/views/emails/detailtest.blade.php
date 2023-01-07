

    <h3>Total Distance</h3>
    {{"Total distance at drop off : " .$rate_DATA_Email['drop_off_distance']}}<br>
    {{"Fare rate drop off distance : " . $rate_DATA_Email['fare_rate_drop_off_distance']}}<br>
    <br>
    <h3>Total Time</h3>
    {{"Total Time at drop off : " .$rate_DATA_Email['drop_off_time']}}<br>
    {{"Fare rate drop off time : " . $rate_DATA_Email['fare_rate_drop_off_time']}}<br>
    <br>
    <h3>Total Distance Before Pickup</h3>
    {{"Total free Distance before pick up : " . $rate_DATA_Email['free_before_pick_up_total_distance']}}<br>
    {{"Before pick up distance charge : " . $rate_DATA_Email['before_pick_up_distance_charge']}}<br>
    {{"Before pick up total distance rate : " .$rate_DATA_Email['before_pick_up_total_distance_rate']}}<br>
    <br>
    <h3>Total Time Before Pickup</h3>
    {{"Total free Time before pick up : ".$rate_DATA_Email['free_before_pick_up_total_time']}}<br>
    {{"Before pick up total time : " . $rate_DATA_Email['before_pick_up_total_time']}}<br>
    {{"Before pick up time charge : " .$rate_DATA_Email['before_pick_up_time_charge']}}<br>
    {{"Before pick up total time rate : " .$rate_DATA_Email['before_pick_up_total_time_rate']}}<br>
    <br>
    <h3>Total Wait on Arrival</h3>
    {{"Total Wait after Arrival : ".$rate_DATA_Email['wait_after_arrived']}}<br>
    {{"Total Wait Charges : ". $rate_DATA_Email['wait_charges']}}<br>
    <br>
    <h3>Ride Distance and Time Rates</h3>
    {{"Destination final KM rate : ".$rate_DATA_Email['destination_final_KM_rate']}}<br>
    {{"Destination final time rate : ".$rate_DATA_Email['destination_final_time_rate']}}<br>
    <br>
    <h3>Total Base Charge</h3>
    {{"Total base charges : ".$rate_DATA_Email['destination_base_charges']}}<br>
    <br>
    <h3>Total Amount Without Wait</h3>
    {{"Destination total without Pick up and Wait : ".$rate_DATA_Email['destination_total_with_out_pick_up_and_wait']}}<br>
    <br>
    <h3>Total Pickup Charges</h3>
    {{"Destination total pick up : ".$rate_DATA_Email['destination_total_pick_up']}}<br>
    <br>
    <h3>Total Wait Charges</h3>
    {{"Destination total wait : ".$rate_DATA_Email['destination_total_wait']}}<br>
    <br>
    <h3>Sub Total</h3>
    {{"Total bill : ".$rate_DATA_Email['total_bill']}}<br>
    <br>
    <h3>Discount Amount if Voucher/Promo</h3>
    {{"Discount if voucher : ".$rate_DATA_Email['discount_if_voucher']}}<br>
    <br>
    <h3>Total Net Bill</h3>
    {{"Total bill After Discount : ".($rate_DATA_Email['total_bill']-$rate_DATA_Email['discount_if_voucher'])}}<br>


