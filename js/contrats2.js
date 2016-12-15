/* JAVASCRIPT CONTRACTS 2 */
		$.fn.dataTable.ext.search.push(
			function( settings, data, dataIndex ) {
				var min = $('#min option:selected').val();
				var max = $('#max option:selected').val();
				var fuseau = data[2]; // use data for the timezone column
		 alert(min+' '+max+' '+fuseau);
				if ( ( isNaN( min )  && isNaN( max ) )  ||
					 ( isNaN( min )  && fuseau <= max ) ||
					 ( min <= fuseau && isNaN( max ) )  ||
					 ( min <= fuseau && fuseau <= max ) )
				{
					return true;
				}
				return false;
			}
		);

        $(document).ready(function () {
			// Setup - add a text input to each footer cell
			$('#aide_contrats tfoot th').each(function () {
				if ( $(this).index() == 2 )
				{
					var title = $(this).text();
					//$(this).html('<input type="text" placeholder="Search ' + title + '"/>');
					var selMin = '<select id="min" name="min" placeholder="Search ' + title + '" >';
					var selMax = '<select id="max" name="max" placeholder="Search ' + title + '" >';
					
					for( var i=0; i<=24; i++)
					{						
						if(i < 10)
							num = '0'+i;
						else
							num = i;
						
						selMin = selMin+'<option value="('+num+':00)">H+'+num+'</option>';
						selMax = selMax+'<option value="('+num+':00)">H+'+num+'</option>';
					}
					selMin = selMin+'</select>';
					selMax = selMax+'</select>';
					
					$(this).html('Entre '+selMin+' et '+selMax );
				}
			});
			
			// DataTable
			var datatableInstance = $('#aide_contrats').DataTable({
				'sortable': true,
				'searchable': false,
				'paging': false,
				'scrollX': false,
				'scrollY': 450
			});
			
			// Apply the search
			// Event listener to the two range filtering inputs to redraw on input
			$('#min, #max').change(function() {
				console.log($(this).val());
				datatableInstance.draw();
			} );
			
			/*
			datatableInstance.columns().every( function () {
				var that = this;
				
				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
				
			} );
			*/
			$('.showHideColumn').on('click', function () {
				var tableColumn = datatableInstance.column($(this).attr('data-columnindex'));
				tableColumn.visible(!tableColumn.visible());
			});
        });
