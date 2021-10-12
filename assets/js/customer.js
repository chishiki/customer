
var customerScript = function() {

	$('.customer-list-autocomplete').autocomplete({

		source: function (request, response) {
			var settings = {
				url: "/api/customer/search/",
				method: "post",
				data: {'customerSearchString':request.term},
				dataType: "json",
				success: function(data) { response(data); }
			};
			$.ajax(settings).always(function(data) { return data; });
		},
		minLength: 2,
		delay: 100,
		select: function (event, ui) {
			$(this).val(ui.item?ui.item.label:"");
			$(this).parent().prev('input').val(ui.item?ui.item.value:0);
			return false;
		},
		change: function(event, ui) {
			$(this).val(ui.item?ui.item.label:"");
			$(this).parent().prev('input').val(ui.item?ui.item.value:0);
		},
		focus: function (event, ui) {
		    event.preventDefault();
		    $(this).val(ui.item?ui.item.label:"");
		    $(this).parent().prev('input').val(ui.item?ui.item.value:0);
		}

	});

	$(document).on('click', '#customerReferenceFormModal a.list-group-item', function (event) {

		var customerID = $(this).data("customerid");
		var customerName = $(this).text();

		$('.btn-customer-modal-trigger').parent().parent().prev('input').val(customerID);
		$('.btn-customer-modal-trigger').parent().prev('input').val(customerName);

		$('#customerReferenceFormModal').modal('hide');

	});


	console.log('customer javascript has loaded');

}

window.addEventListener('load', customerScript, false);
