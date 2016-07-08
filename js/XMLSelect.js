var XMLSelect = function(colname, collid, initialListXml, initialListtag, initialSelectedValue, newvalLabel) {
    var self = this;
    var sel = null;

    function buildSelect() {
        sel = document.createElement('select')
        sel.setAttribute('colname', colname)
        sel.onchange = function() {
            self.onValueSelected(sel.value)
            fieldchange(sel)
        }
        sel.id = collid;

        addOptionsFromXml(initialListXml, initialListtag, initialSelectedValue)
    }

    function addOptionsFromXml(listxml, listtag, selvalue) {
        //Create first null entry
        var opt = document.createElement("option");
        opt.value = "0";
        opt.text = "";
        sel.appendChild(opt);

        opt = document.createElement("option");
        opt.value = "99999";
        opt.text = newvalLabel;
        sel.appendChild(opt);

        parser = new DOMParser();
        dropDoc = parser.parseFromString(listxml, "text/xml");
        if (null != dropDoc) {
            var mems = dropDoc.getElementsByTagName(listtag)[0].childNodes;
            for (i = 0; i < mems.length; i++) {
                var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                opt = document.createElement("option");
                opt.value = id;
                opt.innerHTML = name;
                sel.appendChild(opt);
            }
        }

        //update the value to selected
        var optlist = sel.childNodes;
        for (i = 0; i < optlist.length; i++) {
            if (optlist[i].value == selvalue)
                optlist[i].setAttribute("selected", "");
        }
    }

    function clearOoptions() {
        for (var i = sel.options.length - 1; sel.options.length > 0; i--) {
            sel.remove(i);
        }
    }
    // ===========================================
    // Public interface
    // ===========================================

    self.setXml = function(newListXML, newListTag, selectedValue) {
        clearOoptions()
        addOptionsFromXml(newListXML, newListTag, selectedValue)
    }

    self.onValueSelected = function(value) {
        // do nothing on select
    }

    self.value = function() {
        return self.domNode.value
    }

    buildSelect()
    self.domNode = sel
}