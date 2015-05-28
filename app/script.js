
sendForms = function(){
	$('#last').empty();
	$('form').each(function(){sendOrders($('#match').val(), $('#empire').val(), $( this ).find('.invisible span').text())} );
}

sendOrders = function(matchId, empireId, orderText){
	var req = server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/orders?order_str='+escape(jQuery.trim(orderText));
	console.debug(req);
	jQuery.get(req, function( response ) {
		console.debug( response );
		$('#last').append(response.data.msg + '<br>');
	});
}

// vim: noet sts=0 sw=4 ts=4 :
