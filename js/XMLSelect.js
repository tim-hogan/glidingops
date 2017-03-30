/**
 * A select backed by an XML.
 * @constructor
 */
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
        var opt = document.createElement("option");
        opt.value = "0";
        opt.text = "--";
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

    // ===========================================
    // Public interface
    // ===========================================
    self.clear = function() {
        for (var i = sel.options.length - 1; sel.options.length > 0; i--) {
            sel.remove(i);
        }
    }

    self.setXml = function(newListXML, newListTag, selectedValue) {
        self.clear()
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

var MemberSelect = function(colname, collid, selvalue, newval, options = {}) {
    var self = this;
    var xmlSelect = new XMLSelect(colname, collid, allmembers, "allmembers", selvalue, newval)
    $(xmlSelect.domNode).addClass('combo-search')

    if (options.classes) {
        $(xmlSelect.domNode).addClass(options.classes)
    }

    self.refresh = function() {
        var selectedValue = xmlSelect.value();
        xmlSelect.setXml(allmembers, "allmembers", selectedValue);
        $(xmlSelect.domNode).selectpicker('refresh')
    }

    self.addTo = function(targetDomNode) {
        targetDomNode.appendChild(xmlSelect.domNode)
        $(xmlSelect.domNode).selectpicker({
            // header: 'Put a nice header heare'
            dropupAuto: false,
            dropdownAlignRight: false,
            size: 10,
            width: '100%',
            liveSearch: true,
        })
    }
}

var LaunchOperator = function(colname, collid, xml, xmlTag, selvalue, newval, options = {}) {
    var self = this;
    var xmlSelect = new XMLSelect(colname, collid, xml, xmlTag, selvalue, newval)
    $(xmlSelect.domNode).addClass('combo-search')

    if (options.classes) {
        $(xmlSelect.domNode).addClass(options.classes)
    }

    self.setXml = function(newListXML, newListTag, selectedValue) {
        xmlSelect.setXml(newListXML, newListTag, selectedValue)
        $(xmlSelect.domNode).selectpicker('refresh')
    }

    self.clear = function() {
        xmlSelect.clear()
        $(xmlSelect.domNode).selectpicker('refresh')
    }

    self.value = function() {
        return xmlSelect.domNode.value
    }

    self.addTo = function(targetDomNode) {
        targetDomNode.appendChild(xmlSelect.domNode)
        $(xmlSelect.domNode).selectpicker({
            // header: 'Put a nice header heare'
            dropupAuto: false,
            dropdownAlignRight: false,
            size: 10,
            width: '100%',
            liveSearch: true,
        })
    }
}

var Charges = function(colname, collid, charges, options = {}) {
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

        addOptionsFromXml(charges)
    }

    function addOptionsFromXml(charges) {
        var parser = new DOMParser();
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

                    sel.appendChild(opt);
                }
            }
        }

        var parser2 = new DOMParser();
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

                sel.appendChild(opt);

            }
        }
    }

    // ===========================================
    // Public interface
    // ===========================================
    self.clear = function() {
        for (var i = sel.options.length - 1; sel.options.length > 0; i--) {
            sel.remove(i);
        }
    }

    self.onValueSelected = function(value) {
        // do nothing on select
    }

    self.value = function() {
        return sel.value
    }

    self.addTo = function(targetDomNode) {
        targetDomNode.appendChild(sel)
        $(sel).selectpicker({
            // header: 'Put a nice header heare'
            dropupAuto: false,
            dropdownAlignRight: false,
            size: 10,
            width: '100%',
            liveSearch: true,
        })
    }

    self.refresh = function() {
        var selectedValue = self.value()
        self.clear()
        addOptionsFromXml(selectedValue)
        $(sel).selectpicker('refresh')
    }

    buildSelect()
}
