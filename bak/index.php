<html>
<head>
<script type="text/javascript">
var xmlhttp;

function buatRq(){
            if(window.ActiveXObject)
            {
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            else
            {
                        xmlhttp = new XMLHttpRequest();
            }
			
}
function tampilKan(){
            buatRq();			
            xmlhttp.onreadystatechange = tanganiIni;
            xmlhttp.open("GET","rating.php",true);
            xmlhttp.send(null);
}
function beriRating(i,j){

            xmlhttp.onreadystatechange = tanganiIni;
            xmlhttp.open("GET","rating.php?rating="+i+"&id="+j,true);
            xmlhttp.send(null);
            alert("Terima Kasih");
}
function tanganiIni(){
            if(xmlhttp.readyState == 4){
                        if(xmlhttp.status == 200){
                                    document.getElementById("hasil").innerHTML = xmlhttp.responseText;
                                    setTimeout('tampilKan()',20000);
                        }
            }
}
function diatasRating(i,x){
            var l;
            for(l=1;l<=i;l++)
            {
                        document.getElementById(x+"_"+l).src = "ratingisi.png";
            }
}
function diatasRatin(i,x){
            var l;
            for(l=1;l<=i;l++)
            {
                        document.getElementById(x+"_"+l).src = "star_none.png";
            }
}
</script>
<body onload="tampilKan();">
<div id="hasil"></div>
</body>
</html>
- 