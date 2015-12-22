define(
	[
		'Library/jquery',
		'Nieuwenhuizen.BuJitsuDo/Scripts/Application'
	],
	function($, Application) {
		Application.on('ready', function() {
			$('[data-action="show-password-reset"]').on('click', function() {
				var $item = $('[data-content="reset-password"]');

				$item.addClass('open');

				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $item.offset().top - 50
					}, 'slow');
				}, 300);
			});

			$('[data-action="close-password-reset"]').on('click', function() {
				var $item = $('[data-content="reset-password"]');
				$item.removeClass('open');
			});
		});
	}
);