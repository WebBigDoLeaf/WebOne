			function  append(o,v){
				var option=new Option(v,v);
				o.options.add(option);
			}
			function  initDate(){ 
				var y = document.getElementsByName("year")[0].value;
				var m = document.getElementsByName("month")[0].value; 
				var day=document.getElementsByName("day")[0];
				day.innerHTML="";
				   
				if(m==1 || m==3 || m==5 || m==7 || m==8 || m==10 || m==12){
					for(var j = 1; j <= 31; j++){
						append(day,j);
					}
				}
				else if(m==4 || m==6 || m==9 || m==11){
					for(var j = 1; j <= 30; j++){
						append(day,j);
					}
				}
				else if(m == 2){
					var flag = (y % 4 == 0)&& (y % 100 != 0) || (y % 400 == 0);
					if(flag){
						for(var j = 1; j <= 29; j++){
							 append(day,j);
						}
					}
					else{
						for(var j = 1;j <= 28; j++){
							append(day,j);
						}
					}
				}
			}
