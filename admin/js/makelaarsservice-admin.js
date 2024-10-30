(function( $ ) {
	'use strict';


	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function(){
		/*
			@desc navigates tab-content according to tab navigation
		*/
		// let tabsWithContent = (function () {
		// 	let tabs = document.querySelectorAll('.tabs li');
		// 	let tabsContent = document.querySelectorAll('.tab-content');

		// 	let deactvateAllTabs = function () {
		// 		tabs.forEach(function (tab) {
		// 			tab.classList.remove('is-active');
		// 		});
		// 	};

		// 	let hideTabsContent = function () {
		// 		tabsContent.forEach(function (tabContent) {
		// 			tabContent.classList.remove('is-active');
		// 		});
		// 	};

		// 	let activateTabsContent = function (tab) {
		// 		tabsContent[getIndex(tab)].classList.add('is-active');
		// 	};

		// 	let getIndex = function (el) {
		// 		return [...el.parentElement.children].indexOf(el);
		// 	};

		// 	tabs.forEach(function (tab) {
		// 		tab.addEventListener('click', function () {
		// 			deactvateAllTabs();
		// 			hideTabsContent();
		// 			tab.classList.add('is-active');
		// 			activateTabsContent(tab);
		// 		});
		// 	})

		// 	tabs[0].click();
		// })();

		$('.tabs ul li').on('click', function() {
			var id = $(this).attr('data-tab');

			$(".tabs ul li").removeClass("is-active");
			$(".tab-content").removeClass("is-active");
			$(this).addClass("is-active");
			$("#" + id).addClass("is-active");
		})

		function openTab(evt, tabName) {
			var i, x, tablinks;

			x = $('.content-tab');

			for(i = 0; i < x.length; i++) {
				x[i].style.display = "none";
			}

			tablinks = $(".nl-pnt-ms-tab");

			for(i = 0; i < x.length; i++) {
				tablinks[i].className = tablinks[i].className.replace("is-active", "");
			}

			document.getElementById(tabName).style.display = "block";
			evt.currentTarget.className += " is-active";
		}

		/*
			@desc Update all objects of a given realtor
			@param int id - The realtor id, string name - the realtor name
			@return Notification (success)
		*/
		function updateProperties(id, name) {
			$('.nl-pnt-ms-updated_at[data-token-id='+id+']').html('<progress class="progress is-medium is-primary ms-update-progress" max="100">45%</progress>');

			//Run ajax_add_estate_propeties function in back-end via WP Ajax API
			$.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: 'add_estate_properties',
					type: 'single',
					token_id: id
				},
				success: function( data ){
					$('.nl-pnt-ms-updated_at[data-token-id='+id+']').text(data);
					$('#nl-pnt-ms-admin-message').append('<div class="notification is-success is-dismissible"><button class="delete"></button>'+name+' succesvol bijgewerkt.</div>');

				}
			});
		}


		/*
			@desc Initialize the WordPress color picker
		*/
		$('.makelaarsservice-colorpicker').wpColorPicker();


		/*
			@desc When clicked, remove a single notification
		*/
		$('body').on('click', '.notification > .delete', function() {
			$(this).parent().fadeOut(200, function(){
				$(this).remove();
			});
		});


		/*
			@desc Submit form values to WordPress options API.
			@param e - event
			@return Notification: success
		*/
		$('#nl-pnt-ms-form-settings').submit(function(e) {
			e.preventDefault();
			var b = $(this).serialize();
			$.post( 'options.php', b ).error(
				function() {
					alert('error');
				}).success( function() {
				$('#nl-pnt-ms-admin-message').append('<div class="notification is-success is-dismissible"><button class="delete"></button>Instellingen opgeslagen.</div>');
			});
		})


		/*
			@desc When update button clicked, run updateProperties function for that realtor
		*/
		$('body').on('click', '.nl-pnt-ms-update', function() {
			var id, name;

			id = $(this).attr('data-token-id');
			name = $(this).attr('data-token-name');

			updateProperties(id, name);
		});


		/*
			@desc Pause synchronisation of a certain token when pause button clicked
			@return Notification (success)
		*/
		$('body').on('click', '.nl-pnt-ms-pause', function() {
			var id, name;

			id = $(this).attr('data-token-id');
			name = $(this).attr('data-token-name');

			// Run function ajax_pause_token in back-end via WP Ajax API
			$.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: 'pause_token',
					token_id: id,
				},
				success: function( data ){
					location.reload();

				}
			});
		});


		/*
			@desc Delete a certain token when delete button clicked
			@return Notification (success)
		*/
		$('body').on('click', '.nl-pnt-ms-delete', function() {
			var id, name, element;

			id = $(this).attr('data-token-id');
			name = $(this).attr('data-token-name');
			element = $(this);

			// Run function ajax_delete_token in back-end via WP Ajax API
			$.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: 'delete_token',
					token_id: id,
				},
				success: function( data ){
					element.parent().parent().remove();
					$('#nl-pnt-ms-admin-message').append('<div class="notification is-success is-dismissible"><button class="delete"></button>Token succesvol verwijderd!</div>');
				}
			});
		});


		/*
			@desc Adds a new token. Check if token is valid. If yes, add to WP. If not, display error
			@param e - event
			@return Notification. If valid: success. Else: warning + error message
		*/
		$('body').on('submit', '.nl-pnt-ms-form-add-token', function(e) {
			e.preventDefault();
			var token = $(this).parent().find( $('input[name="token"]') ).val();

			//Run function ajax_add_token in back-end via WP Ajax API
			$.ajax({
				type: "POST",
				url: "/wp-admin/admin-ajax.php",
				data: {
					action: 'add_token',
					token: token,
				},
				dataType: "json",
				success: function( data ){
					var html;

					if ( data === 'invalid' ) {
						// Display error message (invalid token)
						$('.nl-pnt-ms-error-text').text( 'Token ongeldig' );
						$('#nl-pnt-ms-new-token').addClass( 'nl-pnt-ms-input-error' );
					} else if ( data === 'exists' ) {
						// Display error message (token exists)
						$('.nl-pnt-ms-error-text').text( 'Token reeds aanwezig.' );
						$('#nl-pnt-ms-new-token').addClass( 'nl-pnt-ms-input-error' );
					} else {
						// Token is added. Append to <table>
						$('.nl-pnt-ms-error-text').empty();
						$('#nl-pnt-ms-new-token').removeClass( 'nl-pnt-ms-input-error' );

						html = `<tr>
										<td>` + data.name[0] +`</td>
										<td>` + token + `</td>
										<td>
											<div class='nl-pnt-ms-statusbox nl-pnt-ms-bg-success'>
												<span>Actief</span>
											</div>
										</td>
										<td>
											<span class='nl-pnt-ms-updated_at' data-token-id='` + data.token_id +`'></span>
										</td>
										<td>
											<button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-success nl-pnt-ms-update' data-token-id='` + data.token_id +`' data-token-name=''>
												<span class='dashicons dashicons-update'></span>
											</button>
				
											<button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-warning nl-pnt-ms-pause' data-token-id='` + data.token_id +`' data-token-name=''>
												<span class='dashicons dashicons-controls-pause'></span>
											</button>
				
											<button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-danger nl-pnt-ms-delete' data-token-id='` + data.token_id +`' data-token-name=''>
												<span class='dashicons dashicons-trash'></span>
											</button>
										</td>
									</tr>`;

						$('#nl-pnt-ms-token-table tbody').append(html);
						$('input[name="token"]').val('');

						// Initial token synchronisation
						updateProperties( data.token_id, data.name[0]);
						//$('#nl-pnt-ms-admin-message').append('<div class="notification is-success is-dismissible"><button class="delete"></button>Token succesvol toegevoegd!</div>');

					}
				}
			})

		});
	});

})( jQuery );
