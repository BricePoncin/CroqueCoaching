/* JAVASCRIPT CONTRACTS 2 */
	function affiche_masque_villes(AffMasq)
	{
		var myTable = document.getElementById('aide_contrats');
        var tab_tr = myTable.getElementsByClassName('ville_a_points');
		var lg_tab_tr = tab_tr.length;

		for (var i=0; i<lg_tab_tr; i++) 
		{
			if ( AffMasq == "affiche")
			{
				tab_tr[i].style.display = "table-row";
			}
			else
			{
				tab_tr[i].style.display = "none";
			}
		}
	}


		$.fn.dataTable.ext.search.push(
			function( settings, data, dataIndex ) {
				var ret = true;
				var inferno = data[3].substring( 5, 10 ); // use data for the timezone column
				
				var flt_reussite = $('input#filtre_reussite').val();
				var flt_gain = $('input#filtre_gain').val();
				var flt_inferno = $('input#filtre_inferno').val();
				var flt_min = $('select#fuseau_min').val();
				var flt_max = $('select#fuseau_max').val();
				
				var fuseau = data[2].substring( 5, 10 ); // use data for the timezone column
				var reussite = 0;
				var gain = -6666;
				var inferno = data[3];
				
				for (var i = 4; i<=13; i++)
				{
					// GAIN:-392;REU:100;
					var deb = data[i].indexOf("GAIN:")+5;
					var fin = data[i].indexOf(";", deb);
					gain = max( gain, parseInt(data[i].substr(deb, fin) ) );
					
					var deb = data[i].indexOf("REU:")+4;
					var fin = data[i].indexOf(";", deb);
					reussite = max( reussite, parseInt(data[i].substr(deb, fin) ) );
					//console.log( gain+' '+reussite+' '+data[i] );
				}

				if ( flt_min > fuseau || fuseau > flt_max )
				{
					ret = false;
				}

				if ( flt_gain > gain )
				{
					ret = false;
				}

				if ( flt_reussite > reussite )
				{
					ret = false;
				}
				
				if ( flt_inferno > inferno )
				{
					ret = false;
				}
				
				return ret;
			}
		);

        $(document).ready(function () {
			// DataTable
			var datatableInstance = $('#aide_contrats').DataTable({
				'sortable': true,
				'searchable': false,
				'paging': false,
				'scrollX': false,
				'scrollY': 450
			});
		
		$("select#fuseau_max").val("24:59");
		datatableInstance.draw();
		
			// Apply the search
			// Event listener to the two range filtering inputs to redraw on input
			$('select#fuseau_min, select#fuseau_max, input#filtre_reussite, input#filtre_gain, input#filtre_inferno').change(function() {
//				console.log($(this).val());
				datatableInstance.draw();
			} );
        });
