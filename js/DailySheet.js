/**
 * A very basic implementaion of a JS module to hold all functions
 * used for setting up the Daily Sheet page.
 *
 * @module
 */
var DailySheet = function() {
    var myPublic = {};
    var launchTypes = {};

    myPublic.init = function(launchTypeSelfId, launchTypeWinchId) {
        launchTypes.self = launchTypeSelfId;
        launchTypes.winch = launchTypeWinchId;
    }

    myPublic.landbutton = function(what) {
        var stid = what.id;
        var iRow = what.id; // h rownumber
        iRow = iRow.substring(1, iRow.length);
        var n = document.getElementById("g" + iRow);
        if (n.getAttribute("timedata") != "0") {
            var parent = what.parentNode;
            parent.removeChild(what);
            var para = document.createElement("input");
            var d = new Date();
            para.setAttribute("onchange", "timechange(this)");
            para.setAttribute("timedata", d.getTime());
            para.value = pad(d.getHours(), 2) + ":" + pad(d.getMinutes(), 2);
            para.setAttribute("prevval", para.value);
            para.size = 5;
            para.id = stid;
            parent.appendChild(para);

            calcFlightTime(iRow);
            fieldchange(what);
        }
    }

    myPublic.startbutton = function(what) {
        var stid = what.id;
        var iRow = what.id; // h rownumber
        iRow = iRow.substring(1, iRow.length);
        var parent = what.parentNode;
        parent.removeChild(what);
        var para = document.createElement("input");
        para.setAttribute("onchange", "timechange(this)");
        var d = new Date();
        para.setAttribute("timedata", d.getTime());
        para.value = pad(d.getHours(), 2) + ":" + pad(d.getMinutes(), 2);
        para.setAttribute("prevval", para.value);
        para.id = stid;
        para.size = 5;
        parent.appendChild(para);

        //Get the value of P2
        var p2 = document.getElementById("f" + iRow).value;
        if (p2 == "0") {
            //check now check if k = set to P2 change to PIC
            var ch1 = document.getElementById("k" + iRow).value;
            if (ch1 == "c1") {
                var ch = document.getElementById("k" + iRow).childNodes;
                for (mm = 0; mm < ch.length; mm++) {
                    ch[mm].selected = false;
                    if (ch[mm].value == "c2")
                        ch[mm].selected = true;
                }
            }
        }

        fieldchange(what);

        //Create a new row in the table
        var nextrow = parseInt(iRow) + 1;
        var check = document.getElementById("b" + nextrow);
        if (null == check) {
            var tp = "d" + iRow;
            var strtp = document.getElementById(tp).value;
            addrowdata(nextRow, "SUG", "", strtp, "", "", "0", "0", "0", "", "", "", "0");
            nextRow++;
        }
    }

    myPublic.addrowdata = function(id, plane, glider, towy, p1, p2, start, towland, land, height, charges, comments, del) {

        console.log("Add row data plane = " + plane);
        var sel;
        var table = document.getElementById("t1");
        var row = table.insertRow(-1);

        row.insertCell(0).innerHTML = id;


        var r1 = row.insertCell(1);
        var entryTypeSelect = new DailySheetEntryType(towplanes, nextRow, plane, launchTypes)
        r1.appendChild(entryTypeSelect.domNode)

        var r2 = row.insertCell(2);
        r2.innerHTML = "<input type='text' name='glider[]' maxlength='3' size='4' class='upper' onchange='fieldchange(this)'>";
        r2.firstChild.setAttribute("id", "c" + nextRow);
        r2.firstChild.setAttribute("value", glider);

        //New towpilot code 

        var isWinch = (plane == 'l' + launchTypes.winch)
        var xml = isWinch ? winchdriverxml : towpilotxml
        var rootTag = isWinch ? 'wdrivers' : 'tpilots'

        var launchOperatorSelect = createDropDownList(row, 3, "towpilot", "d" + nextRow, xml, rootTag, towy, "new");
        createDropDownList(row, 4, "pic", "e" + nextRow, allmembers, "allmembers", p1, "new");
        createDropDownList(row, 5, "p2", "f" + nextRow, allmembers, "allmembers", p2, "Trial");

        var r6 = row.insertCell(6);
        if (parseInt(start) == 0) {
            r6.innerHTML = "<button name='start[]' type='button' onclick='DailySheet.startbutton(this)'>Start</button>";
            r6.firstChild.setAttribute("id", "g" + nextRow);
            r6.firstChild.setAttribute("timedata", "0");
        } else {
            var para = document.createElement("input");
            var d = new Date(parseInt(start));
            para.setAttribute("onchange", "timechange(this)");
            para.setAttribute("timedata", d.getTime());
            para.value = pad(d.getHours(), 2) + ":" + pad(d.getMinutes(), 2);
            para.setAttribute("prevval", para.value);
            para.size = 5;
            r6.appendChild(para);
            r6.firstChild.setAttribute("id", "g" + nextRow);
        }

        var nextCol = 7;
        //Tow charging based on time code follows
        if (towChargeType == 2) {
            var r13 = row.insertCell(nextCol);
            nextCol++;
            if (parseInt(towland) == 0) {
                r13.innerHTML = "<button name='towland[]' type='button' onclick='towlandbutton(this)'>Tow Land</button>";
                r13.firstChild.setAttribute("id", "n" + nextRow);
                r13.firstChild.setAttribute("timedata", "0");
            } else {
                var para = document.createElement("input");
                var d = new Date(parseInt(towland));
                para.setAttribute("onchange", "timechange(this)");
                para.setAttribute("timedata", d.getTime());
                para.value = pad(d.getHours(), 2) + ":" + pad(d.getMinutes(), 2);
                para.setAttribute("prevval", para.value);
                para.size = 5;
                r13.appendChild(para);
                r13.firstChild.setAttribute("id", "n" + nextRow);
            }
        }

        var r7 = row.insertCell(nextCol);
        nextCol++;
        if (parseInt(land) == 0) {
            r7.innerHTML = "<button name='land[]' type='button' onclick='DailySheet.landbutton(this)'>Land</button>";
            r7.firstChild.setAttribute("id", "h" + nextRow);
            r7.firstChild.setAttribute("timedata", "0");
        } else {
            var para = document.createElement("input");
            var d = new Date(parseInt(land));
            para.setAttribute("onchange", "timechange(this)");
            para.setAttribute("timedata", d.getTime());
            para.value = pad(d.getHours(), 2) + ":" + pad(d.getMinutes(), 2);
            para.setAttribute("prevval", para.value);
            para.size = 5;
            r7.appendChild(para);
            r7.firstChild.setAttribute("id", "h" + nextRow);
        }

        if (towChargeType == 1) {
            sel = "<select onchange='fieldchange(this)'></select>";
            var r8 = row.insertCell(nextCol);
            nextCol++;
            r8.innerHTML = sel;
            r8.firstChild.setAttribute("id", "i" + nextRow);
            var selnode = r8.firstChild;

            //Create an empty node

            var opt = document.createElement("option");
            opt.value = "0";
            opt.innerHTML = "";
            selnode.appendChild(opt);

            for (h = 500; h < 6000; h += 500) {
                opt = document.createElement("option");
                opt.value = h.toString();
                opt.innerHTML = h.toString();
                if (h == parseInt(height))
                    opt.setAttribute("selected", "");
                selnode.appendChild(opt);
            }

            opt = document.createElement("option");
            opt.value = "-1";
            opt.innerHTML = "Check Flight";
            if (-1 == parseInt(height))
                opt.setAttribute("selected", "");
            selnode.appendChild(opt);

            opt = document.createElement("option");
            opt.value = "-2";
            opt.innerHTML = "Retrieve";
            if (-2 == parseInt(height))
                opt.setAttribute("selected", "");
            selnode.appendChild(opt);
        }

        //Time fields
        //If tow time option then we need tiem for tow
        if (towChargeType == 2) {
            r14 = row.insertCell(nextCol);
            nextCol++;
            r14.id = "o" + nextRow;

            if (parseInt(start) != 0 && parseInt(towland) != 0) {
                //We need to update the flight time.
                var dest = document.getElementById("o" + nextRow);
                var e = parseInt(towland) - parseInt(start);
                mins = Math.floor((e / 60000) % 60);
                var n = document.createTextNode(pad(Math.floor(e / (3600 * 1000)), 2) + ":" + pad(mins, 2));
                dest.appendChild(n);
            }


        }

        r9 = row.insertCell(nextCol);
        nextCol++;
        r9.id = "j" + nextRow;

        if (parseInt(start) != 0 && parseInt(land) != 0) {
            //We need to update the flight time.
            var dest = document.getElementById("j" + nextRow);
            var e = parseInt(land) - parseInt(start);
            mins = Math.floor((e / 60000) % 60);
            var n = document.createTextNode(pad(Math.floor(e / (3600 * 1000)), 2) + ":" + pad(mins, 2));
            dest.appendChild(n);
        }

        r10 = row.insertCell(nextCol);
        nextCol++;
        sel = "<select colname='" + "charge" + "' onchange='fieldchange(this)'></select>";
        r10.innerHTML = sel;
        r10.firstChild.setAttribute("id", "k" + nextRow);
        var selnode = r10.firstChild;

        parser = new DOMParser();
        chargeDoc = parser.parseFromString(chargeopts, "text/xml");
        if (null != chargeDoc) {
            var mems = chargeDoc.getElementsByTagName("ChargeOpts")[0].childNodes;
            for (i = 0; i < mems.length; i++) {
                if (mems[i].nodeName == "opt") {
                    var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                    var desc = mems[i].getElementsByTagName("desc")[0].childNodes[0].nodeValue;
                    opt = document.createElement("option");

                    opt.value = "c" + id;
                    opt.innerHTML = desc;
                    if (charges == ("c" + id))
                        opt.setAttribute("selected", "");

                    selnode.appendChild(opt);
                }
            }
        }

        parser2 = new DOMParser();
        membersDoc = parser2.parseFromString(allmembers, "text/xml");
        if (null != membersDoc) {
            var mems = membersDoc.getElementsByTagName("allmembers")[0].childNodes;
            for (i = 0; i < mems.length; i++) {

                var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                opt = document.createElement("option");

                opt.value = "m" + id;
                opt.innerHTML = name;
                if (charges == ("m" + id))
                    opt.setAttribute("selected", "");

                selnode.appendChild(opt);

            }
        }

        r11 = row.insertCell(nextCol);
        nextCol++;
        r11.innerHTML = "<input type='text' name='comment[]' size='30' onchange='fieldchange(this)'>";
        r11.firstChild.setAttribute("value", unescape(comments));
        r11.firstChild.setAttribute("id", "l" + nextRow);

        r12 = row.insertCell(nextCol);
        nextCol++;
        if (del == "0")
            r12.innerHTML = "<button name='delete[]' type='button' onclick='deleteline(this)'>DELETE</button>";
        else
            r12.innerHTML = "<button name='delete[]' type='button' onclick='deleteline(this)'>UNDELETE</button>";
        r12.firstChild.setAttribute("id", "m" + nextRow);
        r12.firstChild.setAttribute("value", del);
        if (del != "0")
            greyRow(nextRow, 1);

        // Configure update events between columns
        entryTypeSelect.onValueSelected = function(value) {
            console.log('Entry type ' + value)
            if (value == 'l' + launchTypes.winch) {
                launchOperatorSelect.setXml(winchdriverxml, 'wdrivers', launchOperatorSelect.value())
            } else {
                launchOperatorSelect.setXml(towpilotxml, 'tpilots', launchOperatorSelect.value())
            }
        }
    }

    // ===========================================
    // private section
    // ===========================================

    function createDropDownList(row, colnum, colname, collid, listxml, listtag, selvalue, newval) {
        var r = row.insertCell(colnum);
        var xmlSelect = new XMLSelect(colname, collid, listxml, listtag, selvalue, newval)
        r.appendChild(xmlSelect.domNode)

        return xmlSelect
    }

    return myPublic
}()