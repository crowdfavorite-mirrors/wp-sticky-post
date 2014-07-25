/**
 * @detail
 * Additional function to handle content
 * http://zourbuth.com/
 */

(function ($) {
    $.fn.spDateTime = function (options) {

        var defaults = {}, selector;

        options  = $.extend(defaults, options);
        selector  = this;

        return this.each(function () {

			var stamp = $('.timestamp span', selector).html();

			function updateText() {
				var attemptedDate, originalDate, currentDate, publishOn,
					aa = $('.aa', selector).val(),
					mm = $('.mm', selector).val(), 
					jj = $('.jj', selector).val(), 
					hh = $('.hh', selector).val(), 
					mn = $('.mn', selector).val();
				
				attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
				originalDate = new Date( $('.hidden_aa', selector).val(), $('.hidden_mm', selector).val() -1, $('.hidden_jj', selector).val(), $('.hidden_hh', selector).val(), $('.hidden_mn', selector).val() );
				currentDate = new Date( $('.cur_aa', selector).val(), $('.cur_mm', selector).val() -1, $('.cur_jj', selector).val(), $('.cur_hh', selector).val(), $('.cur_mn', selector).val() );

				if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
					$('.timestamp-wrap', selector).addClass('form-invalid');
					return false;
				} else {
					$('.timestamp-wrap', selector).removeClass('form-invalid');
				}

				if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
					$('.timestamp span', selector).html(stamp);
				} else {
					$('.timestamp span', selector).html(
						$('option[value="' + $( '.mm', selector ).val() + '"]', ( '.mm', selector ) ).text().substring(3) + ' ' +
						jj + ', ' +
						aa + ' @ ' +
						hh + ':' +
						mn
					);
				}
				return true;
			}

			$('.timestampdiv', selector).siblings('a.edit-timestamp').click(function() {
				if ($('.timestampdiv', selector).is(":hidden")) {
					$('.timestampdiv', selector).slideDown('fast');
					$(this).hide();
				}
				
				return false;
			});

			$('.cancel-timestamp', selector).click(function() {
				$('.timestampdiv', selector).slideUp('fast');
				$('.mm', selector).val($('.hidden_mm', selector).val());
				$('.jj', selector).val($('.hidden_jj', selector).val());
				$('.aa', selector).val($('.hidden_aa', selector).val());
				$('.hh', selector).val($('.hidden_hh', selector).val());
				$('.mn', selector).val($('.hidden_mn', selector).val());
				$('.timestampdiv', selector).siblings('a.edit-timestamp').show();
				updateText();
				return false;
			});

			$('.save-timestamp', selector).click(function () { // crazyhorse - multiple ok cancels
				if ( updateText() ) {
					$('.timestampdiv', selector).slideUp('fast');
					$('.timestampdiv', selector).siblings('a.edit-timestamp').show();
				}
				return false;
			});
		
			
        });
    };
	
	$.fn.spAddImages = function(){
		$(this).click(function() {
			var imagesibling = $(this).siblings('img'),
			inputsibling = $(this).siblings('input'),
			buttonsibling = $(this).siblings('a');
			tb_show('Select Image/Icon Title', 'media-upload.php?post_id=0&type=image&TB_iframe=true');	
			window.send_to_editor = function(html) {
				var imgurl = $('img',html).attr('src');
				if ( imgurl === undefined || typeof( imgurl ) == "undefined" ) imgurl = $(html).attr('src');		
				imagesibling.attr("src", imgurl).slideDown();
				inputsibling.val(imgurl);
				buttonsibling.addClass("showRemove").removeClass("hideRemove");
				tb_remove();
			};
			return false;
		});
	}
	
	$.fn.spRemoveImages = function(){
		$(this).click(function() {
			$(this).next().val('');
			$(this).siblings('img').slideUp();
			$(this).removeClass('show-remove').addClass('hide-remove');
			$(this).fadeOut();
			return false;
		});
	}
})(jQuery);