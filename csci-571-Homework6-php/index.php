<?php

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $itemId=$_GET['id'];
    $url="http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID=MariaFer-Test-PRD-216de56dc-63f77791&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId=".$itemId."&maxResults=8";
    $response=file_get_contents($url);
    echo $response;
    exit();
  }

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $itemId=file_get_contents('php://input');
    $url="http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid=MariaFer-Test-PRD-216de56dc-63f77791&siteid=0&version=967&ItemID=".$itemId."&IncludeSelector=Description,Details,ItemSpecifics";
    $response=file_get_contents($url);
    echo $response;
    exit();
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data=file_get_contents('php://input');
  $jsonObj=json_decode($data);
  $keywords=$jsonObj->keyword;
  $category=$jsonObj->category;
  $condition_new=$jsonObj->condition_new;
  $condition_used=$jsonObj->condition_used;
  $condition_unspecified=$jsonObj->condition_unspecified;
  $shipping_free=$jsonObj->shipping_free;
  $shipping_local=$jsonObj->shipping_local;
  $distance=$jsonObj->distance;
  $here=$jsonObj->here;
  $zip=$jsonObj->zip;
  $loc=$jsonObj->loc;
  $dist_enable_disable=$jsonObj->dist_enable_disable;
  $zipval="";
  $categorySelected="";
  if($dist_enable_disable===false)
    $zipval=$zip;
  if($dist_enable_disable===true){
    if($here==="here")
      $zipval=$zip;
    else
      $zipval=$loc;
  }
  $url="http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=MariaFer-Test-PRD-216de56dc-63f77791&RESPONSE-DATA-FORMAT=JSON&RESTPAYLOAD&paginationInput.entriesPerPage=20&";
  if($category==="default"){
    $categorySelected="default";
  }
  else{
    $categorySelected=$category;
    $url.="categoryId=".$categorySelected."&";
  }
  $i=0;
  $urlManipulation="keywords=".$keywords."&";
    if($dist_enable_disable===true){
    $urlManipulation.="buyerPostalCode=".$zipval."&itemFilter(".$i.").name=MaxDistance&itemFilter(".$i.").value=".$distance."&";
    $i++;
  }
  if($shipping_free==="" && $shipping_local===""){
    $urlManipulation.="itemFilter(".$i.").name=FreeShippingOnly&itemFilter(".$i.").value=true&";
    $i++;
    $urlManipulation.="itemFilter(".$i.").name=LocalPickUpOnly&itemFilter(".$i.").value=true&";
    $i++;
  }
  else
  if($shipping_free!=="" && $shipping_local!==""){
    $urlManipulation.="itemFilter(".$i.").name=FreeShippingOnly&itemFilter(".$i.").value=true&";
    $i++;
    $urlManipulation.="itemFilter(".$i.").name=LocalPickUpOnly&itemFilter(".$i.").value=true&";
    $i++;
  }
  else
    if($shipping_free!==""){
      $urlManipulation.="itemFilter(".$i.").name=FreeShippingOnly&itemFilter(".$i.").value=true&";
    $i++;
    $urlManipulation.="itemFilter(".$i.").name=LocalPickUpOnly&itemFilter(".$i.").value=false&";
    $i++;
    }
  else
    if($shipping_local!==""){
      $urlManipulation.="itemFilter(".$i.").name=FreeShippingOnly&itemFilter(".$i.").value=false&";
    $i++;
    $urlManipulation.="itemFilter(".$i.").name=LocalPickUpOnly&itemFilter(".$i.").value=true&";
    $i++;
    }
    $urlManipulation.="itemFilter(".$i.").name=HideDuplicateItems&itemFilter(".$i.").value=true&";
    $i++;
    $j=0;
    if($condition_new==="" && $condition_used==="" && $condition_unspecified===""){
      $urlManipulation.="itemFilter(".$i.").name=Condition&itemFilter(".$i.").value(".$j.")=New&";
      $j++;
      $urlManipulation.="itemFilter(".$i.").value(".$j.")=Used&";
      $j++;
      $urlManipulation.="itemFilter(".$i.").value(".$j.")=Unspecified"; 
    }
    else{
      $urlManipulation.="itemFilter(".$i.").name=Condition&";
    if($condition_new!==""){
      $urlManipulation.="itemFilter(".$i.").value(".$j.")=New&";
      $j++;
    }
    if($condition_used!==""){
      $urlManipulation.="itemFilter(".$i.").value(".$j.")=Used&";
      $j++;
    }
    if($condition_unspecified!==""){
      $urlManipulation.="itemFilter(".$i.").value(".$j.")=Unspecified"; 
    }
    }
  $url.=$urlManipulation;
  $url = str_replace(' ', '+', $url);
  $response=file_get_contents($url);
  echo $response;
  exit();
}
?>
<html>
<head>
  <title>Product Search</title>
  <script>



