$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   const table = $('#data-table').DataTable(
        
    );

    $('#filterForm').on('submit', function(e){
    e.preventDefault();
     const company_filter =  $('#company_filter').val();
    const type_filter =  $('#type_filter').val();
    const cust_id =  $('#cust_id').val();
    const from_date =  $('#start_date_earning').val();
    const to_date =  $('#end_date_earning').val();
    var _token=$('input[name=_token]').val();
    if (type_filter != '' && cust_id != '' &&  from_date != '' && to_date !=''){
        $.ajax({
            type: 'POST',
            url: APP_URL + '/getLedgerReport',
            data:{_token:_token,type_filter:type_filter,cust_id:cust_id,from_date:from_date,to_date:to_date,company_filter:company_filter},
            success: function (data) {
                $('#account_no').show();
                 $('#acc_name').html(data.acc_name);
                 $('#acc_no').html(data.acc_no);
                 
                $('#data-table').DataTable().draw(true);
               
            }, error: function (data) {
                
            }
        });
    }
});


    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id');

        swal({
            title: 'Destroy Default Record?',
            text: 'Are you sure you want to permanently remove this record?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                deleteRecord(value_id)
            }else{
                table.draw();
            }
        });
    });
    function deleteRecord(value_id) {

        $.ajax({
            type: 'DELETE',
            url: APP_URL + '/ledgerReport' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw();
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
});

function getPassengerORDriver(type){
    var _token=$('input[name=_token]').val();
     const company_filter =  $('#company_filter').val();
    $.ajax({
        type: "POST",
        url:  APP_URL + '/getPassengerORDriver',
        data:{_token:_token,type:type,company_filter:company_filter},
        beforeSend: function(){
            // $("#preloader").css("display","block");
        },success: function(data){
            // $("#preloader").css("display","none");
            $('#cust_id').html(data);
        }
    });
}

function getPassengerORDriverByCompany(company_filter){
    var _token=$('input[name=_token]').val();
     const type_filter =  $('#type_filter').val();
     if(type_filter != ''){
    $.ajax({
        type: "POST",
        url:  APP_URL + '/getPassengerORDriver',
        data:{_token:_token,type:type_filter,company_filter:company_filter},
        beforeSend: function(){
            // $("#preloader").css("display","block");
        },success: function(data){
            // $("#preloader").css("display","none");
            $('#cust_id').html(data);
        }
    });
}
}

