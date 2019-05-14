jQuery( document ).ready( function($) {

	$('.tg-app-connector #submit').on('click', function(e){
		e.preventDefault();
		var getToken = $('.tg-app-connector #tg_app_token').val();

		if(! getToken.length){
			alert('Please insert token first');
		}else{
			$.ajax({
				url: 'http://app.wpspark.io/api/v1/build',
				method: 'post',
				data:{
					token: getToken,
					siteUrl: adminUrl.mysiteurl 
				},
				success: function( response,  data, textStatus, xhr ) {
	
					// $('.tg-app-connector #tg_app_token').val(response['token']);
					// $('.tg-app-connector #tg_woo_key').val(response['woocommerce_key']);
					// $('.tg-app-connector #tg_woo_secret').val(response['woocommerce_secret']);
	
					$('.tg-app-connector #tg_app_token').attr("readonly", true);
					$('.tg-app-connector #submit').val('Connected');
					$('.tg-app-connector #submit').attr("disabled", true);
					$('.tg-app-connector #submit').attr("id", "connected").attr("name", "connected");
	
					if(response && data == 'success'){
						updateDbWithToken(response, getToken);
						console.log('connected');
					}
				},
				error: function(error, xhr, error_text, statusText) {
					console.log('response', error);
					console.log('my error', xhr, 'error text',error_text, 'status text', statusText);
					alert('insert a valid token');
				},
				
			});
		}

	});


	$('.tg-app-connector #tgc-build, #wp-admin-bar-tg-connector-build').on('click', function(e){
		e.preventDefault();
		var getToken = $('.tg-app-connector #tg_app_token').val();
		var buildCount = +$('.tg-app-connector #tgc-build-count').val();

		$.ajax({
            url: 'http://app.wpspark.io/api/v1/build',
			method: 'post',
			data:{
				token: getToken,
				siteUrl: adminUrl.mysiteurl 
			},
            success: function( response,  data, textStatus, xhr ) {
				if(response && data == 'success'){
					buildCount += 1;
					$('.tg-app-connector #tgc-build-count').val(buildCount);
					updateBuildStatus('1');
					// updateDbWithToken(response, getToken);
					console.log('connected');
				}
            },
            error: function(error, xhr, error_text, statusText) {
                console.log('my error', xhr, 'error text',error_text, 'status text', statusText);
			},
			
        });

	});
	/**
	 * Send ajax request on success
	 * @param {*} response 
	 */
	function updateDbWithToken(response, token){
		$.ajax({
            url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'get_connector_app_response',
				data: response,
				token: token
			},
		})
	}

	/**
	 * 
	 * @param {build status} $status 
	 */
	function updateBuildStatus($status){
		$.ajax({
            url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'update_build_status',
				data: $status,
			},
		})
	}


});