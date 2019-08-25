jQuery( document ).ready( function($) {
	
	"use strict";

	$('#already-has-token').on('click', function(){
		$('#spark_annonymus').css('display', 'none');
		$('#spark_auth_state').css({'display':'block', 'margin-top':'0px'});
	});
	$('.show_resgistration_state').on('click', function(){
		$('#spark_annonymus').css('display', 'flex');
		$('#spark_auth_state').css({'display':'none'});
	});

	/**
	 * Registration with email field
	 * Email field checklist for registration
	 * 1. check if field is empty
	 * 2. check valid email
	 * 3. then send email to https://app.wpspark.io/register end point
	 * 4. if success alert user to check the mail
	 */
	$('#register-input').on('click', function(e){
		e.preventDefault();
		if($(this).hasClass('register-new-user')) {
			var email = $('#email-for-register .uk-input').val();
			var sanitizeEmail = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
			if(! email.length){
				$('#email-for-register .alert-text').remove();
				$('#email-for-register').prepend('<p class="alert-text uk-text-danger">Please write a email address</p>');
			}else{
				$('#email-for-register .alert-text').remove();
				if( !sanitizeEmail.test(email)){
					$('#email-for-register').prepend('<p class="alert-text uk-text-warning">Please enter a value email address</p>');
				}else{
					$.ajax({
						url: 'https://app.wpspark.io/api/v1/register', 
						method:'post',
						data:{
							name:email,
							email:email,
							domain:adminUrl.mysiteurl,
							security: adminUrl.ajax_nonce
						},
						beforeSend: function(){
							$('a.register-new-user').html('<img src='+ adminUrl.gifurl +' />');
						},
						success:function(response, data, xhr, textStatus){
							console.log(response, data);
							$('a.register-new-user').text('Success');
							$('#email-for-register').prepend('<p class="alert-text uk-text-success">Check your mail</p>');
						}, 
						error:function(error, jqXHR, xhr, error_text, textStatus, response){
							console.log(error.status, response, jqXHR, xhr, ' error text - ', error_text, ' Status - ', textStatus, ' message -', error.message);
							$('a.register-new-user').text('Register');
							if(error.status === 422){
								$('#email-for-register').prepend('<p class="alert-text uk-text-danger">Email Already Exist <a target="blank" href="https://app.wpspark.io/login">Login for token</a></p>');
							}else{
								$('#email-for-register').prepend('<p class="alert-text uk-text-danger">Something Went Wrong</p>');
							}
						}
					})
				}
			}
		}else{
			$('#email-for-register').css('display', 'block');
			$(this).addClass('register-new-user').text('Register');
		}

	});

	/**
	 * validate token for build
	 * for the first time after login to spark wp admin panel
	 * no_build: false dile token check korbe. ar no_build er value na dile ftp
	 */
	$('.tg-app-connector #submit').on('click', function(e){
		e.preventDefault();
		var getToken = $('.tg-app-connector #spark_app_token').val();
		if(! getToken.length){
			alert('Please insert token first');
		}else{
			$.ajax({
				url: 'https://app.wpspark.io/api/v1/build',
				method: 'post',
				data:{
					token: getToken,
					domain: adminUrl.mysiteurl,
					no_build: true,
					security: adminUrl.ajax_nonce
				},
				beforeSend: function(){
					$('.tg-app-connector #submit').val('Connecting');
				},
				success: function( response,  data, textStatus, xhr ) {
					$('.tg-app-connector #spark_app_token').attr("readonly", true);
					$('.tg-app-connector #submit').val('Connected');
					$('.tg-app-connector #submit').attr("disabled", true);
					$('.tg-app-connector #submit').attr("id", "connected").attr("name", "connected");
	
					if(response && data == 'success'){
						wpsparkconnectorUpdateDbWithToken(response, getToken);
					}
					
				},
				error: function(error, xhr, error_text, statusText) {
					console.log(xhr, 'error text - ',error_text, 'status text - ', statusText);
					alert('insert a valid token');
					$('.tg-app-connector #submit').val('Connect App');
				},
				
			});
		}

	});
	/**
	 * if token is successfully authenticate
	 * send token to admin ajax 
	 * to save token in option table
	 */
	function wpsparkconnectorUpdateDbWithToken(response, token){
		$.ajax({
            url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'wpsparkconnector_get_connector_app_response',
				data: response,
				token: token,
				security: adminUrl.ajax_nonce
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
	 * send a request for new build to wpspark app
	 * both from build button in app or admin bar
	 */
	$('.tg-app-connector #spark-build, #wp-admin-bar-tg-connector-build').on('click', function(e){
		e.preventDefault();
		$(this).attr("disabled", true);
		var getToken = $('.tg-app-connector #spark-app-token').val();
		$('#build-status .uk-alert-success').css('display', 'none');

		$.ajax({
            url: 'https://app.wpspark.io/api/v1/build',
			method: 'post',
			data:{
				token: getToken,
				domain: adminUrl.mysiteurl,
				security: adminUrl.ajax_nonce
			},
            success: function( response,  data, textStatus, xhr ) {
				updateBuildStatus('1', getToken);
				if(response && data == 'success'){
					$('#build-status .build-details').css('display', 'block').append('<p>'+ response.message  +'</p>');
				}
            },
            error: function(error, xhr, error_text, statusText) {
				console.log(xhr, 'error text - ',error_text, 'status text - ', statusText);
				if(error.status === 422){
					var errorMessage = JSON.parse(error.responseText).message;
					var errorSolveUrl = JSON.parse(error.responseText).url;
					console.log(error.responseText, errorMessage, errorSolveUrl);
					$('#build-status .uk-alert-warning.ftp-details').css('display', 'block');
					$('#build-status .uk-alert-warning.ftp-details').append('<a target="_blank" class="uk-button uk-button-primary" href='+errorSolveUrl+'>Add your Ftp</a>');
				}else{
					$('#build-status .uk-alert-primary').css('display', 'none');
					$('#build-status .uk-alert-danger').css('display', 'block');
					$('.tg-app-connector #spark-build').attr("disabled", false);
				}
				
			},
			
        });

	});
	
	/**
	 * update wp_spark_build table
	 * with token
	 */
	function updateBuildStatus($status, $token){
		$.ajax({
            url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'wpsparkconnector_update_build_status',
				data: $status,
				token: $token,
				security: adminUrl.ajax_nonce
			},
		})
	}

	/**
	 * disconnect spark app 
	 * from wordpress
	 */
	$('#disconnect_application').on('click', function(e){
		e.preventDefault();
		var disconnect = confirm('Are you sure to disconnect ?');
		if(disconnect){
			$.ajax({
				url: adminUrl.ajaxurl,
				method: 'post',
				data:{
					action: 'wpsparkconnector_remove_token',
					security: adminUrl.ajax_nonce
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

	/**
	 * check build status 
	 * from build table in the spark page
	 * this will query from wordpress wp_spark_build table 
	 * to check the update status
	 * and then update the row 
	 */
	$('.check-build-status').on('click', function(e){
		var buildId = $(this).parents('tr').find('.build-id').text();
		var rowClass = $(this).parents('tr').attr('class');
		$.ajax({
			url: adminUrl.ajaxurl,
			method: 'post',
			data:{
				action: 'wpsparkconnector_check_build_status',
				buildId: buildId,
				security: adminUrl.ajax_nonce
			},
			beforeSend: function(){
				$('.'+ rowClass + '.check-status-button span').html('<img src='+ adminUrl.gifurl +' />');
			},
			success: function(response){
				var data = JSON.parse(response);
				$('.'+ rowClass + '.build-message > span').text(data.message);
				$('.'+ rowClass + '.build-status').text(data.status);
				
				if(data.message == 'published'){
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
