/* ------------------------------------------------------------------------------
*
*  # Buttons extension for Datatables. Print examples
*
*  Specific JS code additions for datatable_extension_buttons_print.html page
*
*  Version: 1.1
*  Latest update: Mar 6, 2016
*
* ---------------------------------------------------------------------------- */

$(function() {


    // 
   // Table setup
    // ------------------------------

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filter:</span> _INPUT_',
            lengthMenu: '<span>Show:</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        }
    });


    // Basic initialization
    $('.datatable-button-print-basic').DataTable({
        buttons: [
            {
                extend: 'print',
                text: '<i class="icon-printer position-left"></i> Print table',
                className: 'btn bg-blue'
            }
        ]
    });


    // Disable auto print
    $('.datatable-button-print-disable').DataTable({
        buttons: [
            {
                extend: 'print',
                text: '<i class="icon-printer position-left"></i> Print table',
                className: 'btn bg-blue',
                autoPrint: false
            }
        ]
    });


    // Export options - column selector
    $('.datatable-button-print-columns').DataTable({
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 6 ).footer() ).html(
                'Rs :'+pageTotal +' ( Rs: '+ total +' total)'
            );
        },
        columnDefs: [{
          //  targets: 1, // Hide actions column
           // visible: false
        }],
        buttons: [
            {
                extend: 'print',
                text: '<i class="icon-printer position-left"></i> Print ',
                className: 'btn bg-blue',
                exportOptions: {
                    columns: ':visible'
                }
            },
			{
                extend: 'excelHtml5',
				text: '<i class="icon-file-excel position-left"></i> Excel ',
                className: 'btn bg-violet',
                exportOptions: {
                    columns: ':visible'
                   }
            },
		   {
				extend: 'copyHtml5',
				text: '<i class="icon-copy2 position-left"></i> Copy ',
				className: 'btn bg-green',
				exportOptions: {
					columns: ':visible'
				}
			},
			{
				extend: 'pdfHtml5',
				text: '<i class=" icon-file-pdf position-left"></i> Pdf ',
				className: 'btn bg-orange',
				exportOptions: {
					columns: ':visible'
				}
			},

            {
                extend: 'colvis',
                text: '<i class="icon-three-bars"></i> <span class="caret"></span>',
                className: 'btn btn-default btn-icon'
            }
        ]
    });

    $('.datatable-button-print-columns1').DataTable({
        
        columnDefs: [{
            //  targets: 1, // Hide actions column
             // visible: false
          }],
          buttons: [
              {
                  extend: 'print',
                  text: '<i class="icon-printer position-left"></i> Print ',
                  className: 'btn bg-blue',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'excelHtml5',
                  text: '<i class="icon-file-excel position-left"></i> Excel ',
                  className: 'btn bg-violet',
                  exportOptions: {
                      columns: ':visible'
                     }
              },
             {
                  extend: 'copyHtml5',
                  text: '<i class="icon-copy2 position-left"></i> Copy ',
                  className: 'btn bg-green',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  text: '<i class=" icon-file-pdf position-left"></i> Pdf ',
                  className: 'btn bg-orange',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
  
              {
                  extend: 'colvis',
                  text: '<i class="icon-three-bars"></i> <span class="caret"></span>',
                  className: 'btn btn-default btn-icon'
              }
          ]

    });
    // Export options - row selector
    $('.datatable-button-print-rows').DataTable({
        buttons: {
            buttons: [
                {
                    extend: 'print',
                    className: 'btn btn-default',
                    text: '<i class="icon-printer position-left"></i> Print all'
                },
                {
                    extend: 'print',
                    className: 'btn btn-default',
                    text: '<i class="icon-checkmark3 position-left"></i> Print selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    }
                }
            ],
        },
        select: true
    });



    // External table additions
    // ------------------------------

    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder','Type to comman filter...');


    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
    
});
