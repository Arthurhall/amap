
jQuery(function($)
{	
	var data;
	var cnt = 0;	
	var defaults = uploader.settings.multipart_params;
	/*
	 * 
	 */
	
	// PROGRESS
	uploader.bind('UploadProgress', function(up, file)
	{
		if(file.percent > 10)
		{
			$('#'+file.id).find('.progress').css('width', file.percent+'%');
		}
	});
	
	
	// INIT
	uploader.bind('Init', function(up, params)
	{
		if(params.runtime != 'html5')
		{
			$('#drop_area').css('border','none').find('p,span').remove();
		}
	});
	
	
	uploader.init();
	
	
	// ERROR
	uploader.bind('Error', function(up, err)
	{
		alert('Message : '+err.message+'. Code : '+err.code);

		$('#drop_area').removeClass('hover');
		up.refresh();
	});
	
	
	// UPLOADED
	var z = 0;
	uploader.bind('FileUploaded', function(up, file, response)
	{		
		z++;

		data = $.parseJSON(response.response);
		
		if(data.error)
		{
			alert(data.message);
			$('#'+file.id).remove();
		}
		else
		{
			$('#'+file.id).find('.progress_bar').fadeOut();
			$('#'+file.id).prepend('<img class="thumbnail pull-left" src="'+ data.web_path +'" data-toggle="tooltip" data-original-title="'+ plupload.formatSize(file.size) +'" />');
			
			var form_fields = '<label>Titre :</label> <input type="text" name="title['+ data.image_id +']" class="input_title" /> ' +
                '<br /> ' +
                '<label>Alternatif :</label> <input type="text" name="alt['+ data.image_id +']" class="input_alt" /> '+
                '<br /> '+
                '<button '+ 
                '    class="btn vitrine" '+ 
                '    type="button"  '+
                '    value="0" '+
                '    name="vitrine['+data.image_id+']" '+
                '    form="form_'+data.image_id+'" '+
                '> '+
                '    <i class="icon-eye-open"></i> '+
                '    Vitrine '+
                '</button> '+
                '<a class="btn btn-success pull-right update" href="#"> '+
                    '<i class="icon-edit"></i> '+
                    'Envoyer '+
                '</a> '+
            '';
            
            $('#'+file.id+' form').prepend( form_fields );
            
			$('#'+file.id+' form').prepend('<input type="hidden" name="id" value="'+ data.image_id +'" />');
			
			$('#'+file.id+' .btn.delete').fadeIn();
			
			if(defaults.ref == 'new')
			{
    			$('section.prim form.form').append(
                    '<input type="hidden" name="image_entity[' + z + ']" value="'+ data.image_id +'" />'
                );
            }
		}
	})
	
	$('#drop_area').bind({
		dragover : function(e){
			$(this).addClass('hover');
		},
		dragleave : function(e){
			$(this).removeClass('hover');
		}
	});
	
	
	// DELETE
	$(document).on('click', '.file .btn.delete', function(e)
    {
        e.preventDefault();
        if( confirm('Voulez vous vraiment supprimer cette image ?') )
        {
            var box = $(this).parents('.file');
            var form = box.find('form');
            
            $.ajax
            ({
                type: 'POST',
                url: defaults.url_delete,
                dataType: "json",
                data: form.serialize()
            })
            .success(function( msg ) 
            {
                if(msg.error)
                {
                    alert(msg.message);
                }
                else
                {
                    box.remove();
                }
            });
        }
    });
    
    // UPDATE 
    $(document).on('click', '.file .btn.update, .file .btn.vitrine', function(e)
    {
        e.preventDefault();
        
        var box = $(this).parents('.file');
        var form = box.find('form');
        
        if($(this).hasClass('vitrine'))
        {
            var _input = form.find('.input-vitrine');
            
            if(_input.val() == 1)
            {
                _input.val(0);
            }
            else
            {
                _input.val(1);
            }
        }
        
        $.ajax
        ({
            type: 'POST',
            url: form.attr('action'),
            dataType: "json",
            data: form.serialize()
        })
        .success(function( msg ) 
        {
            if(msg.error)
            {
                alert(msg.message);
            }
            else
            {
                box.prepend('<div class="alert alert-success">L\'élément a été mis à jour avec succès.</div>');
                
                if(msg.vitrine)
                {
                    box.find('.btn.vitrine').addClass('btn-info');
                } else {
                    box.find('.btn.vitrine').removeClass('btn-info');
                }
            }
        });
    });
    
    
    // ADDED
    var cnt = 0;
    uploader.bind('FilesAdded', function(up, files)
    {
        $('#drop_area').removeClass('hover');
        var filelist = $('#file_list');
        
        for(var i in files)
        {
            cnt++;
            var file = files[i];
            
            var arr = file.name.split( '.' );
            var ex = arr[arr.length-1];
                
            var champs = '<input type="hidden" name="file_extension_'+ cnt +'" class="input_extension" value="'+ ex +'" /> ' +
                '<input type="hidden" value="'+ defaults.user +'" name="user">' +
                '<input type="hidden" value="'+ defaults.article_id +'" name="article_id">' + 
            '';  
            
            //champs = champs.replace(/§cnt§/g, cnt);
            //champs = champs.replace(/§ex§/g, ex);
            
            filelist.prepend('<div id="' + file.id + '" class="file clearfix"> '+
                '<span class="filename hide">' + file.name + '</span>'+
                '<form action="'+ defaults.form_action +'" method="post">'
                    + champs +
                '</form> '+
                '<div class="btn-group pull-right"> '+
                    '<button class="btn btn-danger delete" type="submit" style="display:none;"> '+
                        '<i class="icon-remove-sign"></i> '+
                        'Supprimer '+
                    '</button> '+
                '</div>'+
                '<div class="progress_bar" style="display:block;"><div class="progress"></div></div> '+
            '</div>');
        }
        
        $('input#plupload_count').val(cnt);
        
        //$('#file_list .btn.delete').css('display', 'none');
        $('#'+file.id+' .progress_bar').fadeIn();
        
        uploader.settings.multipart_params.json = "{" ;
        
        $('.file').each(function(i) 
        {
            
            uploader.settings.multipart_params.json += '"'+i+'": {"filename": "'
                +$(".filename", this).text()+'", "title": "'
                +$(".input_title", this).val()+'", "alt": "'
                +$(".input_alt", this).val()+'", "ext": "'
                +$(".input_extension", this).val()+'"}'
            ;
            
            uploader.settings.multipart_params.json +=  (i < $('.file').length-1) ? ', ':''; 
        });
        
        uploader.settings.multipart_params.json += "}";
        
        uploader.start();
        uploader.refresh();
    });
});

