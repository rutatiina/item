/**
 * Created by t on 9/9/2017.
 */
rg_items = function () {

    var datatable = function () {

        var dtable = $('.rg-datatable').DataTable({
            buttons: {
                dom: {
                    button: {
                        className: 'btn btn-default'
                    }
                },
                buttons: [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-default btn-icon',
                        text: '<i class="icon-copy3"></i>'
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-default btn-icon',
                        text: '<i class="icon-file-excel"></i>'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-default btn-icon',
                        text: '<i class="icon-file-pdf"></i>'
                    }
                ]
            },
            pagingType: "simple",
            language: {
                paginate: {'next': 'Next &rarr;', 'previous': '&larr; Prev'}
            },
            iDisplayLength: 20,
            aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            columnDefs: [
                {
                    'targets': 0,
                    "orderable": false,
                    'checkboxes': {
                        'selectRow': true,
                        'selectCallback': function(nodes, selected, indeterminate) {
                            //nodes: [Array] List of cell nodes td containing checkboxes.
                            //selected: [Boolean]  Flag indicating whether checkbox has been checked.
                            //indeterminate: [Boolean] Flag indicating whether “Select all” checkbox has indeterminate state.
                            //console.log(nodes);
                            //console.log(selected);

                            var rows_selected = nodes.column(0).checkboxes.selected().length;
                            if (rows_selected > 0) {
                                $('.rg_datatable_onselect_btns').show();
                                $('.page-header').hide();
                            } else {
                                $('.rg_datatable_onselect_btns').hide();
                                $('.page-header').show();
                            }
                        }
                    },
                },
                {
                    'targets': [0,7],
                    "orderable": false
                }
            ],
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            order: [[0, false]],
            processing: true,
            serverSide: true,
            ordering: false,
            aoColumns: [
                { "mDataProp": 'id' },
                { "mDataProp": null, "sClass": "text-center pl-5"},
                { "mDataProp": "type", "sClass": "" },
                { "mDataProp": "name", "sClass": "" },
                { "mDataProp": "sku", "sClass": "text-right" },
                { "mDataProp": "selling_rate", "sClass": "text-right" },
                { "mDataProp": 'billing_rate', "sClass": "text-right" },
                { "mDataProp": 'selling_description', "sClass": "" },
                { "mDataProp": 'status', "sClass": "" }
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                $('td:eq(2)', nRow).html('<span class="label label-default">'+aData.type+'</span>');
                $('td:eq(5)', nRow).html(rg_number_format(aData.selling_rate)+' '+aData.selling_currency);
                $('td:eq(6)', nRow).html(rg_number_format(aData.billing_rate)+' '+aData.billing_currency);

                var a = '';

                if (aData.status === 'deactivated') {
                    $(nRow).addClass('danger');

                    $('td:eq(8)', nRow).html('<span class="label label-danger">'+aData.status.toUpperCase()+'</span>');

                        //<li><a href="'+APP_URL+'/items/'+aData.id+'/activate" title="Activate item" class="rg_datatable_row_deactivate"><i class="icon-checkbox-checked2"></i></a></li>\
                        //<li><a href="'+APP_URL+'/items/'+aData.id+'/delete" title="Delete item" class="rg_datatable_row_delete text-danger"><i class="icon-bin"></i></a></li>\

                    a = '\
                    <ul class="icons-list">\
                        <li><a href="'+APP_URL+'/items/'+aData.id+'/edit" title="Edit item"><i class="icon-pencil7"></i></li>\
                    </ul>';

                } else {
                    $('td:eq(8)', nRow).html('<span class="label label-success">'+aData.status.toUpperCase()+'</span>');

                        //<li><a href="'+APP_URL+'/items/'+aData.id+'/deactivate" title="Deactivate item" class="rg_datatable_row_deactivate"><i class="icon-switch2"></i></a></li>\
                        //<li><a href="'+APP_URL+'/items/'+aData.id+'/delete" title="Delete item" class="rg_datatable_row_delete text-danger"><i class="icon-bin"></i></a></li>\

                    a = '\
                    <ul class="icons-list">\
                        <li><a href="'+APP_URL+'/items/'+aData.id+'/edit" title="Edit item"><i class="icon-pencil7"></i></li>\
                    </ul>';
                }

                $('td:eq(1)', nRow).html(a);

            }
        });

        // Datatable Search
        $('#navbar_top_search').keypress(function(e){
            if(e.which == 13) {
                e.preventDefault();
                dtable.search($(this).val()).draw() ;
            }
        });
        
        $('.rg_datatable_selected_delete, .rg_datatable_row_delete').on('click', function (ev) {

            ev.stopPropagation();
            ev.preventDefault();

            var ids = [];
            var url = (rg_empty($(this).data('url')) ? $(this).attr('href') : $(this).data('url') );

            //console.log(url);

            var rows_selected = dtable.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                ids[index] = rowId;
            });

            var ajaxData = {
                ids: ids,
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'post'
            };

            //console.log(ids);

            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the item(s)!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF5350",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel pls!",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                },
                function (isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: ajaxData,
                            dataType: "json",
                            success: function(response, status, xhr, $form) {

                                //Update the cross dite tocken
                                //form.find('[name=ci_csrf_token]').val(Cookies.get('ci_csrf_token'));

                                if (response.status === true) {
                                    swal({
                                        title: "Deleted!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "success",
                                        timer: 2000
                                    });

                                    //delete the row
									if (!rg_empty(response.ids)) {
                                        $.each(response.ids, function(index, id) {
                                            dtable.row( $('tr#row_'+id) ).remove().draw();
                                        });
									}

                                    $('.rg_datatable_onselect_btns').slideUp(100);

                                } else {
                                    swal({
                                        title: "Failed!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "danger",
                                        timer: 2000
                                    });
                                }

                            }
                        });

                    }
                });
        });

        $('.rg_datatable_selected_deactivate, .rg_datatable_row_deactivate').on('click', function (ev) {

            ev.stopPropagation();
            ev.preventDefault();

            var ids = [];
            var url = (rg_empty($(this).data('url')) ? $(this).attr('href') : $(this).data('url') );

            //console.log(url);

            var rows_selected = dtable.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                ids[index] = rowId;
            });

            var ajaxData = {
                ids: ids,
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'post'
            };

            //console.log(ids);

            swal({
                    title: "Are you sure?",
                    text: "You want to deactivate item(s)!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF5350",
                    confirmButtonText: "Yes, deactivate it!",
                    cancelButtonText: "No, cancel pls!",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                },
                function (isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: ajaxData,
                            dataType: "json",
                            success: function(response, status, xhr, $form) {

                                //Update the cross dite tocken
                                //form.find('[name=ci_csrf_token]').val(Cookies.get('ci_csrf_token'));

                                if (response.status === true) {
                                    swal({
                                        title: "Deactivated!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "success",
                                        timer: 2000
                                    });

                                    location.reload(); //SO that table is redrawn with correct status

                                } else {
                                    swal({
                                        title: "Failed!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "error",
                                        timer: 2000
                                    });
                                }

                            }
                        });

                    }
                });
        });

        $('.rg_datatable_selected_activate, .rg_datatable_row_activate').on('click', function (ev) {

            ev.stopPropagation();
            ev.preventDefault();

            var ids = [];
            var url = (rg_empty($(this).data('url')) ? $(this).attr('href') : $(this).data('url') );

            //console.log(url);

            var rows_selected = dtable.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                ids[index] = rowId;
            });

            var ajaxData = {
                ids: ids,
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'post'
            };

            //console.log(ids);

            swal({
                    title: "Are you sure?",
                    text: "Do you want to activate item(s)!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF5350",
                    confirmButtonText: "Yes, activate it!",
                    cancelButtonText: "No, cancel pls!",
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                },
                function (isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: ajaxData,
                            dataType: "json",
                            success: function(response, status, xhr, $form) {

                                //Update the cross dite tocken
                                //form.find('[name=ci_csrf_token]').val(Cookies.get('ci_csrf_token'));

                                if (response.status === true) {
                                    swal({
                                        title: "Activated!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "success",
                                        timer: 2000
                                    });

                                    location.reload(); //SO that table is redrawn with correct status

                                } else {
                                    swal({
                                        title: "Failed!",
                                        text: response.message,
                                        confirmButtonColor: "#66BB6A",
                                        type: "error",
                                        timer: 2000
                                    });
                                }

                            }
                        });

                    }
                });
        });

        return dtable;

    };

    return {
        // public functions
        init: function() {
        	//Nothing here
        },
        datatable: function() {
            datatable();
        }
    };
}();

jQuery(document).ready(function () {

    // Table setup
    // ------------------------------

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        columnDefs: [{
            orderable: false,
            width: '100px',
            targets: [ 5 ]
        }],
        //dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        dom: '<"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            search: '_INPUT_', //'<span>Filter:</span> _INPUT_',
            searchPlaceholder: 'Type to search ...',
            lengthMenu: '<span>Show:</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });

    try {
        rg_items.datatable();
    } catch (e) {
        console.log(e);
    }

});
