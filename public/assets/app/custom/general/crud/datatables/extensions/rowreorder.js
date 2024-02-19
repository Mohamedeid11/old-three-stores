
jQuery(document).ready(function() {

    var table = $('#rep_delivery_table');

	// begin first table
	table.DataTable({
		rowReorder: {selector: 'tr'},
        order: [[ 0, 'asc' ]]
	});
	
	table.on( 'row-reordered', function ( e, diff, edit ) {
        var result = 'Reorder started on row: '+edit.triggerRow.data()[1]+'<br>';
 
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = table.row( diff[i].node ).data();
 
            result += rowData[1]+' updated to be in position '+
                diff[i].newData+' (was '+diff[i].oldData+')<br>';
        }
 
        $('#result').html( 'Event result:<br>'+result );
    } );
    
});