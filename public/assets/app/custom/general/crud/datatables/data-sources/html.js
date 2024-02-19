$(document).ready(function() {
    $('#kt_table_1').DataTable({"iDisplayLength": 100});
    $('#kt_table_2').DataTable({"iDisplayLength": 100,
        columnDefs: [ {
            orderable: false,
            className: 'checkbox',
            targets:   0
        },
        {
            orderable: true,
            className: '',
            targets:   1,
            type: "numeric"
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        order: [[ 1, 'asc' ]]
    });
    
    $('#kt_table_3').DataTable({"iDisplayLength": 100,
        columnDefs: [ {
            orderable: false,
            className: 'checkbox',
            targets:   0
        },
        {
            orderable: true,
            className: '',
            targets:   1,
            type: "date"
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        order: [[ 1, 'desc' ]]
    });
} );