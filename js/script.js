/* Author: Jahfer Husain */
var streamInterval, end, streamLoadUrl;

$(document).ready(function() {

	if(!Modernizr.input.placeholder){	
		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();
		$('[placeholder]').parents('form').submit(function() {
		  $(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
			  input.val('');
			}
		  })
		});	
	}
	
	
	$( "#discussions" ).accordion({
		collapsible: true,
		active: false,
		autoHeight: false,
		header: 'a.header',
	});
	
	$(".load_more").click(function(e) {
		offset = $(this).attr('data-offset');
		url = $(this).attr('data-url') + "daily_mag/ajax_get_" + $(this).attr('id') + "s/" + offset;
		$(this).attr('data-offset', parseInt(offset)+5)
		$.get(url, function(data) {
			$("#discussions").children(".load_more").before(data);
			
			$("#discussions").accordion('destroy');
			$( "#discussions" ).accordion({
				collapsible: true,
				active: false,
				autoHeight: false,
				header: 'a.header',
			});			
		});
		e.preventDefault();
	});
	
	
	

	$('#notification-count').click(function() {
		$('#notif-dropdown').fadeToggle(150);
	});

	$('#dropdown-link').click(function() {
		$(this).siblings().fadeToggle(150);
		if($(this).attr('class') != 'selected')
			$(this).addClass("selected");
		else			
			$(this).removeClass("selected");
	});
		
	$('#attach-link').click(function() {
		$(this).hide();
		$('#attachment').show();
	});
	
	$('#post-text').click(function() {
		$(this).css('border-bottom-color', '#ddd')
		.css('color', '#444')
		.elastic()
		.siblings('.hidden').removeClass('hidden')
		.hide().fadeIn(150);
	});
	
	$(document).delegate(".comment_text", 'click', function() {	
		$(this).elastic();
	});
	
	
	$('#add-button').click(function() {
		$('#filter_panel').fadeToggle('fast');
		$('<div id=blackout>').appendTo('html');
	});
	
	$(document).delegate('#blackout', 'click', function() {
		$('#filter_panel').fadeOut('fast');
		$('#blackout').fadeOut('fast', function() {			
			$('#blackout').remove();
		});		
	});
	
	$(document).delegate(".stream_object", 'mouseover mouseout', function(e) {
		if (e.type == 'mouseover') {
			$('.edit', this).css('display', 'inline-block');
			$('.remove', this).show();
		} else {
			$('.edit', this).hide();
			$('.remove', this).hide();
		}
	});
	
	$(document).delegate(".smile-vote", 'click', function() {
		$(this).siblings('.smile-vote-container').fadeToggle(150);
	});
	
	$(document).delegate(".smile-vote-options", 'click', function() {
		$(this).parent().fadeOut(150);
	});
	
	
	$("#load-more").click(function() {		
		var offset = parseInt($(this).attr('data-offset'));
		$('<div>').load(streamLoadUrl+"/"+offset, function() {
			if($(this).html() == "") {
				return;
			}
			$(this).hide();
			$('#stream-list').append($(this));
			$(this).fadeIn('slow');
			$("#load-more").attr('data-offset', offset+10);
			clearInterval(streamInterval);
		});
	});	
	
	$('.tag_suggestion').click(function(e) {
		var tag_box = $(this).parent().siblings('#post-tags').val();
		
		if(tag_box == "")
			$(this).parent().siblings('#post-tags').val($(this).html());
		else			
			$(this).parent().siblings('#post-tags').val(tag_box + ", " + $(this).html());
		
		e.preventDefault();
	});
	
	// autocomplete for tags
	var availableTags = ["code", "design", "doc", "funny", "gaming", "link", "pic", "question", "school", "science", "technology", "video"];
	
	var allowed_chars = [8, 9, 13, 16, 17, 20, 27, 32, 33, 34, 35, 36, 37, 38, 39, 40, 46];
	var error_flag_up = false;
	var shift_held = false;
	
	for(i=48; i<106; i++)
		allowed_chars.push(i);
	
	function split( val ) {
		return val.split( / \s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}
	
	$("#new-filter-title").bind("keydown", function(event) {		
		if(($.inArray(event.keyCode, allowed_chars) == -1 && ! error_flag_up) 
		|| (shift_held && $.inArray(event.keyCode, [48, 49, 50, 51, 52, 53, 54, 55, 56, 57]) > -1 && !error_flag_up)) {
			$("<img src='img/bad_char.gif' id=bad-char-flag-filter />").appendTo($('#filter_panel')).hide().fadeIn("fast");
			error_flag_up = true;
		} else if(event.keyCode == 8 && error_flag_up) {
			$("#bad-char-flag-filter").fadeOut("fast").delay(100).remove();;
			error_flag_up = false;		
			shift_held = false;	
		} else if(event.keyCode == 16 && !shift_held) {
			shift_held = true;
		} else if(shift_held) {
			shift_held = false;
		}
	});

	$( "#post-tags" )
	.bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
				$( this ).data( "autocomplete" ).menu.active ) {
			event.preventDefault();
		}
		
		if(($.inArray(event.keyCode, allowed_chars) == -1 && ! error_flag_up) 
		|| (shift_held && $.inArray(event.keyCode, [48, 49, 50, 51, 52, 53, 54, 55, 56, 57]) > -1 && !error_flag_up)) {
			$("<img src='img/bad_char.gif' id=bad-char-flag />").appendTo($('#write_block')).hide().fadeIn("fast");
			error_flag_up = true;
		} else if(event.keyCode == 8 && error_flag_up) {
			$("#bad-char-flag").fadeOut("fast").delay(100).remove();;
			error_flag_up = false;		
			shift_held = false;	
		} else if(event.keyCode == 16 && !shift_held) {
			shift_held = true;
		} else if(shift_held) {
			shift_held = false;
		}
	}).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			response( $.ui.autocomplete.filter(
				availableTags, extractLast( request.term ) ) );
		},
		focus: function(event, ui) {
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			terms.pop();
			terms.push( ui.item.value );
			terms.push( "" );
			this.value = terms.join( " " );
			return false;
		},
		open: function() {
			$('.ui-autocomplete')
			.prepend( $("<li class='ui-menu-item-disabled'><p class='info'>Type a new tag or choose from the list below. Separate multiple tags with a space.</p></li>") );
		}
	}).focus(function() {			
			if (this.value == "")
				$(this).trigger('keydown.autocomplete');
		});
	
	$('#stream-settings-btn').click(function() {
		$(this).siblings().toggle();
	});
	
});


