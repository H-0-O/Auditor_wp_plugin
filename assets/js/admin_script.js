(function($) {
    $(".show_details").click(function(){
        let changes_id = $(this).attr('data-id');
        let type = $(this).attr('data-type');
        $.ajax({
            url: ajaxObject.url ,
            data: {
                action: 'get_json',
                changes_id ,
                type
            },
            type: 'POST' ,
            success: (respond)=>{
                console.log(respond);
                if(respond != null)
                {
                    $("#detail .modal-table tbody").html('');
                    $("#detail .modal-table tbody").append(respond);
                }
            }
        })
    });

})(jQuery)

