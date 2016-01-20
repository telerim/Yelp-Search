/* 
 * 
 */

jQuery(document).ready(function($) {
    var term = '';
    var location = '';
    var currentPage = 1; //TOCHECK
    var scrollXvalue = true;

    if($.cookie('term')){
        term = $.cookie('term');
        $('#input-search-term').val(term);
    }
    if($.cookie('location')){
        location = $.cookie('location');
        $('#input-search-location').val(location);
    }
    
    if(term.length === 0){
        $('#input-search-term').focus();
        $('#input-search-term').tooltip('show');
    }

    var tableResult = $('table.table').DataTable({
        //"scrollX": true,
        "searching": false,
        "pageLength": 20,
        "lengthChange": false,
        "ordering": false,
        "info":     false,
        "processing": true,
        "serverSide": true,
        "dom": 'Bfrtip',
        "buttons": [{ 
            extend: 'csv',
            text: 'Download',
            className: 'btn-danger',
            title: term + '-' + location,
            exportOptions: {
                    columns: [ 0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 14 ]
                }
        }],
        "ajax": {
            "url": "ajax.php",
            "type": "POST",
            "data": {
                "term": '', // don't search on first page load
                "location": location
            }
        },
        "columnDefs": [
            { "visible": false, "targets": 2 },
            { "visible": false, "targets": 12 },
            { "visible": false, "targets": 14 }
        ],
        "columns":[
            {"data":"name"},
            {"data":"phone_display"},
            {"data":"phone"},
            {"data":"address"},
            {"data":"city"},
            {"data":"state"},
            {"data":"zip"},
            //{"data":"category"},
            {"data":"category_1"},
            {"data":"category_2"},
            {"data":"category_3"},
            {"data":"category_others"},
            {"data":"url_link"},
            {"data":"url"},
            {"data":"rating_img"},
            {"data":"rating"}
        ]
    });

    tableResult.buttons().container().appendTo($('#controlPanel'));

    $('#btn-search-submit').click(function(e){
        e.preventDefault();
        currentPage = 1;
        term = $('#input-search-term').val();
        location = $('#input-search-location').val();
        
        term.trim();
        location.trim();
        
        $.cookie('term',term,{ expires: 500 });
        $.cookie('location',location,{ expires: 500 });
        
        if(term.length === 0){
            $('#input-search-term').focus();
            $('#input-search-term').tooltip('show');
            return;
        }
        
        tableResult.destroy();
        $('table.table tbody').empty();
        tableResult = $('table.table').on('xhr.dt', function ( e, settings, json, xhr ){
        if(typeof(json.dataError) !== 'undefined'){
            
            $('#error-message').html(json.dataError.message);
            $('.alert').removeClass('hidden');
            $('.alert').fadeIn(200);
        }
        
        if(json.data.length === 0){
            
            scrollXvalue = false;
            console.log(scrollXvalue);
        }
        // get error data and do something about it
        if(xhr.status !== 200){
            
            $('#error-message').text('Opps! Data cannot be loaded. Please try again.');
            $('.alert').removeClass('hidden');
            $('.alert').fadeIn(200);
        }
    }).DataTable({
            "scrollX": scrollXvalue,
            "searching": false,
            "pageLength": 20,
            "lengthChange": false,
            "ordering": false,
            "info":     false,
            "processing": true,
            "serverSide": true,
            "dom": 'Bfrtip',
            "buttons": [{
                extend: 'csv',
                text: 'Download',
                className: 'btn-danger',
                title: term + '-' + location,
                exportOptions: {
                    columns: [ 0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 14 ]
                }
            }],
            "ajax": {
                "url": "ajax.php",
                "type": "POST",
                "data": {
                    "term": term,
                    "location": location
                }
            },
            "columnDefs": [
                { "visible": false, "targets": 2 },
                { "visible": false, "targets": 12 },
                { "visible": false, "targets": 14 }
            ],
            "columns":[
                {"data":"name"},
                {"data":"phone_display"},
                {"data":"phone"},
                {"data":"address"},
                {"data":"city"},
                {"data":"state"},
                {"data":"zip"},
                //{"data":"category"},
                {"data":"category_1"},
                {"data":"category_2"},
                {"data":"category_3"},
                {"data":"category_others"},
                {"data":"url_link"},
                {"data":"url"},
                {"data":"rating_img"},
                {"data":"rating"}
            ]
        });
        
        

        tableResult.buttons().container().appendTo($('#controlPanel'));
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    $('.alert .close').click(function () {
        //$('.alert').addClass('hidden');
        $('.alert').fadeOut(200);
    });
    
    $('table.table').on( 'page.dt', function () {
        var pageInfo = tableResult.page.info();
        currentPage = pageInfo.page + 1;
        console.log(tableResult.buttons(0,null));
       
    });
});
