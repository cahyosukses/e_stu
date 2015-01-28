var Func = {
    base: web.base,
	ajax: function(p) {
		p.is_json = (p.is_json == null) ? true : p.is_json;
		
		$.ajax({ type: 'POST', url: p.url, data: p.param, success: function(data) {
			if (p.is_json == 1) {
				eval('var result = ' + data);
				p.callback(result);
			} else {
				p.callback(data);
			}
		} });
	},
	array_to_json: function(Data) {
		var temp = '';
		for (var i = 0; i < Data.length; i++) {
			temp = (temp.length == 0) ? Func.object_to_json(Data[i]) : temp + ',' + Func.object_to_json(Data[i]);
		}
		return '[' + temp + ']';
	},
	datatable: function(p) {
		/*
		var param = {
			id: 'datatables',
			source: 'helper/datatable.php',
			column: [ { }, { }, { } , { bSortable: false, sClass: 'center' } ],
			callback: function() {
				$('#datatables .btn-detail').click(function() {
					var raw_record = $(this).siblings('.hide').text();
					eval('var record = ' + raw_record);
					
					Func.populate({ cnt: '#modal-submission', record: record });
					$('#modal-submission').modal();
				});
			}
		}
		var dt = Func.datatable(param);
		/*	*/
		var cnt_id = '#' + p.id;
		
		var dt_param = {
			"aoColumns": p.column,
			"sAjaxSource": p.source,
			"bProcessing": true, "bServerSide": true, "sServerMethod": "POST", "sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "<span>Search:</span> ",
				"sInfo": "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
				"sLengthMenu": "_MENU_ <span>entries per page</span>"
			},
			"fnDrawCallback": function (oSettings) {
				// init tooltips
				$('[rel=tooltip]').tooltip();
				$(cnt_id + ' .cursor-font-awesome').tooltip({ placement: 'top' });
				
				// styling row
				var counter = $(cnt_id).find('tbody tr').length;
				if (counter > 0) {
					for (var i = 0; i < counter; i++) {
						// coloring
						var color = $(cnt_id).find('tbody tr').eq(i).find('span.color').data('color');
						if (color != null) {
							$(cnt_id).find('tbody tr').eq(i).find('td').css('background', color);
						}
						
						// font
						var font = $(cnt_id).find('tbody tr').eq(i).find('span.font-weight');
						if (font.length > 0) {
							font.parents('tr').css('font-weight','bold')
						}
					}
				}
				
				if (p.callback != null) {
					p.callback();
				}
			}
		}
		if (p.fnServerParams != null) {
			dt_param.fnServerParams = p.fnServerParams;
		}
		if (p.aaSorting != null) {
			dt_param.aaSorting = p.aaSorting;
		}
		if (p.bPaginate != null) {
			dt_param.bPaginate = p.bPaginate;
		}
		
		var table = $(cnt_id).dataTable(dt_param);
		
		// initiate
		if (p.init != null) {
			p.init();
		}
		
		var dt = {
			table: table,
			reload: function() {
				if ($(cnt_id + '_paginate .paginate_active').length > 0) {
					$(cnt_id + '_paginate .paginate_active').click();
				} else {
					$(cnt_id + '_length select').change();
				}
			}
		}
		
		// init search
		$(cnt_id).parents('.panel-table').find('.btn-search').click(function() {
			var value = $(cnt_id).parents('.panel-table').find('.input-keyword').val();
			dt.table.fnFilter( value );
		});
		
		return dt;
	},
	get_name: function(value) {
		var result = value.trim().replace(new RegExp(/[^0-9a-z]+/gi), '_').toLowerCase();
		return result;
	},
	in_array: function(Value, Array) {
		var Result = false;
		for (var i = 0; i < Array.length; i++) {
			if (Value == Array[i]) {
				Result = true;
				break
			}
		}
		return Result;
	},
	is_empty: function(value) {
		var Result = false;
		if (value == null || value == 0) {
			Result = true;
		} else if (typeof(value) == 'string') {
			value = Helper.Trim(value);
			if (value.length == 0) {
				Result = true;
			}
		}
		
		return Result;
	},
	object_to_json: function(obj) {
		var str = '';
		for (var p in obj) {
			if (obj.hasOwnProperty(p)) {
				if (obj[p] != null) {
					str += (str.length == 0) ? str : ',';
					str += '"' + p + '":"' + obj[p] + '"';
				}
			}
		}
		str = '{' + str + '}';
		return str;
	},
	populate: function(p) {
		for (var form_name in p.record) {
			if (p.record.hasOwnProperty(form_name)) {
				var input = $(p.cnt + ' [name="' + form_name + '"]');
				var value = p.record[form_name];
				
				if (input.attr('type') == 'radio') {
					input.filter('[value=' + value.toString() + ']').prop('checked', true);
				} else if (input.attr('type') == 'checkbox') {
					input.prop('checked', false);
					if (value == 1) {
						input.prop('checked', true);
					}
				} else if (input.hasClass('input-datepicker')) {
					input.val(Func.swap_date(value));
				} else {
					input.val(value);
				}
			}
		}
	},
	swap_date: function(value) {
		if (value == null) {
			return '';
		}
		
		var array_value = value.split('-');
		if (array_value.length != 3) {
			return '';
		}
		
		var result = '';
		if (array_value[0].length == 4) {
			result = array_value[1] + '-' + array_value[2] + '-' + array_value[0];
		} else {
			result = array_value[2] + '-' + array_value[0] + '-' + array_value[1];
		}
		
		return result;
	},
	trim: function(value) {
		return value.replace(/^\s+|\s+$/g,'');
	},
    form: {
        get_value: function(container) {
			var PrefixCheck = container.substr(0, 1);
			if (! Func.in_array(PrefixCheck, ['.', '#'])) {
				container = '#' + container;
			}
			
            var data = Object();
			var set_value = function(obj, name, value) {
				if (typeof(name) == 'undefined') {
					return obj;
				} else if (name.length < 3) {
					obj[name] = value;
					return obj;
				}
				
				var endfix = name.substr(name.length - 2, 2);
				if (endfix == '[]') {
					var name_valid = name.replace(endfix, '');
					if (obj[name_valid] == null) {
						obj[name_valid] = [];
					}
					obj[name_valid].push(value);
				} else {
					obj[name] = value;
				}
				
				return obj;
			}
            
            var Input = jQuery(container + ' input, ' + container + ' select, ' + container + ' textarea');
            for (var i = 0; i < Input.length; i++) {
				var name = Input.eq(i).attr('name');
				var value = Input.eq(i).val();
				
				if (Input.eq(i).attr('type') == 'checkbox') {
					if (Input.eq(i).is(':checked')) {
						data = set_value(data, name, value);
					} else {
						data = set_value(data, name, 0);
					}
				} else if (Input.eq(i).attr('type') == 'radio') {
					value = $(container + ' [name="' + name + '"]:checked').val();
					data = set_value(data, name, value);
				} else if (Input.eq(i).hasClass('input-datepicker')) {
					data = set_value(data, name, Func.swap_date(value));
				} else {
					data = set_value(data, name, value);
				}
            }
			
            return data;
        },
		submit: function(p) {
			p.notify = (p.notify == null) ? true : p.notify;
			
			Func.ajax({ url: p.url, param: p.param, callback: function(result) {
				if (result.status == true) {
					if (p.notify) {
						if (result.message != null && result.message.length > 0) {
							$.notify(result.message, "success");
						}
					}
					
					if (p.callback != null) {
						p.callback(result);
					}
				} else {
					$.notify(result.message, "error");
					
					if (p.callback_error != null) {
						p.callback_error(result);
					}
				}
			} });
		},
		confirm_delete: function(p) {
			var cnt_modal = '';
			cnt_modal += '<div id="cnt-confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
			cnt_modal += '<div class="modal-dialog">';
			cnt_modal += '<div class="modal-content">';
			cnt_modal += '<div class="modal-header">';
			cnt_modal += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			cnt_modal += '<h4 class="modal-title">Confirmation</h4>';
			cnt_modal += '</div>';
			cnt_modal += '<div class="modal-body">';
			cnt_modal += '<p>Are you sure ?</p>';
			cnt_modal += '</div>';
			cnt_modal += '<div class="modal-footer">';
			cnt_modal += '<button type="button" class="btn btn-primary">Yes</button>';
			cnt_modal += '<button type="button" class="btn btn-close btn-default" data-dismiss="modal" aria-hidden="true">No</button>';
			cnt_modal += '</div>';
			cnt_modal += '</div>';
			cnt_modal += '</div>';
			cnt_modal += '</div>';
			$('#cnt-temp').html(cnt_modal);
			$('#cnt-confirm').modal();
			
			$('#cnt-confirm .btn-primary').click(function() {
				$.ajax({ type: "POST", url: p.url, data: p.data }).done(function( RawResult ) {
					eval('var result = ' + RawResult);
					
					$('#cnt-confirm .btn-close').click();
					if (result.status == 1) {
						$.notify(result.message, "success");
					} else {
						$.notify(result.message, "error");
					}
					
					if (p.callback != null) {
						p.callback();
					}
				});
			});
		}
    },
	get_color: function(value) {
		var color = '#FF0000';
		if (value >= 90) {
			color = '#008000';
		} else if (value >= 80) {
			color = '#bacf0b';
		} else if (value >= 70) {
			color = '#e7912a';
		}
		
		return color;
	}
}

