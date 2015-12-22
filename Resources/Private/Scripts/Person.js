define(
	[
		'Library/jquery',
		'Nieuwenhuizen.BuJitsuDo/Scripts/Application',
		'Nieuwenhuizen.BuJitsuDo/Scripts/Service/HttpClient',
		'BuJitsuDo.Authentication/Scripts/Login'
	],
	function($, Application, HttpClient) {
		Application.on('ready', function() {
			$('[data-action="edit-profile"]').on('click', function() {
				var $item = $('[data-content="profile-edit"]'),
					$imageEdit = $('[data-content="profile-image-edit"]'),
					$passwordEdit = $('[data-content="profile-password-edit"]');

				$imageEdit.removeClass('open');
				$passwordEdit.removeClass('open');
				$item.addClass('open');

				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $item.offset().top - 50
					}, 'slow');
				}, 300);
			});

			$('[data-action="edit-profile-image"]').on('click', function() {
				var $item = $('[data-content="profile-image-edit"]'),
					$profileEdit = $('[data-content="profile-edit"]'),
					$passwordEdit = $('[data-content="profile-password-edit"]');

				$profileEdit.removeClass('open');
				$passwordEdit.removeClass('open');
				$item.addClass('open');

				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $item.offset().top - 50
					}, 'slow');
				}, 300);
			});

			$('[data-action="edit-profile-password"]').on('click', function() {
				var $item = $('[data-content="profile-password-edit"]'),
					$profileEdit = $('[data-content="profile-edit"]'),
					$imageEdit = $('[data-content="profile-image-edit"]');

				$profileEdit.removeClass('open');
				$imageEdit.removeClass('open');
				$item.addClass('open');

				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $item.offset().top - 50
					}, 'slow');
				}, 300);
			});

			$('[data-action="close-profile-edit"]').on('click', function() {
				var $item = $('[data-content="profile-edit"]');
				$item.removeClass('open');
			});

			$('[data-action="close-profile-image-edit"]').on('click', function() {
				var $item = $('[data-content="profile-image-edit"]');
				$item.removeClass('open');
			});

			$('[data-action="close-profile-password-edit"]').on('click', function() {
				var $item = $('[data-content="profile-password-edit"]');
				$item.removeClass('open');
			});

			$('[data-action="update-profile"]').on('submit', function(event) {
				event.preventDefault();
				var $that = $(this),
					firstName = $that.find('[name="person[firstName]"]').val(),
					lastName = $that.find('[name="person[lastName]"]').val(),
					referenceNode = $that.find('[name="person[referenceNode]"]').val(),
					address = $that.find('[name="person[address]"]').val(),
					zipCode = $that.find('[name="person[zipCode]"]').val(),
					city = $that.find('[name="person[city]"]').val(),
					emailAddress = $that.find('[name="person[emailAddress]"]').val(),
					phone = $that.find('[name="person[phone]"]').val(),
					dateOfBirth = $that.find('[name="dateOfBirth"]').val(),
					jiuJitsu = $that.find('[name="person[jiuJitsu]"]').is(':checked') ? 1 : 0,
					buJitsuDo = $that.find('[name="person[buJitsuDo]"]').is(':checked') ? 1 : 0,
					jiuJitsuDegree = $that.find('[name="person[jiuJitsuDegree]"]').val(),
					buJitsuDoDegree = $that.find('[name="person[buJitsuDoDegree]"]').val(),
					__csrfToken = $that.find('[name="__csrfToken"]').val(),
					__trustedProperties = $that.find('[name="__trustedProperties"]').val(),
					url = $that.attr('action'),
					button = $that.find('button'),
					buttonHtml = button.html();

				var dataOverride = {
					data: {
						person: {
							firstName: firstName,
							lastName: lastName,
							referenceNode: referenceNode,
							address: address,
							zipCode: zipCode,
							city: city,
							emailAddress: emailAddress,
							phone: phone,
							jiuJitsu: jiuJitsu,
							buJitsuDo: buJitsuDo,
							jiuJitsuDegree: jiuJitsuDegree,
							buJitsuDoDegree: buJitsuDoDegree
						},
						dateOfBirth: dateOfBirth,
						__csrfToken: __csrfToken,
						__trustedProperties: __trustedProperties
					}
				};

				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');

				HttpClient.updateResource(url, dataOverride)
					.always(function() {
						button.removeAttr('disabled');
						button.html(buttonHtml);
					}
				);
			});

			$('[data-action="updateImage"]').on('submit', function() {
				var $that = $(this),
					button = $that.find('button');

				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');
			});

			$('[data-action="updatePassword"]').on('submit', function(event) {
				event.preventDefault();
				var $that = $(this),
					__csrfToken = $that.find('[name="__csrfToken"]').val(),
					__trustedProperties = $that.find('[name="__trustedProperties"]').val(),
					password1 = $that.find('[name="password[0]"]').val(),
					password2 = $that.find('[name="password[1]"]').val(),
					url = $that.attr('action'),
					button = $that.find('button'),
					buttonHtml = button.html();

				var dataOverride = {
					data: {
						password: {
							0: password1,
							1: password2
						}
					}
				};

				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');
				HttpClient.updateResource(url, dataOverride)
					.always(
						function() {
							button.removeAttr('disabled');
							button.html(buttonHtml);
						}
					);
			});

			$('[data-action="password-reset"]').on('submit', function(event) {
				event.preventDefault();

				var $that = $(this),
					__csrfToken = $that.find('[name="__csrfToken"]').val(),
					__trustedProperties = $that.find('[name="__trustedProperties"]').val(),
					emailAddress = $that.find('[name="emailAddress"]').val(),
					requirement = $that.find('[name="requirement"]').val(),
					url = $that.attr('action'),
					button = $that.find('button'),
					buttonHtml = button.html();
				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');

				var dataOverride = {
					data: {
						__csrfToken: __csrfToken,
						__trustedProperties: __trustedProperties,
						emailAddress: emailAddress,
						requirement: requirement
					}
				};
				HttpClient.updateResource(url, dataOverride)
					.always(
						function() {
							button.removeAttr('disabled');
							button.html(buttonHtml);
						}
					);
			});
		});
	}
);