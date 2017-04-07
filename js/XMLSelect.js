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
        var frag = document.createDocumentFragment();

        var opt = document.createElement("option");
        opt.value = "0";
        opt.text = "--";
        if (opt.value == selvalue) {
            opt.setAttribute("selected", "");
        }
        frag.appendChild(opt);

        opt = document.createElement("option");
        opt.value = "new";
        opt.text = newvalLabel;
        if (opt.value == selvalue) {
            opt.setAttribute("selected", "");
        }
        frag.appendChild(opt);

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
                if (opt.value == selvalue) {
                    opt.setAttribute("selected", "");
                }
                frag.appendChild(opt);
            }
        }

        // //update the value to selected
        // var optlist = sel.childNodes;
        // for (i = 0; i < optlist.length; i++) {
        //     if (optlist[i].value == selvalue)
        //         optlist[i].setAttribute("selected", "");
        // }

        sel.appendChild(frag);
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
            header: colname,
            dropupAuto: false,
            dropdownAlignRight: false,
            size: 10,
            width: '100%',
            liveSearch: true,
        })
    }
}
