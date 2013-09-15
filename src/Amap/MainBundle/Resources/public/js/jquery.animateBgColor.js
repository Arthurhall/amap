(function($)
{ 
    $.fn.animateBgColor=function(options)
    {  
        // On recupere la couleur d origine :
        var originColor = this.css('background-color');
        var originFontColor = this.css('color');
        
        //On définit nos paramètres par défaut
        var defauts=
        {
            "fontColor": false,
            "color" : "#ff0000",
            "animParent" : null,
            "callback" : null,
            "originColor" : originColor ,
            "originFontColor" : originFontColor 
        };  
        
        //On fusionne nos deux objets 
        var params=$.extend(defauts, options);
        
        
        // Au survol de l input (this) :
        this.hover(function(e) 
        {
            var elem = (params.animParent) ? $(this).parent(params.animParent) : $(this); 
            
            if(params.fontColor===false) {
                elem
                    .stop(true, true)
                    .animate({ backgroundColor: params.color}, 400)
                ;
            } else {
                elem
                    .stop(true, true)
                    .animate({ 
                        backgroundColor: params.color,
                        color: params.fontColor
                    }, 400)
                ;
            }
        },
        function()
        {
            var elem = (params.animParent) ? $(this).parent(params.animParent) : $(this);
            
            if(params.fontColor===false) {
                elem
                    .stop(true, true)
                    .animate({ backgroundColor: params.originColor}, 400)
                ;
            } else {
                elem
                    .stop(true, true)
                    .animate({ 
                        backgroundColor: params.originColor,
                        color: params.originFontColor
                    }, 400)
                ;
            }
        });
    

        // Pour chainer plusieurs plugins :
        return this;
    };
    
})(jQuery); 