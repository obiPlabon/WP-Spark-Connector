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
		var getToken = $('.tg-app-connector #spark_app_token').val();

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
	
					// $('.tg-app-connector #spark_app_token').val(response['token']);
					// $('.tg-app-connector #tg_woo_key').val(response['woocommerce_key']);
					// $('.tg-app-connector #tg_woo_secret').val(response['woocommerce_secret']);
	
					$('.tg-app-connector #spark_app_token').attr("readonly", true);
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


	$('.tg-app-connector #spark-build, #wp-admin-bar-tg-connector-build').on('click', function(e){
		e.preventDefault();
		$(this).attr("disabled", true);
		var getToken = $('.tg-app-connector #spark-app-token').val();
		var buildCount = +$('.tg-app-connector #spark-build-count').val();
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
				updateBuildStatus('1');
				setTimeout(function(){
					if(response && data == 'success'){
						buildCount += 1;
						$('.tg-app-connector #spark-build-count').val(buildCount);
						// updateBuildStatus('1');
						// updateDbWithToken(response, getToken);
						console.log('connected');
						$('#build-status .uk-alert-primary').css('display', 'none');
						$('#build-status .uk-alert-success').css('display', 'block');
						$('.tg-app-connector #spark-build').attr("disabled", false);
					}
				}, 50000)
				
            },
            error: function(error, xhr, error_text, statusText) {
				console.log('my error', xhr, 'error text',error_text, 'status text', statusText);
				setTimeout(function(){
					$('#build-status .uk-alert-primary').css('display', 'none');
					$('#build-status .uk-alert-danger').css('display', 'block');
					$('.tg-app-connector #spark-build').attr("disabled", false);
				}, 50000);
				
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
				action: 'spark_get_connector_app_response',
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
		var disconnect = confirm('Are you sure to disconnect ?');
		if(disconnect){
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
		}
		
	});

	$('.check-build-status').on('click', function(e){
		var buildId = $(this).parents('tr').find('.build-id').text();
		var rowClass = $(this).parents('tr').attr('class');
		$.ajax({
			url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'spark_check_build_status',
				buildId: buildId
			},
			success: function(response){
				var data = JSON.parse(response);
				console.log(data);
				// var buildMessage = response.message;
				// var buildStatus = response.status;
				$('.'+ rowClass + '.build-message > span').text(data.message);
				$('.'+ rowClass + '.build-status').text(data.status);
				
				if(data.status == '200'){
					$('.'+ rowClass + '.check-status-button > span').removeClass('uk-alert-primary').addClass('uk-alert-success').text('Success');
				}else if(data.status == '500'){
					$('.'+ rowClass + '.check-status-button > span').removeClass('uk-alert-primary').addClass('uk-alert-danger').text('Build Failed');
				}else{
					$('.'+ rowClass + '.check-status-button span').text('Check Status');
				}

			},
			error: function(error){
				console.log(error.message);
			}
		})
	})


});