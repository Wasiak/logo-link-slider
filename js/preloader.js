function kinkyPreloader(unique_name,preload_class,check_time,execute_this_function)
		{

		
  	  	$(document).everyTime(check_time,unique_name,function(i)
  	  		{
  	  			var we_can_go=true;
  	  	  	  	
  	  			preload_class.each(function()
  	  				{

  	  					if (!$(this).get(0).complete) 
							
  	  					{ 
  	  						we_can_go=false; 
  	  					}
					
  	  					if (typeof $(this).get(0).naturalWidth!="undefined" && $(this).get(0).naturalWidth==0) 
									
  	  					{
  	  						we_can_go=false;
  	  					}
  	  	  	  	  	  	  	  	  	  	  	  	   	  	  	  	  	  	 
  	  			
  	  					
  	  				});
  	  			
  	  					if (we_can_go==true) 
  	  	  	  	
  	  					{ 
  	  						$(document).stopTime(unique_name); 
  	  						execute_this_function();
  	  					}


  	  	  	});
  	  	}
  		