/*	jQuery */
// combo.category_sub({ category_id: $(this).val(), target: $('#modal-advert-type-sub [name="category_sub_id"]') });
// combo.category_sub({ category_id: 'x', target: $('#modal-advert-type-sub [name="category_sub_id"]'), value: result.category_sub_id });
var combo = {
	student: function(p) {
		p.s_parent_id = (p.s_parent_id == null) ? 0 : p.s_parent_id;
		
		var ajax_param = {
			is_json: 0, url: web.base + 'combo',
			param: { action: 'student', s_parent_id: p.s_parent_id },
			callback: function(option) {
				p.target.html(option);
				
				// set value
				if (typeof(p.value) != 'undefined') {
					p.target.val(p.value);
				}
				
				if (p.callback != null) {
					p.callback();
				}
			}
		}
		Func.ajax(ajax_param);
	},
	category_sub: function(p) {
		p.category_id = (p.category_id == null) ? 0 : p.category_id;
		
		var ajax_param = {
			is_json: 0, url: web.base + 'panel/combo',
			param: { action: 'category_sub', category_id: p.category_id },
			callback: function(option) {
				p.target.html(option);
				
				// set value
				if (typeof(p.value) != 'undefined') {
					p.target.val(p.value);
				}
				
				if (p.callback != null) {
					p.callback();
				}
			}
		}
		Func.ajax(ajax_param);
	}
}

