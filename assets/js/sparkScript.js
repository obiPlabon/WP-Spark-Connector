jQuery( document ).ready( function($) {

	$('#already-has-token').on('click', function(){
		$('#spark_annonymus').css('display', 'none');
		$('#spark_auth_state').css({'display':'block', 'margin-top':'0px'});
	});
	$('.show_resgistration_state').on('click', function(){
		$('#spark_annonymus').css('display', 'flex');
		$('#spark_auth_state').css({'display':'none'});
	});

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
		$(this).attr("disabled", true);
		var getToken = $('.tg-app-connector #spark-app-token').val();
		var buildCount = +$('.tg-app-connector #tgc-build-count').val();
		$('#build-status .uk-alert-primary').css('display', 'block');
		$('#build-status .uk-alert-success').css('display', 'none');

		$.ajax({
            url: 'http://app.wpspark.io/api/v1/build',
			method: 'post',
			data:{
				token: getToken,
				siteUrl: adminUrl.mysiteurl 
			},
            success: function( response,  data, textStatus, xhr ) {
				setTimeout(function(){
					if(response && data == 'success'){
						buildCount += 1;
						$('.tg-app-connector #tgc-build-count').val(buildCount);
						updateBuildStatus('1');
						// updateDbWithToken(response, getToken);
						console.log('connected');
						$('#build-status .uk-alert-primary').css('display', 'none');
						$('#build-status .uk-alert-success').css('display', 'block');
						$('.tg-app-connector #tgc-build').attr("disabled", false);
					}
				}, 5000)
				
            },
            error: function(error, xhr, error_text, statusText) {
				console.log('my error', xhr, 'error text',error_text, 'status text', statusText);
				setTimeout(function(){
					$('#build-status .uk-alert-primary').css('display', 'none');
					$('#build-status .uk-alert-danger').css('display', 'block');
					$('.tg-app-connector #tgc-build').attr("disabled", false);
				}, 5000);
				
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
			success:function(response){
				location.reload();
			},
			error: function(error){
				console.log(error.message);
			}
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
	$('#disconnect_application').on('click', function(e){
		e.preventDefault();
		$.ajax({
            url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'spark_remove_token',
			},
			success: function( response,  data, textStatus, xhr ) {
				location.reload();
            },
            error: function(error, xhr, error_text, statusText) {
				console.log('my error', xhr, 'error text',error_text, 'status text', statusText);
			},
		})
	});


});