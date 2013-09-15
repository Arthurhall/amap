var docInstance = $(document); 

$(document).ready(function() 
{
	
	// Anim BG Menu principal :
	$('#menu a:not(.current)').animateBgColor({'color': '#f5f506'});
	
    // Anim BG Checkbox :
    $('.wrapp_checkbox input').animateBgColor({
        'color':'#ca00ca', 
        'originColor':'#000', 
        'animParent': '.wrapp_checkbox'
    });
    // Anim BG Helper :
    $('.box .helper').animateBgColor({
        'color':'#ca00ca', 
    });
    $('.montant_global .helper, #contrat .helper').animateBgColor({
        'color':'#f5f506', 
        'originColor': '#ffffff',
        'fontColor': '#000000'
    });
    
    // TwitterBootstrap Tooltip
    $('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });
    
    // Confirm DELETE 
    $('form.form_delete').submit(function(e)
    {
        var check = confirm('Vous êtes sur le point de supprimer l\'élément, voulez-vous continuer ?');
        
        if(!check) {
            return false;
        }
    });
    
    // Champ Quantité :
    $('.form .input-append.input-prepend .icon-plus-sign, .form .input-append.input-prepend .icon-minus-sign').click(function(e)
    {
       var input = $(this).parents('.input-append.input-prepend').find('input.quantity');
       var val = parseInt( input.val() );
       
       if ($(this).hasClass('icon-plus-sign')) {
           input.val(val+1);
       } else if ($(this).hasClass('icon-minus-sign') && val>0) {
           input.val(val-1); 
       }
       calculTotal();
    });
    
    // TOTAL Commande :
    $('input.quantity').change(function(e)
    {
       calculTotal();
    });
    
    function calculTotal ()
    {
        var prices = $('input.price');
        var total = 0;
        
        prices.each(function(i)
        {
            var elem = $(this);
            //var product_id = $(this).attr('id').replace(/price_/g, '');
            var p = parseFloat( elem.val() );
            var q = parseFloat( elem.parent().find('.quantity').val() );
            total += (p*q);
        });
        
        if(total > 0) {
            $('#total').text( total.toFixed(2) );
            $('#tr_total').removeClass('hide');
        } else {
            $('#total').text( total.toFixed(2) );
            $('#tr_total').addClass('hide');
        }
        
    }
    
    // Page mon Compte :
    $('.sonata-user-show .nav-tabs a').click(function(e)
    {
        if($(this).hasClass('prevent-default')) {
            e.preventDefault();
        }
        
        $(this).parents('ul.nav-tabs').find('li.active').removeClass('active');
        $(this).parent().addClass('active');
        
        var id = $(this).attr('id');
        var wrapp = $('#wrapp_'+id);
        
        if( wrapp.hasClass('hide') ){
            
            $('.wrapper').addClass('hide');
            wrapp.removeClass('hide');
        }
    });
    
    
    // Les Permanences
    $('.form.create_permanence').submit(function(e)
    {
        e.preventDefault();
        var form = $(this);
        
        var check = confirm('Inscription pour le mardi ' + form.find('.date').val() + ' à ' + form.find('.heure').val() + 'h ?');
        
        if(!check) {
            return false;
        }
        
        $.ajax
        ({
            type: form.attr('method'),
            url: form.attr('action'),
            dataType: "json",
            data: form.serialize()
        })
        .success(function( msg ) 
        {
            var td = form.parent();
            var tr = td.parent();
                
            if(msg.returnState == 'complet')
            {
                td.append(' <span class="label label-success">Complet</span>');
            }
            if(msg.returnState == 'remove')
            {
                form.remove();
            }
            if(msg.returnState == 'ok')
            {
                form.remove();
                td.append(' <span class="label label-success">Inscription Ok</span>');
            }
            
            if(msg.error)
            {
                alert(msg.message);
            }
            else
            {
                if(msg.showMessage)
                {
                    alert(msg.message);
                }
                
                tr.find('.users').append(msg.inscription + '<br />');
                tr.attr('class', msg.usersCountClass);
                
                //alert(msg.message);
                $('#wrapp_permanences').prepend('<div class="alert alert-success">'+msg.fullInscription+'</div>');
            }
        });
        
        return false;
    });
	
});

function centrage(elemWidth, elemHeight)
{
    var margeLeft = ($(window).width() - elemWidth) /2;
    var margeTop = ($(window).height() - elemHeight) /2;
    
    var offset = $(window).scrollTop();
    margeTop += (offset+15);
    if(margeTop < 50) margeTop = 50;
    
    return new Array(margeLeft, margeTop);
}
