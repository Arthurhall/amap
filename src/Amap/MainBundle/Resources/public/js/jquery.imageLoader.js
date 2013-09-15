(function($)
{ 
	$.fn.imageLoader=function(options)
	{  
		//On définit nos paramètres par défaut
		var defauts=
		{
			"images" : new Array(),
			"callback" : null
		};  
   
		//On fusionne nos deux objets 
		var params=$.extend(defauts, options);
		
		var arrayLinkImages = params.images;
		var arrayImages = new Array();
		var etatChargement = null;
		
		// si les images ne sont pas fournies en parametre 
		// On les récupère : 
		if(arrayLinkImages.length == 0)
		{
			this.find('img').each(function(i) 
			{
				arrayLinkImages[i] = $(this).attr('src');
			});
		}
		
		var nb_img = arrayLinkImages.length;
		
		
		// Chargement des images :
		for(i=0; i<nb_img; i++)
		{
			arrayImages[i]=document.createElement("img");
			arrayImages[i].setAttribute("id", "img_"+new Date().getTime()+"_"+i);
			arrayImages[i].setAttribute("src", arrayLinkImages[i]);
		}
		etatChargement = setInterval(checkLoad,1);
		
		
		function checkLoad()
		{
			retour=true;
			for(i=0; i<nb_img; i++)
			{
				if(arrayImages[i].complete == false)
				{
					retour = false;
				}
			}
			if(retour==true)
			{
				// On coupe la verification :
				clearInterval( etatChargement );
				// Et on lance le callback :
				if(params.callback != null) params.callback();
			}
		}
		
		
		// Pour chainer plusieurs plugins :
		return this;
	};
	
})(jQuery); 	   