// JavaScript source code
//Generic formlist JS
function deleteButtonChange(tbl) {
    var but = document.getElementById("del" + tbl);
    var l = document.getElementsByClassName('listcheck' + tbl);
    but.disabled = true;
    for (var i = 0; i < l.length; i++) {
        if (l[i].checked) {
            but.disabled = false;
        }
    }
}
//This page
function minmaxwinddow(n) {
    //Get the parenet div
    var e = parseInt(n.getAttribute("expanded"));
    var m = parseInt(n.getAttribute("minsize"));
    var p = n.parentElement;
    if (e == 1) {
        p.fullwidth = p.style.width;
        p.style.width = m + "px";
        p.style.flexBasis = m + "px";
        p.style.overflow = "hidden";
        n.setAttribute("expanded", "0");
        n.setAttribute("title", "Maximise Panel");
        n.className = "minimiser1";
    }
    else {
        p.style.width = p.fullwidth;
        p.style.flexBasis = p.fullwidth;
        p.style.overflow = "auto";
        n.setAttribute("expanded", "1");
        n.setAttribute("title", "Minimise Panel");
        n.className = "minimiser";
    }
}

function hideAllRightForms() {
    document.getElementById('formdetails').style.display = 'none';
    var l = document.getElementsByClassName('detailEntity');
    for (var i = 0; i < l.length; i++) {
        l[i].style.display = "none";
    }
}


function hidewinddow(n) {
    var p = n.parentElement;
    p.style.display = 'none';

    var l = document.getElementsByClassName('detailEntity');
    for (var i = 0; i < l.length; i++) {
        l[i].style.display = "none";
    }
}

function selectRight(n, name) {
    hideAllRightForms();
    g_pageState.select = name;
    console.log("selectRight2 g_PageState = " + JSON.stringify(g_pageState));


    var l = document.getElementsByClassName('rtEntity');
    for (var i = 0; i < l.length; i++) {
        if (l[i].id == name) {
            console.log(" Display " + name);
            l[i].style.display = "block";
        }
        else {
            l[i].style.display = "none";
        }
    }
    var l = document.getElementsByClassName('liselector');
    for (var i = 0; i < l.length; i++) {
        if (l[i] == n) {
            l[i].style.color = "#b92e02";
            l[i].style.fontWeight = "bold";
            l[i].style.paddingLeft = "0px";
            l[i].style.paddingTop = "10px";
            l[i].style.paddingBottom = "10px";
            l[i].style.fontSize = "13pt";
        }
        else {
            l[i].style.color = "";
            l[i].style.fontWeight = "";
            l[i].style.paddingLeft = "";
            l[i].style.paddingTop = "";
            l[i].style.paddingBottom = "";
            l[i].style.fontSize = "";
        }
    }
}
function start() {
    var n = document.getElementById('sel' + g_pageState.select);
    selectRight(n, g_pageState.select);
    if (g_pageState.form.display) {
        document.getElementById('formdetails').style.display = 'block';
        document.getElementById(g_pageState.select + 'form').style.display = 'block';
    }
}