function sub(url, el, mode, owner, filter) {
	if( ! el.hasClass('clicked') )
	$.post(url, 
		{action:mode, o:owner, f:filter},
		function(data) {
			switch(parseInt(data)) {
				case 0:
					// success
					el.addClass('btn-success clicked').html(mode.charAt(0).toUpperCase() + mode.slice(1) + 'scribed!');
					break;
				default:
					// failure
					el.addClass('btn-failure').html(mode.charAt(0).toUpperCase() + mode.slice(1) + 'scription failed!');
					break;
				}
		}
	);
}

function run_stream_ajax(load_url) {
}

/*
function run_stream_ajax(load_url) {
	streamLoadUrl = load_url;
	//play_stream();
	
	loading = end = false;
	
	$(window).scroll(function() {
		var diff = parseInt($(window).height()) + parseInt($(window).scrollTop());
		if ($('body').height() <= diff) {
			if(!loading && !end) {
				var offset = parseInt($('#stream').attr('data-offset'));
				loading = true;
				$('<div>').load(load_url+"/"+offset, function() {
					if($(this).html() == "") {
						end = true;
						return;
					}
					$(this).hide();
					$('#stream-list').append($(this));
					$(this).fadeIn('slow');
					$('#stream').attr('data-offset', offset+10)
					loading = false;
					clearInterval(streamInterval);
				})
			}
		}	
	});
	
}
*/

function run_tag_js(ajax_url) {
	$(document).delegate(".edit", 'click', function() {			
		pause_stream();
		// TODO: keep current tags in place, add input box after	
		var contents = new Array(),
			 tags = "", 
			 name = $(this).siblings(".name").html(),
			 appendString = "";	
		$(this).siblings('.tag').each(function() {
			contents.push( $(this).html() );
		});
		
		for(var i=0; i<contents.length-1; i++) 
			tags += contents[i] + " ";
		tags += contents[contents.length-1];
		if(tags == "undefined") tags = "";
		appendString += "<input type='text' name='tag_edit' id='tag_edit' value='" + tags + "' />";	
		appendString += "<span class='small'>&crarr; Enter to save.</span>"
		$(this).parentsUntil('.post_main').html(appendString);	
		
		$('#tag_edit').focus();	
		
		// add AJAX submit
		$('#tag_edit').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13) {			
				$.ajax({
					url: ajax_url,
					type: 'POST',
					context: $(this).parent(),
					data: { 
						new_tags: $('#tag_edit').val(),
						id: $(this).parentsUntil('stream_object').siblings('.post_id').html()
					},
					success: function(msg) {
						$(this).html(msg+"<input type='button' value='+ edit' class='tag edit' />");
						play_stream();
					}
				});						
			};
		});	
		
		$('#tag_edit').focusout(function() {
			$.ajax({
				url: ajax_url,
				type: 'POST',
				context: $(this).parent(),
				data: { 
					new_tags: $('#tag_edit').val(),
					id: $(this).parentsUntil('stream_object').siblings('.post_id').html()
				},
				success: function(msg) {
					$(this).html(msg+"<input type='button' value='+ edit' class='tag edit' />");
					play_stream();
				}
			});				
		});
		
	});
}

function pause_stream() {
	clearInterval(streamInterval);
}

function play_stream() {
	streamInterval = setInterval(function() {
		$('#stream-list').load(streamLoadUrl);
	}, 10000);
}

// force instant reload
function refresh_stream() {
	pause_stream();
	$('#stream-list').load(streamLoadUrl);
	play_stream();
}