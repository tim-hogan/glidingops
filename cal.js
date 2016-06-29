var aRefNew=0;
function padcal(num, size){var s = num+"";while(s.length<size)s="0"+s;return s;}
function whatcol(a,t){for(i=0;i<a.length;i++){if (a[i]==t) return i;}return -1;}
function getXMLNodeVal(node,target)
{
  
  var str="";
  try
  {
    str=node.getElementsByTagName(target)[0].childNodes[0].nodeValue;
  }
  catch(err)
  {
    return str;
  }
  return str;
}

function AddRosterRow(type,name,tableid)
{
 var btable=document.getElementById(tableid);
 var r1=btable.insertRow(-1);
 r1.setAttribute("class","calros");
 var c1 = r1.insertCell(0);
 c1.setAttribute("class","cal6");
 c1.innerHTML=type;
 c1 = r1.insertCell(1);
 c1.setAttribute("class","cal6");
 c1.colSpan=44;
 c1.innerHTML=name;
}

function AddTableRow(row,v,tableid)
{
 var btable=document.getElementById(tableid);
 var r1=btable.insertRow(-1);
 r1.setAttribute("class","cal");
 var c1 = r1.insertCell(0);
 c1.setAttribute("class","cal1");
 c1.innerHTML=v;
 for (i=0;i<44;i++)
 {
   var c=r1.insertCell(i+1);
   c.setAttribute("id","br" + padcal(row,2) + padcal(i,2));
   c.setAttribute("class","cal2");   
 }
}

function bookinginrow(r,s,e,id,t,typeclr)
{
    var startcol=-1;
    var lastcol=-1;
    var i=0;
             
    //check that is displayable
    var stcol = (s*4)-32;
    var ecol = (e*4)-32;

    if (stcol > 43 || ecol < 0)
       return; 

    for (i=0;i<44;i++)
    {
      if (i>=stcol) 
      {
        if (i<ecol)
        {
         if(startcol==-1)startcol=i;
         var c=document.getElementById("br" + padcal(r,2) + padcal(i,2));
         c.removeAttribute("class");
        }
        else
          if(lastcol==-1)lastcol=(i-1);
      }
    }
    if(lastcol==-1)lastcol=43;
    for(i=startcol;i<=lastcol;i++)
    {
    	var c=document.getElementById("br" + padcal(r,2) + padcal(i,2));
        if (i==startcol)
        {
            c.colSpan=(lastcol-startcol)+1;
            c.removeAttribute("class");
            c.style.backgroundColor=typeclr;
            c.setAttribute("class","cal4");
            var g = "<a href='bookings.php?id=" + id +"'";
            if (aRefNew > 0)
 		g+= " target='_blank'";
            g += " class='cal' title='" + t + "'>" + t + "</a>";
            c.innerHTML = g;
	}
	else
            c.parentNode.removeChild(c);
    }
}

function buildbookingtable(strXML,tableid,bNewRefTarget)
{
   var res=new Array();
   var nextrow=0;
   var i=0;
   
   aRefNew =  bNewRefTarget;  

   parser=new DOMParser();
   bookDoc=parser.parseFromString(strXML,"text/xml");


   //Create the headings
   var btable=document.getElementById(tableid);
   if (null == btable)
     return;
   btable.setAttribute("class","cal");
   var x = btable.rows.length;   
   
   //Delete all children
   for (i=0;i<x;i++)
      btable.deleteRow(0);

   
   //Insert a blank row
   var r1=btable.insertRow(-1);
   r1.setAttribute("class","cal7");
   
   //Craete the roster entries
   var bookings = bookDoc.getElementsByTagName("bookings")[0].childNodes;	
   for(i=0;i<bookings.length;i++)
   {
     if (bookings[i].nodeName=='duty')
     {
       var type = getXMLNodeVal(bookings[i],"t");
       var name = getXMLNodeVal(bookings[i],"n");
       var phone = getXMLNodeVal(bookings[i],"p");
       var dispname = name + " [" + phone + "]";
       
       AddRosterRow(type,dispname,tableid);
 
     }
   }
 
   //Insert a blank row
   r1=btable.insertRow(-1);
   r1.setAttribute("class","cal7");

   //Create new headers
   r1=btable.insertRow(-1);
  
   var c1 = r1.insertCell(0);
   c1.setAttribute("class","cal1");
   for (i=0;i<11;i++)
   {
       var c=r1.insertCell(i+1);
       c.colSpan="4";
       var v=i+8; 
       c.innerHTML = padcal(v,2) + ":" + "00";
       c.setAttribute("class","cal1");
       
   }
   r1=btable.insertRow(-1);
   c1 = r1.insertCell(0);
   c1.setAttribute("class","cal3");   
   c1.innerHTML="RESOURCES";	
   for (i=0;i<44;i++)
   {
       var c=r1.insertCell(i+1);
       c.setAttribute("class","cal3");
       
   }


   //Loop here hetting just instructors first
   for(i=0;i<bookings.length;i++)
   {
     if (bookings[i].nodeName=='duty')
     {
        var type=getXMLNodeVal(bookings[i],"t");
        var name=getXMLNodeVal(bookings[i],"n");
        var phone=getXMLNodeVal(bookings[i],"p");

       var ins = type.match(/instructor/i);
       if (null != ins)
       {	
       		var col = whatcol(res,name);
                if (col<0)
                {
       			AddTableRow(nextrow,name,tableid);
       			res[nextrow]=name;
       			nextrow++;
                }
       }
     }
   }

	
   
   //Loop here and get all the instructors first   
   for(i=0;i<bookings.length;i++)
   {
     if (bookings[i].nodeName=='booking')
     {
        var instructor=getXMLNodeVal(bookings[i],"i");
       
        if (instructor.length > 0)
        {
           var col = whatcol(res,instructor);
	   if (col<0)
           {
               AddTableRow(nextrow,instructor,tableid);
               res[nextrow]=instructor;
               nextrow++;
            }
        }
        
     }
   }
   
   for(i=0;i<bookings.length;i++)
   {
     if (bookings[i].nodeName=='booking')
     {
       var id = getXMLNodeVal(bookings[i],"id");
       var r = getXMLNodeVal(bookings[i],"r");
       var s = getXMLNodeVal(bookings[i],"s");
       var e = getXMLNodeVal(bookings[i],"e");
       var t = getXMLNodeVal(bookings[i],"t");
       var instructor=getXMLNodeVal(bookings[i],"i");
       var ty = getXMLNodeVal(bookings[i],"ty");
       var tyclr = getXMLNodeVal(bookings[i],"tyclr");
       if (r.length == 0)
          r=ty;
       var col = whatcol(res,r);
       if (col < 0)
       {
          AddTableRow(nextrow,r,tableid);
          bookinginrow(nextrow,s,e,id,t,tyclr);
          res[nextrow]=r;
          nextrow++;
       }
       else
       {
          
          bookinginrow(col,s,e,id,t,tyclr);
       }
       if (instructor.length>0)
       {
           console.log("We have and instructor to book");
           col = whatcol(res,instructor);
           console.log("Add to col " + col) ;
           if (col >= 0)
               bookinginrow(col,s,e,id,t,tyclr);
       }


     }
     
   }
}