window.onload = function() {
    document.getElementById('distance').disabled = true;
    document.getElementById('here').disabled = true;
    document.getElementById('zip').disabled = true;
    document.getElementById('loc').disabled = true;
    document.getElementById("submit_btn").disabled = true;
    if (window.XMLHttpRequest)
    {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
    } 
    else 
    {
    // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
      }
    xmlhttp.open("GET", "http://ip-api.com/json" ,false);
    xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState == 4 ){
            var jsonObj = JSON.parse(xmlhttp.responseText);
            zip = jsonObj.zip;
            document.getElementById("submit_btn").disabled = false;
        }
      };
      xmlhttp.send();
       }




       function submitForm(){
        document.getElementById('errorMsg').style.display = "none";
        if(document.getElementById('enable_nearby_search').checked && /[a-zA-Z]/.test(document.getElementById('distance').value)){
          document.getElementById('errorMsg').style.display = "block";
          document.getElementById('errorMsg').innerHTML="Distance is invalid";
          document.getElementById('table').style.display = "none";
        }
        else
          if(parseInt(document.getElementById('distance').value, 10)<0){
          document.getElementById('errorMsg').style.display = "block";
          document.getElementById('errorMsg').innerHTML="Distance is invalid";
          document.getElementById('table').style.display = "none";
          }
        else
          if(document.getElementById('zip').checked==true && document.getElementById('loc').value==""){
          document.getElementById('loc').required=true;
          }
          else
          if(document.getElementById('enable_nearby_search').checked && document.getElementById('zip').checked && isNaN(parseInt(document.getElementById('loc').value, 10)) && document.getElementById('loc').value.length!=5){
          document.getElementById('errorMsg').style.display = "block";
          document.getElementById('errorMsg').innerHTML="Zipcode is invalid";
          document.getElementById('table').style.display = "none";
        }
        else{
        if(document.getElementById('keyword').value!=""){
        if(document.getElementById('zip').checked==true && document.getElementById('loc').value==""){
          document.getElementById('loc').required=true;
        }
        else{
          var keyword="",category="",condition_new="",condition_used="",condition_unspecified="",shipping_free="",shipping_local="",distance="",here="",loc="",zipcode=zip,dist_enable_disable=true;
        if(!document.getElementById('zip').checked)
          document.getElementById('loc').required=false;
        keyword=document.getElementById('keyword').value;
        category=document.getElementById('category').value;
        if(document.getElementById('new').checked)
        condition_new=document.getElementById('new').value;
        if(document.getElementById('used').checked)
        condition_used=document.getElementById('used').value;
        if(document.getElementById('unspecified').checked)
        condition_unspecified=document.getElementById('unspecified').value;
        if(document.getElementById('free').checked)
        shipping_free=document.getElementById('free').value;
        if(document.getElementById('local').checked)
        shipping_local=document.getElementById('local').value;
        if(document.getElementById('here').checked)
        here=document.getElementById('here').value;
        if(document.getElementById('zip').checked)
        zipcode=document.getElementById('zip').value;
        if(document.getElementById('loc').value)
        loc=document.getElementById('loc').value;
        if(document.getElementById('distance').value=="0.0" || document.getElementById('distance').value=="0" || !document.getElementById('distance').value)
          distance="10";
        else
          distance=document.getElementById('distance').value;     
        if(!document.getElementById('enable_nearby_search').checked){
          dist_enable_disable=false;
        }
        if(document.getElementById('enable_nearby_search').checked){
          dist_enable_disable=true;
        }
        var obj = {
          keyword:keyword,
          category:category,
          condition_new:condition_new,
          condition_used:condition_used,
          condition_unspecified:condition_unspecified,
          shipping_free:shipping_free,
          shipping_local:shipping_local,
          distance:distance,
          here:here,
          zip:zipcode,
          loc:loc,
          dist_enable_disable:dist_enable_disable
        };
        if (window.XMLHttpRequest)
    {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
    } 
    else 
    {
    // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
      }
      
      xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState == 4 ){
            jsonObj= JSON.parse(xmlhttp.responseText);
            generateHTML(jsonObj);
        }
      };
      xmlhttp.open("POST", "index.php",true);
      xmlhttp.send(JSON.stringify(obj));
    }
  }
    }
  }




  function generateHTML(jsonObj) {
      x="50px"; y="50px";
      if(jsonObj.findItemsAdvancedResponse[0].ack[0]=="Failure"){
      if(jsonObj.findItemsAdvancedResponse[0].errorMessage[0].error[0].errorId[0]){
         document.getElementById('errorMsg').style.display = "block";
         document.getElementById('errorMsg').innerHTML="No Records has been found.";
         document.getElementById('table').style.display = "none";
      }
     }
      else
      if(jsonObj.findItemsAdvancedResponse[0].ack[0]=="Failure"){
      if(jsonObj.findItemsAdvancedResponse[0].errorMessage[0].error[0].errorId[0]=="18"){
         document.getElementById('errorMsg').style.display = "block";
         document.getElementById('errorMsg').innerHTML="Zipcode is invalid";
         document.getElementById('table').style.display = "none";
      }
     }
      else
      if(jsonObj.findItemsAdvancedResponse[0].searchResult[0].item!=undefined){
      var tabledata=jsonObj.findItemsAdvancedResponse[0].searchResult[0].item;
      var html_text="<div style='margin-top: 50px;margin-left: 150px;'><table style='border:2px solid lightgrey;border-collapse: collapse;'>";
      html_text+="<tbody>";
      html_text+="<tr>";
      html_text+="<th>Index</th>";
      html_text+="<th>Photo</th>";
      html_text+="<th>Name</th>";
      html_text+="<th>Price</th>";
      html_text+="<th>Zip Code</th>";
      html_text+="<th>Condition</th>";
      html_text+="<th>Shipping Options</th>";
      html_text+="</tr>";
      for(i=0;i<tabledata.length;i++) {
        html_text+="<tr>";
        var values=tabledata[i];
        html_text+="<td>"+(i+1)+"</td>";

        if(tabledata[i].galleryURL!=undefined){
        if(tabledata[i].galleryURL[0])
           html_text+="<td><img src='"+ tabledata[i].galleryURL[0] +"' width='"+x+"' height='"+y+"'></td>";
         else
          html_text+="<td>N/A</td>";
        }
        else
        html_text+="<td>N/A</td>";


        if(tabledata[i].title!=undefined){
        if(tabledata[i].title[0])
           html_text+="<td><a style='color: black;text-decoration: none;' onclick='goToSecondPage("+tabledata[i].itemId[0]+")'>"+ tabledata[i].title[0] +"</a></td>";
         else
          html_text+="<td>N/A</td>";
        }
        else
        html_text+="<td>N/A</td>";


        if(tabledata[i].sellingStatus!=undefined){
          if(tabledata[i].sellingStatus[0].currentPrice!=undefined){
        if(tabledata[i].sellingStatus[0].currentPrice[0].__value__)
          html_text+="<td>"+ "$ "+tabledata[i].sellingStatus[0].currentPrice[0].__value__ +"</td>";
        else
          html_text+="<td>N/A</td>";
         }
         else
           html_text+="<td>N/A</td>";
       }
         else
           html_text+="<td>N/A</td>";


        if(tabledata[i].postalCode!=undefined){
        if(tabledata[i].postalCode[0])
           html_text+="<td>"+ tabledata[i].postalCode[0]+"</td>";
         else
          html_text+="<td>N/A</td>";
        }
        else
          html_text+="<td>N/A</td>";


        if(tabledata[i].condition!=undefined){
        if(tabledata[i].condition[0].conditionDisplayName)
           html_text+="<td>"+ tabledata[i].condition[0].conditionDisplayName+"</td>";
         else
          html_text+="<td>N/A</td>";
        }
        else
         html_text+="<td>N/A</td>";

   
        if(tabledata[i].shippingInfo!=undefined){
          if(tabledata[i].shippingInfo[0].shippingServiceCost!=undefined){
            if(tabledata[i].shippingInfo[0].shippingServiceCost[0].__value__=="0.0")
              html_text+="<td> Free Shipping </td>";
             else if(tabledata[i].shippingInfo[0].shippingServiceCost[0].__value__)
              html_text+="<td> $"+tabledata[i].shippingInfo[0].shippingServiceCost[0].__value__+"</td>";
          }
        else
          html_text+="<td>N/A</td>";
      }
      else
        html_text+="<td>N/A</td>";

      }
      html_text+="</tbody>";
      html_text+="</table></div>";
      document.getElementById('table').style.display = "block";
      document.getElementById('table').innerHTML=html_text;
    }
    else{
      document.getElementById('errorMsg').style.display = "block";
      document.getElementById('errorMsg').innerHTML="No Records has been found";
      document.getElementById('table').style.display = "none";
    }
  }




  function goToSecondPage(itemId){
     document.getElementById('errorMsg').style.display = "none";
     if (window.XMLHttpRequest)
    {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
    } 
    else 
    {
    // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
      }
    xmlhttp.open("PUT", "index.php" ,true);
    xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState == 4 ){
            jsonObj= JSON.parse(xmlhttp.responseText);
            generateSecondTable(jsonObj);
        }
      };
      xmlhttp.send(itemId);
    }




    function generateSecondTable(jsonObj){
      document.getElementById('errorMsg').style.display = "none";
      x="150px"; y="150px";
      if(jsonObj.Item!=undefined){
      var tabledata=jsonObj.Item;
      var html_text="<div style='margin-top: 50px;margin-right:auto;margin-left:auto;width:700px;'><table style='border:2px solid lightgrey;border-collapse: collapse;width:100%'><caption style='margin-bottom: 0px;'><h1 style='margin-bottom:0px'>Item Details</h1></caption>";
      html_text+="<tbody>";
      html_text+="<tr><th>Photo</th>";
      if(tabledata.PictureURL!=undefined){
      if(tabledata.PictureURL[0]!="")
       html_text+="<td><img src='"+ tabledata.PictureURL[0] +"' width='"+x+"' height='"+y+"'></td>";
      else
      html_text+="<td>N/A</td></tr>";
      }
      else
      html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Title</th>";
      if(tabledata.Title!=undefined){
      if(tabledata.Title!="")
       html_text+="<td>"+ tabledata.Title +"</td>";
      else
      html_text+="<td>N/A</td></tr>";
      }
      else
        html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Subtitle</th>";
      if(tabledata.Subtitle!=undefined){
      if(tabledata.Subtitle!="")
       html_text+="<td>"+ tabledata.Subtitle +"</td>";
      else
      html_text+="<td>N/A</td></tr>";
      }
      else
        html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Price</th>";
      if(tabledata.CurrentPrice!=undefined){
      if(tabledata.CurrentPrice.Value!="")
        html_text+="<td>"+ tabledata.CurrentPrice.Value+" "+tabledata.CurrentPrice.CurrencyID+"</td>";
      else
      html_text+="<td>N/A</td></tr>";
      }
      else
        html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Location</th>";
      if(tabledata.Location!=undefined){
      if(tabledata.Location!="")
        html_text+="<td>"+ tabledata.Location +"</td>";
      else
      html_text+="<td>N/A</td></tr>";
      }
      else
        html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Seller</th>";
      if(tabledata.Seller!=undefined){
        if(tabledata.Seller.UserID!=undefined){
          if(tabledata.Seller.UserID!="")
            html_text+="<td>"+ tabledata.Seller.UserID+"</td>";
            else
            html_text+="<td>N/A</td></tr>";
        }
        else
            html_text+="<td>N/A</td></tr>";
      }
      else
        html_text+="<td>N/A</td></tr>";


      html_text+="<tr><th>Return Policy(US)</th>";
      if(tabledata.ReturnPolicy!=undefined){
        if(tabledata.ReturnPolicy.ReturnsWithin!=undefined){
          if(tabledata.ReturnPolicy.ReturnsWithin!="")
          html_text+="<td> Return Accepted Within "+ tabledata.ReturnPolicy.ReturnsWithin+"</td>";
           else
          html_text+="<td>N/A</td></tr>";
      }
      else
          html_text+="<td>N/A</td></tr>";
      }
      else
          html_text+="<td>N/A</td></tr>";


      if(tabledata.ItemSpecifics!=undefined){
        if(tabledata.ItemSpecifics.NameValueList!=undefined){
      for(i=0;i<tabledata.ItemSpecifics.NameValueList.length;i++){
        var check=tabledata.ItemSpecifics.NameValueList[i].Name;
        var valCheck=tabledata.ItemSpecifics.NameValueList[i];
        
        html_text+="<tr><th>"+check+"</th>";
        if(valCheck.Value[0]!="")
        html_text+="<td>"+ valCheck.Value[0]+"</td>";
        else
        html_text+="<td>N/A</td></tr>";

      }
     }
    }
    var framedata ="",length="";
      if(tabledata.Description!=undefined){
        framedata = tabledata.Description.replace(/"/g, "'");
        length=framedata.length;
      }
      else{
        framedata = "";
        length=framedata.length;
      }
      html_text+="</tbody>";
      html_text+="</table></div>";
      html_text+="<div id=show style='text-align:center;margin-top:30px;'>Click to show seller details</div>";
      html_text+="<div id=showImage style='text-align:center;margin-top:10px;'><img src=http://csci571.com/hw/hw6/images/arrow_down.png style='width:25px;height:15px' onclick='expandIframe("+length+","+jsonObj.Item.ItemID+")'></div>";
      html_text+="<div id=noSellerMsg style='display:none;margin-left: auto;margin-right: auto;margin-top: 10px;width: 800px;text-align: center;overflow-x: scroll;border: 1px solid darkgrey;background-color: lightgrey;'></div>";
      html_text+="<iframe id=frame style='display:none;width: 80%;margin-left: 150px;margin-top: 20px;' scrolling=no srcdoc=\""+framedata+"\"></iframe>";
      html_text+="<div id=similar style='text-align:center;margin-top:30px;'>Click to show similar items</div>";
      html_text+="<div id=showSimilarImage style='text-align:center;margin-top:10px;'><img src=http://csci571.com/hw/hw6/images/arrow_down.png style='width:25px;height:15px' onclick='expandSimilarItems("+length+","+jsonObj.Item.ItemID+")'></div>";
      html_text+="<div id=slidingframe style='margin-left:auto;margin-right:auto;width:800px;overflow-x:scroll;border-right: 1px solid lightgrey;'></div>";
      document.getElementById('table').innerHTML=html_text;
    }
    else{
      document.getElementById('table').innerHTML="<div id=noSellerMsg style='margin-left: auto;margin-right: auto;margin-top: 30px;width: 800px;text-align: center;overflow-x: scroll;border: 1px solid grey;background-color: lightgrey;'>No Items Found</div>";
    }

    }










function collapseIframe(data,item){
      document.getElementById('noSellerMsg').style.display="none";
      document.getElementById('show').innerHTML="Click to show seller details";
      document.getElementById('showImage').innerHTML="<img src=http://csci571.com/hw/hw6/images/arrow_down.png style='width:25px;height:15px' onclick='expandIframe("+data+","+item+")'>";
      document.getElementById('frame').style.display="none";
    }





function expandIframe(data,item){
      collapseSimilarItems(data,item);
      document.getElementById('show').innerHTML="Click to hide seller details";
      document.getElementById('showImage').innerHTML="<img src=http://csci571.com/hw/hw6/images/arrow_up.png onclick='collapseIframe("+data+","+item+")' style='width:25px;height:15px'>";
      if(data>0){
      document.getElementById('frame').style.display="block";
      document.getElementById('frame').style.height=document.getElementById('frame').contentWindow.document.body.scrollHeight+20;
      }
      else{
        document.getElementById('noSellerMsg').style.display="block";
        document.getElementById('noSellerMsg').innerHTML="No Seller Message found.";
      }
    }




function generateSimilarProducts(jsonObj){
       if(jsonObj.getSimilarItemsResponse!=undefined){
         if(jsonObj.getSimilarItemsResponse.itemRecommendations!=undefined){
           if(jsonObj.getSimilarItemsResponse.itemRecommendations.item!=undefined){
                if(jsonObj.getSimilarItemsResponse.itemRecommendations.item.length!=0){
                var tabledata=jsonObj.getSimilarItemsResponse.itemRecommendations.item;
                var html_text="<table style='border-collapse: collapse;border: 1px solid lightgrey;'>";
                html_text+="<tbody>";
                html_text+="<tr style='border:none;'>";
                for(i=0;i<tabledata.length;i++){
                   html_text+="<th style='border:none;'><img style='width: 130px;height: 100px;padding-left: 40px;padding-right: 40px;padding-top: 30px;' src="+tabledata[i].imageURL+"></th>";
                }
                html_text+="</tr><tr style='border:none;'>";
                for(i=0;i<tabledata.length;i++){
                   html_text+="<th style='border:none;padding-left: 40px;padding-right: 40px;padding-top: 30px;font-weight: 500;'><a onmouseover=this.style.color='lightgrey' onmouseout=this.style.color='black' style='color: black;text-decoration: none;' onclick='goToSecondPage("+tabledata[i].itemId+")'>"+tabledata[i].title+"</a></th>";
                 }
                 html_text+="</tr><tr style='border:none;'>";
                 for(i=0;i<tabledata.length;i++){
                   html_text+="<th style='border:none;padding-left: 40px;padding-right: 40px;'> $"+tabledata[i].buyItNowPrice.__value__+"</th>";
                 }
                html_text+="</tr></tbody></table>";
                document.getElementById('slidingframe').innerHTML=html_text;
              }
              else{
                document.getElementById('slidingframe').style.border="none";
                document.getElementById('slidingframe').innerHTML="<div style='border-top: 2px solid lightgrey;border-left: 2px solid lightgrey;border-bottom: 2px solid lightgrey;border-right: 2px solid lightgrey;overflow-y: scroll;max-height: 48px;'><div style='border: 2px solid lightgrey;text-align: center;margin-top: 9px;margin-bottom: 9px;margin-left: 9px;margin-right: 9px;padding: 5px;font-weight: bold;'>No Similar Item found.</div></div>";
              }
            }
            else{
                document.getElementById('slidingframe').style.border="none";
                document.getElementById('slidingframe').innerHTML="<div style='border-top: 2px solid lightgrey;border-left: 2px solid lightgrey;border-bottom: 2px solid lightgrey;border-right: 2px solid lightgrey;overflow-y: scroll;max-height: 48px;'><div style='border: 2px solid lightgrey;text-align: center;margin-top: 9px;margin-bottom: 9px;margin-left: 9px;margin-right: 9px;padding: 5px;font-weight: bold;'>No Similar Item found.</div></div>";
              }
            }
            else{
                document.getElementById('slidingframe').style.border="none";
                document.getElementById('slidingframe').innerHTML="<div style='border-top: 2px solid lightgrey;border-left: 2px solid lightgrey;border-bottom: 2px solid lightgrey;border-right: 2px solid lightgrey;overflow-y: scroll;max-height: 48px;'><div style='border: 2px solid lightgrey;text-align: center;margin-top: 9px;margin-bottom: 9px;margin-left: 9px;margin-right: 9px;padding: 5px;font-weight: bold;'>No Similar Item found.</div></div>";
            }
       }
        else{
                document.getElementById('slidingframe').style.border="none";
                document.getElementById('slidingframe').innerHTML="<div style='border-top: 2px solid lightgrey;border-left: 2px solid lightgrey;border-bottom: 2px solid lightgrey;border-right: 2px solid lightgrey;overflow-y: scroll;max-height: 48px;'><div style='border: 2px solid lightgrey;text-align: center;margin-top: 9px;margin-bottom: 9px;margin-left: 9px;margin-right: 9px;padding: 5px;font-weight: bold;'>No Similar Item found.</div></div>";
            }

  }  





function expandSimilarItems(data,item){
      collapseIframe(data,item);
      document.getElementById('similar').innerHTML="Click to hide similar items";
      document.getElementById('showSimilarImage').innerHTML="<img src=http://csci571.com/hw/hw6/images/arrow_up.png onclick='collapseSimilarItems("+data+","+item+")' style='width:25px;height:15px;margin-bottom: 10px;'>";
      if (window.XMLHttpRequest)
     {
     // code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp=new XMLHttpRequest();
     } 
     else 
     {
     // code for IE6, IE5
     xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
      }
     xmlhttp.open("GET", "index.php?id="+item ,true);
     xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState == 4 ){
            var jsonObj = JSON.parse(xmlhttp.responseText);
            generateSimilarProducts(jsonObj);
        }
      };
      xmlhttp.send();
      document.getElementById('slidingframe').style.display="block";
    }



    function collapseSimilarItems(data,item){
      document.getElementById('similar').innerHTML="Click to show similar items";
      document.getElementById('showSimilarImage').innerHTML="<img src=http://csci571.com/hw/hw6/images/arrow_down.png style='width:25px;height:15px' onclick='expandSimilarItems("+data+","+item+")'>";
      document.getElementById('slidingframe').style.display="none";
    }




