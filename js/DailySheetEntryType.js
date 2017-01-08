/**
 * A select that allows you to choose the type of entry. E.g. Tow/Self launch/Winch etc.
 * @constructor
 */
var DailySheetEntryType = function(towplanesXml, rowIndex, selected, launchTypes) {
    var self = this;

    function buildSelect() {
        var sel = document.createElement('select')
        sel.setAttribute('colname', 'launch')
        sel.onchange = function() {
            self.onValueSelected(sel.value)
            fieldchange(sel)
        }
        sel.id = 'b' + rowIndex;

        parser = new DOMParser();
        towPlaneDoc = parser.parseFromString(towplanesXml, "text/xml");
        if (null != towPlaneDoc) {
            var mems = towPlaneDoc.getElementsByTagName("TowPlanes")[0].childNodes;
            for (i = 0; i < mems.length; i++) {
                if (mems[i].nodeName == "plane") {
                    var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                    var rego = mems[i].getElementsByTagName("rego")[0].childNodes[0].nodeValue;
                    var opt = document.createElement("option");

                    opt.value = "t" + id;
                    opt.text = rego;
                    if (selected == ("t" + id)) {
                        opt.selected = true
                    }

                    sel.appendChild(opt);
                }
            }
        }

        var opt = document.createElement("option");
        var id = launchTypes.self //self.launchTypeSelfId;
        opt.value = "l" + id;
        opt.innerHTML = "SELF";
        if (selected == ("l" + id))
            opt.setAttribute("selected", "");
        sel.appendChild(opt);

        opt = document.createElement("option");
        id = launchTypes.winch // self.launchTypeWinch;
        opt.value = "l" + id;
        opt.innerHTML = "WINCH";
        if (selected == ("l" + id))
            opt.setAttribute("selected", "");
        sel.appendChild(opt);

        opt = document.createElement("option");
        opt.value = "f1";
        opt.innerHTML = "LND FEE";
        if (selected == ("f1"))
            opt.setAttribute("selected", "");
        sel.appendChild(opt);

        $(sel).addClass('autocomplete')
        return sel
    }

    // ===========================================
    // Public interface
    // ===========================================

    self.onValueSelected = function(value) {
        // do nothing on select
    }
    self.domNode = buildSelect();
}