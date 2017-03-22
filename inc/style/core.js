	Alert_ = function() {}
	Alert_.Warning = function(Message, ID_)
	{
		$('#' + ID_).html('<div class="alert alert-dismissible alert-danger"><a class="close" data-dismiss="alert">×</a><span>' + Message + '</span></div>')
	}
	Alert_.Information = function(Message, ID_)
	{
		$('#' + ID_).html('<div class="alert alert-dismissible alert-info"><a class="close" data-dismiss="alert">×</a><span>' + Message + '</span></div>')
	}
	Alert_.Success = function(Message, ID_)
	{
		$('#' + ID_).html('<div class="alert alert-dismissible alert-success"><a class="close" data-dismiss="alert">×</a><span>' + Message + '</span></div>')
	}

function PasswordCheck(Pass1, Pass2, ID_)
{
	var Result = true;
	
    if (Pass1 != Pass2)
    {
        Alert_.Warning(MESSAGE_Match, ID_);
        Result = false;
    }
	
    if (Pass1.length < 5)
    {
        Alert_.Warning(MESSAGE_Short, ID_);
        Result = false;
    }
	
    return Result;
}


    function SetVisibility(ElementID) 
	{
       var Element = document.getElementById(ElementID);
       if(Element.style.display == 'block')
          Element.style.display = 'none';
       else
          Element.style.display = 'block';
    }
	
    $(function () 
	{
            $('#events_').bootstrapTable({ })
			//.on('click-row.bs.table', function (e, row, $element) { })
			.on('dbl-click-row.bs.table', function (e, row, $element) 
			{
				if($element.context.firstElementChild.id == "sho___aa")
				{
					var tmp = $element.context.firstElementChild.textContent;
					//console.log($element.context.firstElementChild);
					$element.context.firstElementChild.textContent = $element.context.firstElementChild.getAttribute("name");
					$element.context.firstElementChild.setAttribute("name", tmp)
					//$element.context.firstElementChild.name = tmp;
				}

            })/*.on('all.bs.table', function (e, name, args) 
			{
				if(name == "dbl-click-row.bs.table")
					alert(args[1].context.textContent);
                console.log('Event:', name, ', data:', args);
            })*/;
    });
	
	var VBack;
	function Check(Doc)
	{
		var bRes = false;
		
		if(Doc.href.length > 5)
			VBack = Doc.href;
		
		bootbox.confirm("Are you sure?", function(result) 
		{
			if(result)
				window.open(VBack, "_self");
		});
		
		Doc.href = "#";
	}
	
$(document).ready(function () 
{
	$("#iTi").keypress(function (e) { if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) { return false; } });
	$("#iLi").keypress(function (e) { if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) { return false; } });
	
	$('select').on('change', function()
	{
		var selected = $(this).find("option:selected").val();
		if(selected == 3 || selected == 4 || selected == 5)
		{
		    $('#iTi').prop('disabled', false);
		}
		else
		{
		   $('#iTi').prop('disabled', 'disabled');
		}
	});
});