function clearForm() {
        document.getElementById('keyword').value = "";
        document.getElementById('category').value = "default";
        document.getElementById('new').checked = false;
        document.getElementById('used').checked = false;
        document.getElementById('unspecified').checked = false;
        document.getElementById('local').checked = false;
        document.getElementById('free').checked = false;
        document.getElementById('here').checked = true;
        document.getElementById('zip').checked = false;
        document.getElementById('loc').value = "";
        document.getElementById('distance').value = "10";
        document.getElementById('distance').placeholder = "10";
        document.getElementById('enable_nearby_search').checked = false;

        document.getElementById('here').disabled = true;
        document.getElementById('distance').disabled = true;
        document.getElementById('zip').disabled = true;
        document.getElementById('loc').disabled = true;

        document.getElementById('errorMsg').style.display = "none";
        document.getElementById('table').style.display = "none";
    }



    function enableOrDisableButtons(){
      if(document.getElementById('enable_nearby_search').checked==true){
        document.getElementById('here').checked=true;
        document.getElementById('here').disabled=false;
        document.getElementById('zip').checked=false;
        document.getElementById('zip').disabled=false;
        document.getElementById('distance').disabled = false;
        document.getElementById('distance').value = "10";
      }
      else
      if(document.getElementById('enable_nearby_search').checked==false)
      { 
        document.getElementById('here').checked=true;
        document.getElementById('here').disabled=true;
        document.getElementById('zip').checked=false;
        document.getElementById('zip').disabled=true;
        document.getElementById('loc').disabled=true;
        document.getElementById('distance').disabled = true;
        document.getElementById('loc').value="";
        document.getElementById('distance').value = "10";
      }
    }




   function enableLocationBox(){
     document.getElementById('loc').disabled=true;
  }



   function disableLocationBox(){
     document.getElementById('loc').disabled=false;
  }


    </script>
  <style>

  #keyword{
    box-shadow:none;
  }

   .lineBreak{
    margin-right: 9px;
        margin-left: 9px;
        border-width: thin;
        margin-top: -14px;
   }

   .formPosition{
    width: 600px;
      border: 2.5px solid lightgrey;
      margin-top: 47px;
      margin-right: auto;
      margin-left: auto;
      background-color: whitesmoke;
      }

    .formTitle{
      text-align: center;
        margin-top: -20px;
    }

    .titleWeight{
      font-weight: 500;
    }

    .alignFormElements{
      line-height: 170%;
        margin-left: 17px;
    }

    .spacing{
      padding-right: 15px;
    }

    .btnPosition{
      margin-left: 195px;
      margin-bottom: 18px;
      margin-top: 13px;
    }

    td, th {
    border: 2px solid lightgrey;
    }

    #errorMsg{
    background-color: lightgrey;
    border: 1px solid grey;
    text-align: center;
    margin-top: 30px;
    margin-left: 240px;
    margin-right: 240px;
    }
   </style>
  </head>
   <body>
   <div class="formPosition">
     <div class="formTitle"> <h1 class="titleWeight"><i>Product Sarch</i></h1> </div>
     <hr class="lineBreak">
     <form class="alignFormElements" action="" onsubmit="event.preventDefault()">
        <b style="padding-right:4px;">Keyword</b>
        <input type="text" name="keyword" id="keyword" required ><br>
        <b>Category</b>
        <select name="category" id="category">
            <option value="default" selected>All Categories</option>
            <option value="550">Art</option>
            <option value="2984">Baby</option>
            <option value="267">Books</option>
            <option value="11450">Clothing Shoes & Accessories</option>
            <option value="58058">Computers/Tablets & Networking</option>
            <option value="26395">Health & Beauty</option>
            <option value="11233">Music</option>
            <option value="1249">Video Games & Consoles</option>
        </select>
        <br>
        <b class="spacing">Condition</b>
        <input type="checkbox" id="new" name="new" value="new"><label class="spacing">New</label>
        <input type="checkbox" id="used" name="used" value="used"><label class="spacing">Used</label>
        <input type="checkbox" id="unspecified" name="unspecified" value="unspecified"><label class="spacing">Unspecified</label>
        <br>
        <b style="padding-right:30px;">Shipping Options</b>
        <input type="checkbox" id="local" name="local" value="local"><label style="padding-right:42px">Local Pickup</label>
        <input type="checkbox" id="free" name="free" value="free"><label>Free Shipping</label>
        <br>
        <input type="checkbox" id="enable_nearby_search" name="enable_nearby_search" value="enable_nearby_search" onclick="enableOrDisableButtons()"><b style="padding-right:20px;">Enable Nearby Search </b>
        <input type="text" name="distance" value="10" placeholder="10" id="distance" size="7"><b style="padding-left:5px;">miles from</b>
        <input type="radio" name="location_btn" value="here" id="here" checked onclick="enableLocationBox()">Here <br>
        <input type="radio" name="location_btn" value="zip" id="zip" style="margin-left: 333px;" onclick="disableLocationBox()" >
        <input type="text" name="location_btn" placeholder="zip code" id="loc">
        <br>
        <input class="btnPosition" type="submit" value="Search" id="submit_btn" onclick="submitForm()">
        <input type="button" value="Clear" onclick="clearForm()">
    </form>
</div>
<div id="errorMsg" style="display:none"></div>
<div id="table"></div>
</body>
</html>