function str_pad(input, pad_length, pad_string, pad_type) {
  //  discuss at: http://phpjs.org/functions/str_pad/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Michael White (http://getsprink.com)
  //    input by: Marco van Oort
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
  //   returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
  //   example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
  //   returns 2: '------Kevin van Zonneveld-----'

  var half = '',
    pad_to_go;

  var str_pad_repeater = function(s, len) {
    var collect = '',
      i;

    while (collect.length < len) {
      collect += s;
    }
    collect = collect.substr(0, len);

    return collect;
  };

  input += '';
  pad_string = pad_string !== undefined ? pad_string : ' ';

  if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
    pad_type = 'STR_PAD_RIGHT';
  }
  if ((pad_to_go = pad_length - input.length) > 0) {
    if (pad_type === 'STR_PAD_LEFT') {
      input = str_pad_repeater(pad_string, pad_to_go) + input;
    } else if (pad_type === 'STR_PAD_RIGHT') {
      input = input + str_pad_repeater(pad_string, pad_to_go);
    } else if (pad_type === 'STR_PAD_BOTH') {
      half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
      input = half + input + half;
      input = input.substr(0, pad_length);
    }
  }

  return input;
}

$(document).ready(function() {
	// init date & time picker
	if ($(".datepicker").datepicker != null) {
		$(".datepicker").datepicker({ format: 'mm-dd-yyyy' }).on('changeDate', function (ev) {
			$(ev.target).find('input').change()
		});
	}
	if ($(".datepicker").timepicker != null) {
		$('.timepicker').timepicker({ minuteStep: 1, template: 'modal', showSeconds: true, showMeridian: false, defaultTime: 'value' });
	}
	
	// tooltips
	if ($('[rel=tooltip]').tooltip != null) {
		$('[rel=tooltip]').tooltip();
	}
	
	// form password
	$('.btn-update-password').click(function() {
		$('#modal-update-password').modal();
	});
	$('#modal-update-password form').validate({
		rules: {
			passwd_old: { required: true },
			passwd_new: { required: true },
			passwd_confirm: { required: true, equalTo: '#modal-update-password [name="passwd_new"]' }
		}
	});
	$('#modal-update-password form').submit(function(e) {
		e.preventDefault();
		if (! $('#modal-update-password form').valid()) {
			return false;
		}
		
		// ajax request
		var param = Func.form.get_value('modal-update-password');
		Func.form.submit({
			url: web.base + 'home/action',
			param: param,
			callback: function(result) {
				$('#modal-update-password').modal('hide');
				$('#modal-update-password form')[0].reset();
			}
		});
	});
	
	// reset task & attendance
	$('.btn-reset-task').click(function() {
		Func.form.confirm_delete({
			data: { action: 'reset_task' },
			url: web.base + 'home/action'
		});
	});
	$('.btn-reset-attendance').click(function() {
		Func.form.confirm_delete({
			data: { action: 'reset_attendance' },
			url: web.base + 'home/action'
		});
	});
	
	// change student
	$('.change-student').click(function() {
		var student_id = $(this).data('student_id');
		
		// ajax request
		var param = Func.form.get_value('form-mail');
		Func.form.submit({
			url: web.base + 'home/action',
			param: { action: 'change_student', student_id: student_id },
			callback: function(result) {
				window.location = web.base;
			}
		});
	});
});